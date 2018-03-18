Укорачиватель ссылок с админ-панелью и отображением статистики

### Требования
- PHP 7.0 (mysqli, bcmath)
- MySQL (MariaDB)
- nginx
- node.js 6.11
- npm 3.10

### Установка
Загрузить исходники либо склонировать репозиторий
`git clone https://github.com/ismd/mif-links.git`
###### Конфигурация
Скопировать файл `application/configs/application.example.ini` в `application/configs/application.ini` и отредактировать его
###### PHP
Включить расширения:  
`extension=bcmath`  
`extension=mysqli`
###### nginx
Создать конфиг на основе `doc/nginx.conf`
###### npm-пакеты
`npm install`
###### MySQL
- Создать базу данных:
`CREATE DATABASE <имя базы> DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;`
- Создать пользователя и выдать права:
`GRANT ALL ON <имя базы>.* TO '<имя пользователя>'@'localhost' IDENTIFIED BY '<пароль>';`
- Скопировать файл `doc/database.example.json` в `doc/database.json` и отредактировать его
- Выполнить миграции
`cd doc && node node_modules/db-migrate/bin/db-migrate up`
###### Сборка js/less
`NODE_ENV=production npm run build` production-режим  
`NODE_ENV=development npm run build` development-режим
###### Использование
В браузере перейти по ссылке `<имя домена>/admin`
