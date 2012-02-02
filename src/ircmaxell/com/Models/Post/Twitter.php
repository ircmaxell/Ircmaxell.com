<?php

namespace ircmaxell\com\Models\Post;

use ircmaxell\com\Sources\REST;

class Twitter implements \ircmaxell\com\Models\Post {

    protected $data = array();

    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function getBody() {
        return $this->data['text'];
    }

    public function getChildren() {
        return array();
    }

    public function getIcon() {
        return 'twitter.png';
    }

    public function getSummary() {
        return $this->data['text'];
    }

    public function getThumbnail() {
        return '';
    }

    public function getTime() {
        return strtotime($this->data['created_at']);
    }

    public function getTitle() {
        return substr($this->data['text'], 0, 10);
    }

    public function hasChildren() {
        return false;
    }

    public function toJSON() {
        $data = array(
            'type' => 'Twitter',
            'data' => $this->data,
        );
        return json_encode($data);
    }

}