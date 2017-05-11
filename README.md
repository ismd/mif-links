Укорачиватель ссылок с админ-панелью и отображением статистики

### Требования
- PHP 5.7
- MySQL (MariaDB)
- nginx
- node.js 6.10.3
- npm 3.10.10

### Установка
Загрузить исходники либо склонировать репозиторий  
`git clone https://github.com/ismd/mif-links.git`
###### Конфиг nginx
```
server {
    server_name <домен>;
    charset utf-8;
    root <путь к директории public в загруженном проекте>;

    location / {
        try_files $uri /index.php?route=$uri;
    }

    location ~ \.php$ {
        fastcgi_pass <php-fpm сокет>;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
}
```
###### MySQL
Создать базу данных и выполнить скрипт `doc/structure.sql`
###### Конфигурация
Скопировать и отредактировать файл `application/configs/application.example.ini` в `application/configs/application.ini`
###### npm-пакеты
`npm install`
###### Сборка js/less
`cd js && NODE_ENV=production node_modules/.bin/gulp`
###### Использование
В браузере перейти по ссылке `<имя домена>/admin`
