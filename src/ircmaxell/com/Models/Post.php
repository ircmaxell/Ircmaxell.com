<?php

namespace ircmaxell\com\Models;

class Post {

    protected $data = array();

    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function __toString() {
        $data = $this->data;
        $data['has_children'] = false;
        if (isset($data['children'])) {
            foreach ($this->data['children'] as $key => $child) {
                $data['children'][$key] = json_decode((string) $child);
            }
            $data['has_children'] = !empty($data['children']);
        }
        if (isset($this->data['parent'])) {
            $data['parent'] = json_decode((string) $this->data['parent']);
        }
        return json_encode($data);
    }
    
    public function __clone() {
        foreach ($this->data['children'] as $key => $child) {
            $this->data['children'][$key] = clone $child;
        }
        if (isset($this->data['parent'])) {
            $this->data['parent'] = clone $this->data['parent'];
        }
    }

    public function __get($field) {
        return isset($this->data[$field]) ? $this->data[$field] : null;
    }

    public function __set($field, $value) {
        $this->data[$field] = $value;
    }

}