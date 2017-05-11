<?php

/**
 * @author ismd
 */
abstract class PsObject {

    /**
     * @var mixed[]
     */
    protected $_data = [];

    public function __set($name, $value) {
        $this->_data[$name] = $value;
        return $this;
    }

    public function __get($name) {
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }

    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    public function __unset($name) {
        if (!isset($this->_data[$name])) {
            return;
        }

        unset($this->_data[$name]);
    }

    /**
     * @return PsObject
     */
    public function clear() {
        $this->_data = [];
        return $this;
    }
}
