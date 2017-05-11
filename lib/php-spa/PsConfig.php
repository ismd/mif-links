<?php

/**
 * Класс, предоставляющий доступ к конфигам
 * @author ismd
 */
class PsConfig extends PsSingleton {

    /**
     * Путь к главному конфигурационному файлу
     * @var string
     */
    const FILENAME         = '/configs/application.ini';
    const FILENAME_EXAMPLE = '/configs/application.example.ini';

    /**
     * Объект конфига
     * @var object
     */
    protected $_config;

    /**
     * Читает конфигурационный файл
     * @throws Exception
     */
    protected function __construct() {
        $filename        = APPLICATION_PATH . self::FILENAME;
        $filenameExample = APPLICATION_PATH . self::FILENAME_EXAMPLE;

        if (!is_readable($filename) && !is_readable($filenameExample)) {
            throw new Exception("Can't read config file");
        }

        $config = [];

        if (is_readable($filename)) {
            $config = $this->_parse($filename);
        } elseif (is_readable($filenameExample)) {
            $config = $this->_parse($filenameExample, $config);
        }

        // Преобразуем все элементы к объектам
        array_walk($config, function(&$val) {
            $val = (object)$val;
        });

        $this->_config = (object)$config;
    }

    protected function _parse($filename, $oldConfig = []) {
        $config = parse_ini_file($filename, true);

        if (!$config) {
            throw new Exception("Can't read config file");
        }

        foreach ($config as $i => $section) {
            if (!isset($oldConfig[$i])) {
                $oldConfig[$i] = $section;
                continue;
            }

            foreach ($section as $j => $item) {
                if (!isset($oldConfig[$i][$j])) {
                    $oldConfig[$i][$j] = $item;
                }
            }
        }

        return $oldConfig;
    }

    public function __get($name) {
        return new PsConfigSection(isset($this->_config->$name) ? $this->_config->$name : null);
    }

    /**
     * @return PsConfig
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }
}

class PsConfigSection {

    protected $_value;

    public function __construct($value) {
        $this->_value = $value;
    }

    public function __get($name) {
        if (is_null($this->_value) || !isset($this->_value->$name)) {
            return null;
        }

        return $this->_value->$name;
    }

    public function toArray() {
        return (array)$this->_value;
    }
}
