# Результаты нагрузочного тестирования

## Входные данные

1 млн записей пользователей. 
1 запрос в БД на получение списка данных с и без фильтрацией по префиксному поиску like "query%" с ограничением лимита на отображение данных (50 элементов).  
Тестирование производилось локально, проект поднят в doker с использованием настроек по умолчанию laradock.

## До добавления индексов

### Тестирование запроса без фильтрации

 wrk -t1 -c1 -d30s http://localhost/users
 ```
Latency   353.01ms
61 requests in 30.08s, 1.57MB read
Socket errors: connect 0, read 0, write 0, timeout 1

Requests/sec:      2.03
Transfer/sec:     53.39KB
```

wrk -t1 -c10 -d30s http://localhost/users
 ```
Latency   831.73ms
357 requests in 30.00s, 9.18MB rea

Requests/sec:     11.90
Transfer/sec:    313.20KB
```

wrk -t10 -c10 -d30s http://localhost/users
 ```
Latency   840.99ms
352 requests in 30.09s, 9.05MB read

Requests/sec:     11.70
Transfer/sec:    307.94KB
```

wrk -t10 -c100 -d30s http://localhost/users
 ```
Latency   992.14ms
304 requests in 30.09s, 7.81MB read
Socket errors: connect 0, read 0, write 0, timeout 288

Requests/sec:     10.10
Transfer/sec:    265.95KB
```

wrk -t10 -c1000 -d30s http://localhost/users
 ```
Latency   1.11s
353 requests in 30.10s, 8.49MB read
Socket errors: connect 758, read 127, write 3, timeout 334
Non-2xx or 3xx responses: 23

Requests/sec:     11.73
Transfer/sec:    288.83KB
```

#### Промежуточные выводы
Пропускная способность приложения (возможны не оптимальны серверные настройки) составляет около 10-11 запросов / секунду.
Если увеличивать нагрузку, то возникает больше ошибок с подключением и обработкой сокетов. 
Среднее Latency под нагрузкой составляет около 840ms.

### Тестирование запроса c фильтрацией
Для запроса с фильтрацией был выбран запрос "ser". Он давал высокую селективность данных на сгенерированном наборе данных (487 найденных результатов из 1 млн)

wrk -t1 -c1 -d30s  http://localhost/users?q=ser
```
Latency   487.13ms
63 requests in 30.10s, 1.63MB read

Requests/sec:      2.09
```

wrk -t1 -c10 -d30s  http://localhost/users?q=ser
```
Latency   1.21s
234 requests in 30.00s, 6.06MB read
Socket errors: connect 0, read 0, write 0, timeout 4

Requests/sec:      7.80
```

wrk -t10 -c100 -d30s  http://localhost/users?q=ser
```
Latency   1.14s
243 requests in 30.09s, 6.29MB read
Socket errors: connect 0, read 0, write 0, timeout 228

Requests/sec:      8.08
```

wrk -t10 -c1000 -d30s  http://localhost/users?q=ser
```
Latency   1.13s
265 requests in 30.09s, 6.48MB read
Socket errors: connect 758, read 175, write 0, timeout 251
Non-2xx or 3xx responses: 15

Requests/sec:      8.81
```

#### Промежуточные выводы
Пропускная способность приложения (возможны не оптимальны серверные настройки) составляет около 8 запросов / секунду.
При увеличении нагрузки система ведет себя аналогично запросу без фильтрации. 
Среднее Latency под нагрузкой составляет около 1.1ms, что ~ на 30% медленнее без фильтрации.

Дополнительно был проведен эксперимент без гидратации моделей (работа с чистыми массивами).
В этом случае средний throughput составил: 13 req/s; Latency: 1.07s (почему latency почти не изменился?)

На этих же условиях работа с Laravel Eloquent дало не сильное отклонение:
wrk -t10 -c100 -d30s  http://localhost/users?q=ser
```
Latency   1.18s
240 requests in 30.10s, 6.21MB read
Socket errors: connect 0, read 0, write 0, timeout 226

Requests/sec:      7.97
```
 

wrk -t10 -c1000 -d30s  http://localhost/users?q=ser
```
Latency   969.58ms
232 requests in 30.09s, 5.09MB read
Socket errors: connect 758, read 102, write 1, timeout 222
Non-2xx or 3xx responses: 36

Requests/sec:      7.71
```

## Добавление индексов

Изначальный запрос в БД работал по условию с ИЛИ (select my_data from users where name like query% or last_name like query%).
Его Explain после индекса:
```json
{
  "query_block": {
    "select_id": 1,
    "cost_info": {
      "query_cost": "26921.98"
    },
    "ordering_operation": {
      "using_filesort": true,
      "table": {
        "table_name": "users",
        "access_type": "index_merge",
        "possible_keys": [
          "name_index",
          "last_name_index"
        ],
        "key": "sort_union(name_index,last_name_index)",
        "key_length": "22,22",
        "rows_examined_per_scan": 10253,
        "rows_produced_per_join": 10253,
        "filtered": "100.00",
        "cost_info": {
          "read_cost": "24871.38",
          "eval_cost": "2050.60",
          "prefix_cost": "26921.98",
          "data_read_per_join": "64M"
        },
        "used_columns": [
          "id",
          "name",
          "last_name",
          "birthday",
          "gender",
          "city"
        ],
        "attached_condition": "((`socnetwork`.`users`.`name` like 'ab%') or (`socnetwork`.`users`.`last_name` like 'ab%'))"
      }
    }
  }
} 
```

Данный запрос работал весьма эффективно (без индексов), но без переписывания запроса было сложно улучшить его производительность. 
Для оптимизации я добавил индексы на первые символы. 
Для определения количества символов я проверял селективность выборок, чтобы она была небольшой и равномерной. 
```
SELECT (COUNT(*)/10000) AS cnt, LEFT(last_name, 5) AS pref FROM users GROUP BY pref ORDER BY cnt DESC LIMIT 3
```
Для 5 символов last_name я получил равномерную селективность порядка 0,6%.
Для столбца name я взял 7 символов из-за особенности тестовых данных (префикс Prof., дающий низкую селективность на меньшей длине ключа). 
  
Также я переписал запрос на следующий:
```
explain select * from 
(
    select * from 
    (
        select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users where name like 'ab%' order by id asc limit  0, 50
    ) t1
    union 
    select * from 
    (
    	select id, name, last_name, gender, city, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age  from users where last_name like 'ab%' order by id asc limit  0, 50
    ) t2
   
) tbl order by id asc limit  0, 50
```
Его explain:
```json
{
  "query_block": {
    "select_id": 1,
    "cost_info": {
      "query_cost": "35.00"
    },
    "ordering_operation": {
      "using_filesort": true,
      "table": {
        "table_name": "tbl",
        "access_type": "ALL",
        "rows_examined_per_scan": 100,
        "rows_produced_per_join": 100,
        "filtered": "100.00",
        "cost_info": {
          "read_cost": "15.00",
          "eval_cost": "20.00",
          "prefix_cost": "35.00",
          "data_read_per_join": "302K"
        },
        "used_columns": [
          "id",
          "name",
          "last_name",
          "gender",
          "city",
          "age"
        ],
        "materialized_from_subquery": {
          "using_temporary_table": true,
          "dependent": false,
          "cacheable": true,
          "query_block": {
            "union_result": {
              "using_temporary_table": true,
              "table_name": "<union2,4>",
              "access_type": "ALL",
              "query_specifications": [
                {
                  "dependent": false,
                  "cacheable": true,
                  "query_block": {
                    "select_id": 2,
                    "cost_info": {
                      "query_cost": "22.50"
                    },
                    "table": {
                      "table_name": "t1",
                      "access_type": "ALL",
                      "rows_examined_per_scan": 50,
                      "rows_produced_per_join": 50,
                      "filtered": "100.00",
                      "cost_info": {
                        "read_cost": "12.50",
                        "eval_cost": "10.00",
                        "prefix_cost": "22.50",
                        "data_read_per_join": "151K"
                      },
                      "used_columns": [
                        "id",
                        "name",
                        "last_name",
                        "gender",
                        "city",
                        "age"
                      ],
                      "materialized_from_subquery": {
                        "using_temporary_table": true,
                        "dependent": false,
                        "cacheable": true,
                        "query_block": {
                          "select_id": 3,
                          "cost_info": {
                            "query_cost": "5463.81"
                          },
                          "ordering_operation": {
                            "using_filesort": true,
                            "table": {
                              "table_name": "users",
                              "access_type": "range",
                              "possible_keys": [
                                "name_index"
                              ],
                              "key": "name_index",
                              "used_key_parts": [
                                "name"
                              ],
                              "key_length": "22",
                              "rows_examined_per_scan": 3902,
                              "rows_produced_per_join": 3902,
                              "filtered": "100.00",
                              "cost_info": {
                                "read_cost": "4683.41",
                                "eval_cost": "780.40",
                                "prefix_cost": "5463.81",
                                "data_read_per_join": "24M"
                              },
                              "used_columns": [
                                "id",
                                "name",
                                "last_name",
                                "birthday",
                                "gender",
                                "city"
                              ],
                              "attached_condition": "(`socnetwork`.`users`.`name` like 'ab%')"
                            }
                          }
                        }
                      }
                    }
                  }
                },
                {
                  "dependent": false,
                  "cacheable": true,
                  "query_block": {
                    "select_id": 4,
                    "cost_info": {
                      "query_cost": "22.50"
                    },
                    "table": {
                      "table_name": "t2",
                      "access_type": "ALL",
                      "rows_examined_per_scan": 50,
                      "rows_produced_per_join": 50,
                      "filtered": "100.00",
                      "cost_info": {
                        "read_cost": "12.50",
                        "eval_cost": "10.00",
                        "prefix_cost": "22.50",
                        "data_read_per_join": "151K"
                      },
                      "used_columns": [
                        "id",
                        "name",
                        "last_name",
                        "gender",
                        "city",
                        "age"
                      ],
                      "materialized_from_subquery": {
                        "using_temporary_table": true,
                        "dependent": false,
                        "cacheable": true,
                        "query_block": {
                          "select_id": 5,
                          "cost_info": {
                            "query_cost": "8892.41"
                          },
                          "ordering_operation": {
                            "using_filesort": true,
                            "table": {
                              "table_name": "users",
                              "access_type": "range",
                              "possible_keys": [
                                "last_name_index"
                              ],
                              "key": "last_name_index",
                              "used_key_parts": [
                                "last_name"
                              ],
                              "key_length": "22",
                              "rows_examined_per_scan": 6351,
                              "rows_produced_per_join": 6351,
                              "filtered": "100.00",
                              "cost_info": {
                                "read_cost": "7622.21",
                                "eval_cost": "1270.20",
                                "prefix_cost": "8892.41",
                                "data_read_per_join": "39M"
                              },
                              "used_columns": [
                                "id",
                                "name",
                                "last_name",
                                "birthday",
                                "gender",
                                "city"
                              ],
                              "attached_condition": "(`socnetwork`.`users`.`last_name` like 'ab%')"
                            }
                          }
                        }
                      }
                    }
                  }
                }
              ]
            }
          }
        }
      }
    }
  }
} 
```

Если оценивать изменение параметра query_cost, то он уменьшился почти в 2 раза. 
  
### Тестирование запроса c фильтрацией после добавления индекса

wrk -t1 -c1 -d30s  http://localhost/users?q=ser
```
Latency   353.63ms
86 requests in 30.10s, 2.22MB read

Requests/sec:      2.86
```

wrk -t1 -c10 -d30s  http://localhost/users?q=ser
```
Latency   916.67m
315 requests in 30.02s, 8.13MB read
Socket errors: connect 0, read 0, write 0, timeout 4

Requests/sec:     10.49
```

wrk -t10 -c100 -d30s  http://localhost/users?q=ser
```
Latency  1.07s 
309 requests in 30.10s, 7.98MB read
Socket errors: connect 0, read 0, write 0, timeout 293

Requests/sec:     10.26
```
wrk -t10 -c1000 -d30s  http://localhost/users?q=ser
```
Latency 1.32s
330 requests in 30.10s, 7.96MB read
Socket errors: connect 758, read 131, write 0, timeout 319
Non-2xx or 3xx responses: 22

Requests/sec:     10.96
```

### Промежуточные выводы

Запрос через union без добавления индексов работал на несколько порядков медленее. Но после добавления индексов он начал работать быстрее запроса через or.
Общий прирост производительности по latency и throughput составил порядка 15-20%.
Решение на vanilla php существенно быстрее (набросал только прототип (public/hw2_vanila.php) для сравнения). 
Latency ~ 35ms с малой нагрузкой, 550ms с перегрузом. 
Throughput 260-290 req/s. 

Сравнил разные способы работы с БД. 
Если использовать буферизированный выввод, то latency немного выше, но пропускная способность меньше.  Отличия порядка 5-10%. 
