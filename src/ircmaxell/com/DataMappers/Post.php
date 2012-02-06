<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class Post {

    protected $fields = array(
        'id',
        'parent_id',
        'type',
        'type_id',
        'user',
        'type_user_id',
        'title',
        'summary',
        'body',
        'thumbnail',
        'created_at',
        'has_children',
        'rawData'
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
                $values[] = $post->$field;
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
                $values[] = $post->$field;
            }
        }
        $this->mysqli->query($sql, $values);
        if (!$id) {
            $post->id = $this->mysqli->insert_id;
        }
        if ($post->has_children) {
            foreach ($post->children as $child) {
                $child->parent_id = $post->id;
                $this->save($child);
            }
        }
        if ($post->tags) {
            foreach ($post->tags as $tag) {
                $this->saveTag($tag, $post->id);
            }
        }
    }

    public function saveTag($tag, $postId) {
        $sql = 'SELECT id FROM `tags` WHERE `name` LIKE ? LIMIT 1';
        $result = $this->mysqli->query($sql, $params);
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
        $sql = 'SELECT * FROM `posts` WHERE `parent_id` IS NULL ORDER BY `'.$sort.'` LIMIT ?, ?';
        return $this->loadSet($sql, array($offset, $limit));
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

    public function loadByParentId($id) {
        $sql = 'SELECT * FROM `posts` WHERE parent_id = ?';
        return $this->loadSet($sql, array($id));

    }

    protected function loadSet($sql, $params) {
        $results = array();
        $result = $this->mysqli->query($sql, $params);
        while ($row = $result->fetch_assoc()) {
            $tmp = new PostModel($row);
            if ($tmp->has_children) {
                $tmp->children = $this->loadByParentId($tmp->id);
            }
            $results[] = $tmp;
        }
        return $results;
    }

    protected function loadSingle($sql, $params) {
        $results = $this->loadSet($sql, $params);
        return isset($results[0]) ? $results[0] : false;
    }

}