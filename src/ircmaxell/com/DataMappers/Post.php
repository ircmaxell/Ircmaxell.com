<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class Post {

    protected $fields = array(
        'id',
        'type',
        'type_id',
        'user',
        'type_user_id',
        'title',
        'summary',
        'body',
        'thumbnail',
        'created_at',
        'source_url',
        'rawData',
        'parent',
        'children',
    );

    protected $mysqli;

    public function __construct(\ircmaxell\com\Sources\MySQLi $mysqli) {
        $this->mysqli = $mysqli;
    }

    public function save(\ircmaxell\com\Models\Post $post) {
        $sql = 'SELECT id FROM `posts` WHERE type = ? AND type_id = ? LIMIT 1';
        $result = $this->mysqli->query($sql, array($post->type, $post->type_id))->fetch_assoc();
        $id = $result ? $result['id'] : 0;
        $values = array();
        $sql = '';
        if ($post->id || $id) {
            $post->id = $id;
            $sql = 'UPDATE `posts` SET ';
            foreach ($this->fields as $field) {
                if ($field === 'id') {
                    continue;
                }
                $sql .= '`' . $field . '` = ?, ';
                if (in_array($field, array('rawData', 'parent', 'children'))) {
                    $values[] = serialize($post->$field);
                } else {
                    $values[] = $post->$field;
                }
            }
            $sql = rtrim($sql, ' ,');
            $sql .= ' WHERE `id` = ?';
            $values[] = $post->id;
        } else {
            $sql = 'INSERT INTO `posts` (`' .
                implode('`, `', $this->fields) .
                '`) VALUES (' .
                implode(', ', array_map(function() { return '?'; }, $this->fields)) .
                ')';
            foreach ($this->fields as $field) {
                if (in_array($field, array('rawData', 'parent', 'children'))) {
                    $values[] = serialize($post->$field);
                } else {
                    $values[] = $post->$field;
                }
            }
        }
        $this->mysqli->query($sql, $values);
        if (!$id) {
            $post->id = $this->mysqli->insert_id;
        }
        if ($post->tags) {
            foreach ($post->tags as $tag) {
                $this->saveTag($tag, $post->id);
            }
        }
    }

    public function saveTag($tag, $postId) {
        $tag = ucwords(strtolower($tag));
        $sql = 'SELECT id FROM `tags` WHERE `name` LIKE ? LIMIT 1';
        $result = $this->mysqli->query($sql, array($tag));
        $row = $result->fetch_assoc();
        if ($row) {
            $id = $row['id'];
        } else {
            // Add tag to database
            $sql = 'INSERT INTO `tags` (`name`) VALUES (?)';
            $this->mysqli->query($sql, array($tag));
            $id = $this->mysqli->insert_id;
        }
        $sql = 'INSERT IGNORE INTO `post_to_tag` (post_id, tag_id) VALUES (?, ?)';
        $this->mysqli->query($sql, array($postId, $id));
    }

    public function saveAll(array $posts) {
        foreach ($posts as $post) {
            $this->save($post);
        }
    }

    public function find($limit = 10, $offset = 0, $sort = 'created_at') {
        if (!in_array($sort, $this->fields)) {
            throw new InvalidArgumentException('Invalid Sort Field Provided');
        }
        $sql = 'SELECT * FROM `posts` ORDER BY `'.$sort.'` DESC LIMIT '.(int) $offset.', '.(int) $limit;
        return $this->loadSet($sql, array());
    }

    public function findByType($type, $limit = 10, $offset = 0, $sort = 'created_at') {
        if (!in_array($sort, $this->fields)) {
            throw new InvalidArgumentException('Invalid Sort Field Provided');
        }
        $sql = 'SELECT * FROM `posts` WHERE type = ? ORDER BY `'.$sort.'` LIMIT ?, ?';
        return $this->loadSet($sql, array($type, $offset, $limit));
    }

    public function loadById($id) {
        $sql = 'SELECT * FROM `posts` WHERE id = ? LIMIT 1';
        return $this->loadSingle($sql, array($id));

    }

    public function loadByTypeId($type, $id) {
        $sql = 'SELECT * FROM `posts` WHERE type = ? AND type_id = ? LIMIT 1';
        return $this->loadSingle($sql, array($type, $id));
    }

    protected function loadSet($sql, $params) {
        $results = array();
        $result = $this->mysqli->query($sql, $params);
        while ($row = $result->fetch_assoc()) {
            $row['children'] = unserialize($row['children']);
            $row['parent'] = unserialize($row['parent']);
            $tmp = new PostModel($row);
            $results[$tmp->id] = $tmp;
        }
        unset($row);
        $inClause = array();
        $values = array();
        foreach ($results as $row) {
            $inClause[] = '?';
            $values[] = $row->id;
        }
        if ($inClause) {
            $result = $this->mysqli->query('SELECT `post_id`, `name` FROM `tags` AS t JOIN `post_to_tag` AS p2t ON t.id = p2t.tag_id WHERE p2t.`post_id` IN ('. implode(',', $inClause).')', $values);
            $tags = array();
            while ($row = $result->fetch_assoc()) {
                $tags = $results[$row['post_id']]->tags;
                if ($tags) {
                    $tags[] = $row['name'];
                } else {
                    $tags = array($row['name']);
                }
                $results[$row['post_id']]->tags = $tags;
            }
        }
        return $results;
    }

    protected function loadSingle($sql, $params) {
        $results = $this->loadSet($sql, $params);
        return isset($results[0]) ? $results[0] : false;
    }

}