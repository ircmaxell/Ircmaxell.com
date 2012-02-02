<?php

namespace ircmaxell\com\Models\Post;

class StackOverflowComment implements \ircmaxell\com\Models\Post {

    protected $data = array();

    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function getBody() {
        return $this->data['body'];
    }

    public function getChildren() {
        return array();
    }

    public function getIcon() {
        return 'stackoverflow_comment.png';
    }

    public function getSummary() {
        return $this->data['body'];
    }

    public function getThumbnail() {
        return '';
    }

    public function getTime() {
        return $this->data['creation_date'];
    }

    public function getTitle() {
        return substr(strip_tags($this->data['title']), 0, 10);
    }

    public function hasChildren() {
        return false;
    }

    public function toJSON() {
        $data = array(
            'type' => 'StackOverflowComment',
            'data' => $this->data,
        );
        return json_encode($data);
    }

}