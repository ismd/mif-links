<?php

class GroupMapper extends PsDbMapper {

    public function add($title) {
        $stmt = self::$db->prepare("INSERT INTO Groups (title) VALUES (?)");
        $stmt->bind_param('s', $title);
        $stmt->execute();

        return [
            'id' => $stmt->insert_id,
        ];
    }

    public function edit($id, $title) {
        $stmt = self::$db->prepare("UPDATE Groups SET title = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param('si', $title, $id);
        $stmt->execute();

        return [
            'id' => $id,
        ];
    }

    public function remove($id) {
        $stmt = self::$db->prepare("DELETE FROM Groups WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function fetch($where = [], $limit = null, DateTime $from = null, DateTime $to = null) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        if ($from != null && $to != null) {
            $period = "AND s.visited >= ? AND s.visited < ? ";
        } else {
            $period = null;
        }

        $stmt = self::$db->prepare("SELECT g.id, g.title, COUNT(DISTINCT l.id) AS links_count, COUNT(s.id) AS stat_count " .
                                   "FROM Groups g " .
                                   "LEFT JOIN Links l ON g.id = l.group_id " .
                                   "LEFT JOIN Stat s ON l.id = s.link_id " .
                                   ($period != null ? $period : "") .
                                   (!empty($where) ? "WHERE " . implode(' AND ', $where) . ' ' : '') .
                                   "GROUP BY g.id " .
                                   "ORDER BY g.id DESC " .
                                   (!is_null($limit) ? "LIMIT " . $limit : ''));

        if ($from != null && $to != null) {
            $stmt->bind_param('ss', $from->format('Y-m-d'), $to->format('Y-m-d'));
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $links = [];
        while ($row = $result->fetch_assoc()) {
            $links[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'links_count' => $row['links_count'],
                'stat_count' => $row['stat_count'],
            ];
        }

        return $links;
    }

    public function fetchCount($where = []) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        $stmt = self::$db->prepare("SELECT 1 " .
                                   "FROM Groups g" .
                                   (!empty($where) ? " WHERE " . implode(' AND ', $where) : ''));

        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows;
    }

    public function search($title) {
        $result = self::$db->query("SELECT g.id, g.title " .
                                   "FROM Groups g " .
                                   "WHERE g.title LIKE '%" . $title . "%' " .
                                   "ORDER BY g.id DESC");

        $groups = [];
        while ($row = $result->fetch_assoc()) {
            $groups[] = [
                'id' => $row['id'],
                'title' => $row['title'],
            ];
        }

        return $groups;
    }
}
