<?php

/**
 * Родительский класс для моделей
 * @author ismd
 */
abstract class PsModel extends PsObject {

    /**
     * @param mixed[] $options
     * @throws Exception
     */
    public function __construct($options = null) {
        // Сразу инициализируем объект данными
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Инициализация объекта из массива
     * @param mixed[] $options
     * @return PsObject
     * @throws Exception
     */
    public function setOptions(array $options) {
        foreach ($options as $name => $value) {
            $method = explode('_', $name);

            // ucfirst для всех элементов
            array_walk($method, function($val) {
                return ucfirst($val);
            });

            // Имя метода в стиле camelCase
            $method = 'set' . implode($method);

            if (!method_exists($this, $method)) {
                throw new Exception('Bad property');
            }

            $this->$method($value);
        }

        return $this;
    }
}
