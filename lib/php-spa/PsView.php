<?php

/**
 * Класс для работы с шаблонами
 *
 * Передача в шаблон переменной test со значением 'test':
 * $view->test = 'test'
 *
 * Обращаться к переменным в шаблоне следующим образом:
 * $this->test
 *
 * @author ismd
 */
class PsView extends PsObject {

    protected $_registry;

    /**
     * Флаг вывода
     * @var boolean
     */
    private $_rendered = false;

    /**
     * JSON-данные для вывода при запросе действия
     * @var mixed[]
     */
    protected $_json = null;

    /**
     * Meta тэги
     * @var mixed[]
     */
    protected $_meta = [];

    /**
     * Имя шаблона
     * @var string
     */
    protected $_partial;

    /**
     * Имя layout'а
     */
    protected $_layout = 'layout';

    public function __construct($registry) {
        $this->_registry = $registry;
    }

    /**
     * Отображает страницу
     * @param string $partial Если передан параметр, то выводим заданный шаблон
     * @throws ActionFinishedException
     * @throws Exception
     */
    public function render($partial = null) {
        if ($this->_rendered) {
            return;
        }

        $this->_partial = $partial;

        if (is_null($partial)) {
            switch ($this->_registry->router->getRequestType()) {
                case PsRouter::PARTIAL_REQUEST:
                    header('Content-Type: text/html; charset=utf-8');
                    $this->content();
                    return;

                case PsRouter::ACTION_REQUEST:
                    if (is_null($this->_json)) {
                        header('Content-Type: text/html; charset=utf-8');
                        http_response_code(404);
                        $this->content();
                        return;
                    }

                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($this->_json);
                    return;

                case PsRouter::NON_SPA:
                    if (!empty($this->_json)) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($this->_json);
                        return;
                    }
            }

            if (!empty($this->_json)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($this->_json);
                return;
            }
        }

        // Отображаем главную страницу
        header('Content-Type: text/html; charset=utf-8');
        $filename = APPLICATION_PATH . '/views/' . $this->_layout . '.phtml';

        if (!is_readable($filename)) {
            throw new Exception('Layout not found');
        }

        require $filename;
        $this->_rendered = true;
        throw new ActionFinishedException;
    }

    /**
     * Выводит шаблон
     * @throws Exception
     */
    protected function content() {
        // Путь к директории с шаблонами
        $viewsPath = APPLICATION_PATH . '/views/';

        if (!is_null($this->_partial)) {
            $filename = $viewsPath . $this->_partial . '.phtml';

            if (is_readable($filename)) {
                require $filename;
                return;
            } else {
                throw new Exception('Partial not found');
            }
        }

        $router = $this->_registry->router;

        // Путь к файлу шаблона
        $filename = $viewsPath . $router->getControllerUrlPart()
            . '/' . $router->getAction() . '.phtml';

        if (!is_readable($filename)) {
            throw new Exception('Partial not found');
        }

        require $filename;
    }

    /**
     * Передача json-данных для вывода в шаблон
     * Можно использовать только при запросе действия
     * @param mixed[] $value
     * @throws ActionFinishedException
     */
    public function json($value) {
        $this->_json = (array)$value;
        throw new ActionFinishedException;
    }

    /**
     * Передача meta тэгов в шаблон
     * @param mixed[] $value
     */
    public function meta($value) {
        $this->_meta = (array)$value;
    }

    /**
     * Устанавливает главный шаблон для вывода
     * @param string $layout
     */
    public function setLayout($layout) {
        $this->_layout = $layout;
    }

    /**
     * Возвращает инстанс view хелпера
     * @param $name Название хелпера
     * @return PsViewHelper
     */
    public function getHelper($name) {
        $name = ucfirst($name) . 'ViewHelper';
        return new $name;
    }
}
