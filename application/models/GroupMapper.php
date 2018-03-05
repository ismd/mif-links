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

    public function fetch($where = [], $limit = null) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        $result = self::$db->query("SELECT g.id, g.title " .
                                   "FROM Groups g " .
                                   (!empty($where) ? "WHERE " . implode(' AND ', $where) . ' ' : '') .
                                   "ORDER BY g.id DESC " .
                                   (!is_null($limit) ? "LIMIT " . $limit : ''));

        $links = [];
        while ($row = $result->fetch_assoc()) {
            $links[] = [
                'id' => $row['id'],
                'title' => $row['title'],
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
