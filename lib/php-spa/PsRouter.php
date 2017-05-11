<?php

/**
 * Роутер
 *
 * Запуск:
 * $router->delegate()
 *
 * Получение имени контроллера:
 * $router->getController()
 *
 * Получение имени действия:
 * $router->getAction()
 *
 * Получение переданных аргументов
 * $router->getArgs()
 *
 * Пример запроса: <префикс>/<контроллер>/<действие>/<арг.1>/<арг.2>/...
 *
 * @author ismd
 */
class PsRouter {

    const INDEX_REQUEST   = 1;
    const PARTIAL_REQUEST = 2;
    const ACTION_REQUEST  = 3;
    const NON_SPA         = 4;

    const ERROR_CONTROLLER = 'Error';
    const ERROR_ACTION     = 'index';

    private $_registry;
    private $_route;

    /**
     * Возможные префиксы
     * @var string[]
     */
    private $_prefixes;

    /**
     * Префикс запроса
     * @var string
     */
    private $_prefix;

    /**
     * Контроллер
     * @var string
     */
    private $_controller;

    /**
     * Действие
     * @var string
     */
    private $_action;

    /**
     * Аргументы запроса
     * @var mixed[]
     */
    private $_args = [];

    public function __construct($registry, $route) {
        $this->_registry = $registry;
        $this->_route    = trim($route, '/');

        $urlPrefixed = PsConfig::getInstance()->url_prefixes->toArray();
        if (!empty($urlPrefixed)) {
            $this->_prefixes = $urlPrefixed;
        }
    }

    /**
     * Подключаем нужный контроллер, модель и выполняем действие
     * @throws Exception
     */
    public function delegate() {
        // Анализируем путь
        try {
            $this->parseRoute();
        } catch (Exception $e) {
            $this->showErrorPage();
            return;
        }

        // Подключаем контроллер
        try {
            $controller = $this->includeController($this->_controller);
        } catch (Exception $e) {
            $this->showErrorPage();
            return;
        }

        // Определяем вызываемый метод
        $action = $this->_action;

        $requestType = $this->getRequestType();
        switch ($requestType) {
            case self::PARTIAL_REQUEST:
                $action .= 'Partial';
                break;

            default:
                $action .= 'Action';
                break;
        }

        // Если действие недоступно
        if (!is_callable(array($controller, $action))) {
            $this->showErrorPage();
            return;
        }

        // Инициализируем контроллер, если надо
        if (is_callable(array($controller, 'init'))) {
            $controller->init();
        }

        // Выполняем действие
        try {
            $controller->$action();
        } catch (ActionFinishedException $e) {
        } catch (Exception $e) {
            $this->showErrorPage();
        }
    }

    /**
     * Подключает контроллер
     * @param string $controller
     * @return PsController
     * @throws Exception
     */
    private function includeController($controller) {
        $controller .=  'Controller';

        // Путь к директории с контроллерами
        $controllersPath = APPLICATION_PATH . '/controllers/';

        // Путь к контроллеру
        $controllerFile = $controllersPath . $controller . '.php';

        // Если недоступен файл контроллера
        if (!is_readable($controllerFile)) {
            throw new Exception('Controller not found');
        }

        // Подключаем контроллер
        require_once $controllerFile;

        // Создаём экземпляр контроллера
        return new $controller($this->_registry);
    }

    /**
     * Отображает страницу ошибки
     * @throws Exception
     */
    private function showErrorPage() {
        $error = PsConfig::getInstance()->error;

        $this->_controller = !is_null($error->controller) ? ucfirst($error->controller) : self::ERROR_CONTROLLER;
        $this->_action     = !is_null($error->action)     ? $error->action              : self::ERROR_ACTION;

        $controller = $this->includeController($this->_controller);
        $action     = $this->_action . 'Action';

        // Если действие недоступно
        if (!is_callable(array($controller, $action))) {
            throw new Exception('Action not found');
        }

        // Инициализируем контроллер, если надо
        if (is_callable(array($controller, 'init'))) {
            $controller->init();
        }

        // Выполняем действие
        try {
            $controller->$action();
        } catch (ActionFinishedException $e) {
        }
    }

    /**
     * Определяет контроллер, действие и аргументы
     * Устанавливает свойства _controller, _action и _args
     * @throws Exception
     */
    private function parseRoute() {
        $route = explode('/', $this->_route);

        // Префикс
        if (!is_null($this->_prefixes) && (count($route) > 1 || count($route) == 1 && !empty($route[0]))) {
            $this->_prefix = $route[0];

            if (!in_array($this->_prefix, $this->_prefixes)) {
                throw new Exception('Bad route');
            }

            $controllerId = 1;
        } else {
            $controllerId = 0;
        }

        // Контроллер
        $this->_controller = '' != $route[$controllerId] ? ucfirst(strtolower($route[$controllerId])) : 'Index';

        // Действие
        if (count($route) > $controllerId + 1) {
            $actionExplode = explode('-', str_replace('_', '-', $route[$controllerId + 1]));
            $this->_action = $actionExplode[0]
                . implode(array_map(function($val) {
                    return ucfirst($val);
                }, array_slice($actionExplode, 1)));
        } else {
            $this->_action = 'index';
        }

        // Аргументы
        $this->_args = array_slice($route, $controllerId + 2);
    }

    /**
     * Возвращает контроллер
     * @return string
     */
    public function getController() {
        return $this->_controller;
    }

    /**
     * Возвращает действие
     * @return string
     */
    public function getAction() {
        return $this->_action;
    }

    /**
     * Возвращает аргументы, переданные в url
     * @return mixed[]
     */
    public function getArgs() {
        return $this->_args;
    }

    /**
     * Возвращает тип запрошенной страницы
     * Возможные варианты:
     * - PsRouter::INDEX_REQUEST
     * - PsRouter::PARTIAL_REQUEST
     * - PsRouter::ACTION_REQUEST
     * @return int
     */
    public function getRequestType() {
        if (is_null($this->_prefixes)) {
            return '' == $this->_route ? self::INDEX_REQUEST : self::NON_SPA;
        }

        if ($this->_prefix == $this->_prefixes['partial']) {
            return self::PARTIAL_REQUEST;
        } elseif ($this->_prefix == $this->_prefixes['action']) {
            return self::ACTION_REQUEST;
        } else {
            return self::INDEX_REQUEST;
        }
    }
}

/**
 * Это исключение завершает работу действия (action)
 */
class ActionFinishedException extends Exception {
}
