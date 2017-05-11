<?php

/**
 * Класс, предоставляющий доступ к БД
 * @author ismd
 */
class PsDb extends PsSingleton {

    /**
     * Объект подключения
     * @var mysqli
     */
    public $db;

    /**
     * Подключается к БД
     * @throws Exception
     */
    protected function __construct() {
        $config = PsConfig::getInstance()->database;

        $this->db = new mysqli(
            $config->host,
            $config->username,
            $config->password,
            $config->dbname
        );

        if ($this->db->connect_error) {
            throw new Exception("Can't connect to database");
        }

        $this->db->set_charset('utf8');
    }

    public function __destruct() {
        $this->db->close();
    }

    /**
     * @param mixed[] $options
     * @return PsDb
     */
    public static function getInstance($options = []) {
        return parent::getInstance();
    }
}
