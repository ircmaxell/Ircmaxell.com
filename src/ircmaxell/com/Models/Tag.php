<?php

namespace ircmaxell\com\Models;

class Tag {

    protected $data = array();

    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function __toString() {
        $data = $this->data;
        return json_encode($data);
    }

    public function __get($field) {
        return isset($this->data[$field]) ? $this->data[$field] : null;
    }

    public function __set($field, $value) {
        $this->data[$field] = $value;
    }

}