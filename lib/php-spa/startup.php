<?php
require_once 'common.php';

// PsRegistry, в котором будем хранить глобальные значения
$registry = new PsRegistry;

// Загружаем router
$registry->router = new PsRouter($registry, isset($_GET['route']) ? $_GET['route'] : '');

// Загружаем класс для работы с шаблонами
$registry->view = new PsView($registry);

// Выбираем нужный контроллер, определяем действие и выполняем
$registry->router->delegate();

// Отображаем вывод
try {
    $registry->view->render();
} catch (ActionFinishedException $e) {
}
