<?php

namespace ircmaxell\com\Models\Post;

class StackOverflow implements \ircmaxell\com\Models\Post {

    protected $data = array();
    protected $type = array();

    public function __construct(array $data = array(), $type = 'answer') {
        $this->data = $data;
    }

    public function getBody() {
        return $this->data['body'];
    }

    public function getChildren() {
        return array_map($this->data['comments'], function($comment) {
            return new StackOverflowComment($comment);
        });
    }

    public function getIcon() {
        return 'stackoverflow.' . $this->type . '.png';
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
        return $this->data['title'];
    }

    public function hasChildren() {
        return !empty($this->data['children']);
    }

    public function toJSON() {
        $data = array(
            'type' => 'StackOverflow',
            'posttype' => $this->type,
            'data' => $this->data,
        );
        return json_encode($data);
    }

}