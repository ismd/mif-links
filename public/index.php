<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../public/'));

set_include_path(implode(':', [
    realpath(APPLICATION_PATH . '/../lib'),
    get_include_path(),
]));

// Инициализируем систему
require 'php-spa/startup.php';
