<?php

class StatMapper extends PsDbMapper {

    public function add($idLink, $stat) {
        $stmt = self::$db->prepare("INSERT INTO Stat (link_id, referer, user_agent, ip) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $idLink, $stat['referer'], $stat['user_agent'], $stat['ip']);
        $stmt->execute();
    }

    public function fetch($where = [], $limit = null) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        $result = self::$db->query("SELECT id, visited, referer, user_agent, ip "
            . "FROM Stat "
            . (!empty($where) ? "WHERE " . implode(' AND ', $where) . ' ' : '')
            . "ORDER BY id DESC "
            . (!is_null($limit) ? "LIMIT " . $limit : ''));

        $stat = [];
        while ($row = $result->fetch_assoc()) {
            $stat[] = [
                'id' => $row['id'],
                'visited' => $row['visited'],
                'referer' => $row['referer'],
                'user_agent' => $row['user_agent'],
                'ip' => $row['ip'],
            ];
        }

        return $stat;
    }

    public function fetchCount($where = []) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        $stmt = self::$db->prepare("SELECT 1 "
            . "FROM Stat"
            . (!empty($where) ? " WHERE " . implode(' AND ', $where) : ''));

        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows;
    }

    /**
     * @return StatMapper
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }
}
