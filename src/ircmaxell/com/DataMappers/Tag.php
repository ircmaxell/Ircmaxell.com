<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Tag as TagModel;

class Tag {

    protected $fields = array(
        'id',
        'name',
    );

    protected $mysqli;

    public function __construct(\ircmaxell\com\Sources\MySQLi $mysqli) {
        $this->mysqli = $mysqli;
    }

    public function find($limit = 10, $offset = 0, $sort = 'name') {
        if (!in_array($sort, $this->fields)) {
            throw new InvalidArgumentException('Invalid Sort Field Provided');
        }
        $sql = 'SELECT * FROM `tags` ORDER BY `'.$sort.'` ASC LIMIT '.(int) $offset.', '.(int) $limit;
        return $this->loadSet($sql, array());
    }

    protected function loadSet($sql, $params) {
        $results = array();
        $result = $this->mysqli->query($sql, $params);
        while ($row = $result->fetch_assoc()) {
            $tmp = new TagModel($row);
            $results[$tmp->id] = $tmp;
        }
        return $results;
    }

    protected function loadSingle($sql, $params) {
        $results = $this->loadSet($sql, $params);
        return isset($results[0]) ? $results[0] : false;
    }

}