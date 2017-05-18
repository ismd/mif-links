Укорачиватель ссылок с админ-панелью и отображением статистики

### Требования
- PHP 5.7 (mysqli, bcmath)
- MySQL (MariaDB)
- nginx
- node.js 6.10.3
- npm 3.10.10

### Установка
Загрузить исходники либо склонировать репозиторий  
`git clone https://github.com/ismd/mif-links.git`
###### Конфигурация
Скопировать файл `application/configs/application.example.ini` в `application/configs/application.ini` и отредактировать его
###### nginx
Создать конфиг на основе `doc/nginx.conf`
###### MySQL
- Создать базу данных:  
`CREATE DATABASE <имя базы> DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;`  
- Создать пользователя и выдать права:  
`GRANT ALL ON <имя базы>.* TO '<имя пользователя>'@'localhost' IDENTIFIED BY '<пароль>';`  
- Выполнить скрипт  
`/usr/bin/php doc/init_db.php`  
###### npm-пакеты
`npm install`
###### Сборка js/less
`cd js && NODE_ENV=production node_modules/.bin/gulp`
###### Использование
В браузере перейти по ссылке `<имя домена>/admin`
