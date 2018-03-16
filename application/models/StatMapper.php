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

    public function fetchPeriodByGroup(DateTime $from, DateTime $to, $groupId) {
        $to = $to->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($from, $interval, $to);

        $dates = [];
        foreach ($period as $date){
            $dates[$date->format('d.m.Y')] = 0;
        }

        $stmt = self::$db->prepare("SELECT DATE_FORMAT(s.visited, '%d.%m.%Y') AS visited_date, COUNT(s.id) AS stat_count " .
                                   "FROM Stat s " .
                                   "JOIN Links l ON s.link_id = l.id " .
                                   "JOIN Groups g ON l.group_id = g.id " .
                                   "WHERE g.id = ? AND s.visited >= ? AND s.visited < ? " .
                                   "GROUP BY visited_date " .
                                   "ORDER BY s.id");

        $stmt->bind_param('iss', $groupId, $from->format('Y-m-d'), $to->format('Y-m-d'));
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dates[$row['visited_date']] = $row['stat_count'];
        }

        return $dates;
    }

    /**
     * @return StatMapper
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }
}
