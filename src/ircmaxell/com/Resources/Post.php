<?php

namespace ircmaxell\com\Resources;

use ircmaxell\com\Models\Source;

class Post {

    protected $id;
    
    protected $mapper;

    public function __construct(\ircmaxell\com\DataMappers\Post $mapper, $id) {
        $this->mapper = $mapper;
        $this->id = $id;
    }

    public function get(\ircmaxell\com\Request $request, \ircmaxell\com\Response $response) {
        $data = (string) $this->mapper->loadById($this->id);
        $response->setBody($data);
    }

}