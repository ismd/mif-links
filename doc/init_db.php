<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../public/'));

set_include_path(implode(':', [
    realpath(APPLICATION_PATH . '/../lib'),
    get_include_path(),
]));

require_once 'php-spa/common.php';

try {
    $db = PsDb::getInstance()->db;
} catch (Exception $e) {
    die($e->getMessage());
}

$sql = file_get_contents(realpath(APPLICATION_PATH . '/../doc/structure.sql'));
$db->multi_query($sql);
