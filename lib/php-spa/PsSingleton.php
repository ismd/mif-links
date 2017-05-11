<?php

/**
 * Реализация singleton
 * @author ismd
 */
abstract class PsSingleton {

    /**
     * @var object[] Массив созданных объектов
     */
    private static $_instances = [];

    private function __construct() {
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    /**
     * Возвращает инстанс запроса
     * @param mixed[] $options
     * @return PsSingleton
     */
    public static function getInstance($options = []) {
        $class = get_called_class();

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class($options);
        }

        return self::$_instances[$class];
    }
}
