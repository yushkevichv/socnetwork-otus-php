# Результаты нагрузочного тестирования до / после репликации

## Входные данные

Существуют таблица пользователей, куда постоянно пишутся записи (1 млн записей).
В процессе записи проводится нагрузочное тестирование запросов на чтение по выборке пользователей с условием (условия аналогичные предыдущему заданию).
После настраивается ассинхронная репликация и повторяются входные данные.

Тестирование производится на 2 независимых endpoint (резульаты замеров учитываются раздельно). 
1- в рамках фреймворка Laravel без использования Eloquent (raw запросы через query builder с гидратацией в объекты). 2- vanila php код. 

Настройки сервера (nginx в частности) использовались стандартные, не кастомизировались. 

Так как замеры предыдущей работы делались в локальном докере, а задача с репликацией будет делаться на сервере, для сравнения чтения с паралельной нагрузкой записи и без, будут продублированы замеры в докере, но итогу будут зафиксированы только в выводы. 
 
## До реплики

### Тестирование запроса c фильтрацией
#### Запросы на фреймворк

wrk -t1 -c1 -d30s --latency https://otus-php.pugofka.com/users?q=%D0%BC%D0%B0%D0%BA 
```
Latency   97.90ms
Latency Distribution
     75%   96.27ms
     90%  122.72ms
     99%  241.93ms
308 requests in 30.03s, 9.89MB read

Requests/sec:     10.26
```

wrk -t10 -c10 -d30s --latency https://otus-php.pugofka.com/users?q=%D0%BC%D0%B0%D0%BA 
```
Latency   555.41ms
Latency Distribution
     75%  565.65ms
     90%  665.90ms
     99%  912.13ms
532 requests in 30.10s, 17.08MB read

Requests/sec:     17.68
```

wrk -t10 -c100 -d30s --latency https://otus-php.pugofka.com/users?q=%D0%BC%D0%B0%D0%BA 
```
Latency   1.34s
Latency Distribution
     75%    1.89s 
     90%    1.94s 
     99%    1.94s 
351 requests in 30.09s, 11.27MB read
Socket errors: connect 0, read 0, write 0, timeout 331

Requests/sec:     11.66
```

wrk -t10 -c1000 -d30s --latency https://otus-php.pugofka.com/users?q=%D0%BC%D0%B0%D0%BA 
```
Latency   45.97ms
Latency Distribution
     75%   47.28ms
     90%   51.16ms
     99%   68.20ms
56223 requests in 30.06s, 25.20MB read
Socket errors: connect 759, read 0, write 0, timeout 220
Non-2xx or 3xx responses: 55985

Requests/sec:   1870.19
```


#### Промежуточные выводы

На небольшой загрузке наблюдается повышенная проихводительность, относительно запросов без одновременной записи. 
Это легко объясняется, если смотреть на график загрузки сервера различными приложениями (php и mysql) - при работе через фреймворк основное время работа ввыполняется фреймвворком, чем объясняется низкая производительность скрипта (относительно чистого кода) и незначительные корреляции при повышении одновременной нагрузке на базу через другую точку входа (запись в базу происходила через консольный скрипт). 
Эти запросы не приводят к значительному увеличению нагрузки на БД, поэтому latency и througput отличаются на уровне погрешности. 
Когда же нагрузка становится более критичной для сервера, то начинают чувствоваться "тормоза" базы данных, в связи с чем, на высокой нагрузке видна более существенная просадка производительности.   



#### Запросы на vanilla php

wrk -t1 -c1 -d30s --latency https://otus-php.pugofka.com/hw2_vanila.php?q=%D0%BC%D0%B0%D0%BA 
```
Latency   51.61ms
Latency Distribution
     75%   53.72ms
     90%   56.78ms
     99%   79.78ms
573 requests in 30.07s, 8.18MB read

Requests/sec:     19.06
```

wrk -t10 -c10 -d30s --latency https://otus-php.pugofka.com/hw2_vanila.php?q=%D0%BC%D0%B0%D0%BA 
```
Latency   223.72ms
Latency Distribution
     75%  252.41ms
     90%  274.04ms
     99%  327.61ms
1317 requests in 30.09s, 18.81MB read

Requests/sec:     43.77
```

wrk -t10 -c100 -d30s --latency https://otus-php.pugofka.com/hw2_vanila.php?q=%D0%BC%D0%B0%D0%BA 
```
Latency   1.33s
Latency Distribution
     75%    1.62s 
     90%    1.82s 
     99%    1.99s 
1275 requests in 30.10s, 18.21MB read
Socket errors: connect 0, read 0, write 0, timeout 1186

Requests/sec:     42.36
```

wrk -t10 -c1000 -d30s --latency https://otus-php.pugofka.com/hw2_vanila.php?q=%D0%BC%D0%B0%D0%BA 
```
Latency   85.40ms
Latency Distribution
     75%   84.35ms
     90%   89.56ms
     99%  371.83ms
22695 requests in 30.09s, 20.36MB read
Socket errors: connect 759, read 0, write 0, timeout 874
Non-2xx or 3xx responses: 21747

Requests/sec:   754.23
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

Добавил результаты с графиками сравнений [в свобдную таблицу](https://docs.google.com/spreadsheets/d/1tmdPo_m1PM5rNO4cO1JB38WA0T_1Yz8xqy2GRhhwKYs/edit?usp=sharing)
