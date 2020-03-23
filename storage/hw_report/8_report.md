# Сравнение производительности построения отчетов

## Исходные данные
Пользователей 1,65 млн записей.
Работа в docker.

В Clickhouse создана БД на движке Mysql, чтобы сразу подцепилась существующая база из Mysql
Запрос на создание 
```sql
create database socnetwork_analyze engine=MySQL('mysql:3306', 'socnetwork', 'default', 'secret')
```

Для разминки попробовал аггрегационные запросы, в том числе с ограничением. 
Запрос на определение среднего возраста:

```
SELECT avg(dateDiff('year', birthday, now()))
FROM socnetwork_analyze.users
WHERE (dateDiff('year', birthday, now()) > 18) AND (dateDiff('year', birthday, now()) < 65)

┌─avg(dateDiff('year', birthday, now()))─┐
│                       34.6424419521067 │
└────────────────────────────────────────┘

1 rows in set. Elapsed: 2.997 sec. Processed 1.65 million rows, 3.30 MB (550.37 thousand rows/s., 1.10 MB/s.) 

```
Аналогичный запрос на Mysql выполнялся 4,5 сек. 

Запрос на получение распределения людей по возрасту:
```sql
SELECT 
    dateDiff('year', birthday, now()), 
    (100 * count(dateDiff('year', birthday, now()))) / 
    (
        SELECT count(*)
        FROM socnetwork_analyze.users
    ) AS percent
FROM socnetwork_analyze.users
GROUP BY dateDiff('year', birthday, now())
ORDER BY dateDiff('year', birthday, now()) ASC

```
Время выполнения запроса составило 5,8 секунд. 

Аналогичный запрос на mysql выполнялся 7 секунд. 

Запрос на распределение по полу:
```
SELECT 
    gender, 
    (100 * count(gender)) / 
    (
        SELECT count(*)
        FROM socnetwork_analyze.users
    ) AS percent
FROM socnetwork_analyze.users
GROUP BY gender

┌─gender─┬───────────percent─┐
│      1 │ 49.94892394730285 │
│      2 │ 50.05107605269715 │
└────────┴───────────────────┘

2 rows in set. Elapsed: 5.959 sec. Processed 1.65 million rows, 1.65 MB (276.80 thousand rows/s., 276.80 KB/s.) 

```
Аналогичный запрос по mysql занял 4,5 секунд.  Выигрыш в mysql предполагаю из-за enum, не селективного индекса и, возможно, не самого оптимального запроса в clickhouse.
Дополнительно я сделал выборку по распределению пользователей по городам. 
Запрос на clickhouse занял 5,8 секунды, а аналогичный на mysql занял 7 секунд.  
 
