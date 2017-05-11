<?php

/**
 * Родительский класс для mapper'ов с подключением к БД
 * @author ismd
 */
abstract class PsDbMapper extends PsMapper {

    /**
     * @var mysqli
     */
    protected static $db;

    /**
     * Подключается к БД
     * @throws Exception
     */
    protected function __construct() {
        if (is_null(self::$db)) {
            self::$db = PsDb::getInstance()->db;
        }
    }

    /**
     * @return PsDbMapper
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }
}
