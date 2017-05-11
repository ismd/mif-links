<?php

/**
 * Предоставляет доступ к данным запроса
 * @author ismd
 */
class PsRequest extends PsSingleton {

    private $_post;

    /**
     * Возвращает post-данные
     * return PsPostRequest
     */
    public function getPost() {
        if (is_null($this->_post)) {
            $this->_post = new PsPostRequest;
        }

        return $this->_post;
    }

    /**
     * Возвращает true, если переданы данные методом post
     * @return boolean
     */
    public function isPost() {
        return 'POST' == $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return PsRequest
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }
}

/**
 * Данные, переданные методом post
 */
class PsPostRequest {

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $_POST[$name];
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return isset($_POST[$name]);
    }
}
