<?php

class StatMapper extends PsDbMapper {

    public function add($idLink, $stat) {
        $stmt = self::$db->prepare("INSERT INTO Stat (link_id, referer, user_agent, ip) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $idLink, $stat['referer'], $stat['user_agent'], $stat['ip']);
        $stmt->execute();
    }

    /**
     * @return StatMapper
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }
}
