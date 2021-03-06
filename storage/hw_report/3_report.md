# Результаты нагрузочного тестирования до / после репликации

## Входные данные

Существуют таблица пользователей, куда постоянно пишутся записи (1 млн записей).
В процессе записи проводится нагрузочное тестирование запросов на чтение по выборке пользователей с условием (условия аналогичные предыдущему заданию).
После настраивается ассинхронная репликация и повторяются входные данные.

Тестирование производится на 2 независимых endpoint (резульаты замеров учитываются раздельно). 
1- в рамках фреймворка Laravel без использования Eloquent (raw запросы через query builder с гидратацией в объекты). 2- vanila php код. 

Настройки сервера (nginx в частности) использовались стандартные, не кастомизировались. 

Так как замеры предыдущей работы делались в локальном докере, а задача с репликацией будет делаться на сервере, для сравнения чтения с паралельной нагрузкой записи и без, будут продублированы замеры в докере, но итогу будут зафиксированы только в выводы. 
 
Slave база данных находится на другом сервере (в другом географическом регионе и датацентре), что вносит дополнительный оверхед. 

## Результаты измерения

Результаты с графиками доступны [в свобдной таблице](https://docs.google.com/spreadsheets/d/1tmdPo_m1PM5rNO4cO1JB38WA0T_1Yz8xqy2GRhhwKYs/edit?usp=sharing)


## Выводы

При одновременной записи и чтении на одну базу наблюдается существенный рост нагрузки и более раннее "захлебывание" базы данных. 
Так как при тестировании фреймворка laravel все упирается в процессор и воркеры php-fpm, то повышенная нагрузка на БД не оказывает столь существенной деградации производительности системы.
Для тестирования чистого кода php, где влияние БД оказывает более существенное влияние на общую производительность, повышенная нагрузка (одновременное чтение и запись) приводят к существнной деградаци производительности (в 2-3 раза). 

При тестировании одновременной нагрузки записи и чтения с репликацией, наблюдалась существенно более низкая нагрузка на подсистемы сервера (общая нагрузка обоих серверов не превышала 50% по CPU и памяти).
Но общая производительность (latency и throughput) оказалассь ниже. Это объясняется удаленным расположением БД относительно приложения и, как следствие, большими потерями на постоянную установку соединения (особенность работы php). 
Эти потери должны быть существенно минимизированы при использовании постоянного соединения для разных независимых запросов (roadrunner, cli), либо на других языках (go).        
