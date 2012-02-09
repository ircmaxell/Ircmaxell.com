<?php

namespace ircmaxell\com\Resources;

use ircmaxell\com\Models\Source;

class Tags {

    protected $mapper;

    public function __construct(\ircmaxell\com\DataMappers\Tag $mapper) {
        $this->mapper = $mapper;
    }

    public function get(\ircmaxell\com\Request $request, \ircmaxell\com\Response $response) {
        $limit = $request->get('limit', 100) + $request->get('start', 0);
        $data = $this->mapper->find($limit, (int) $request->get('start', 0));
        $data = '[' . implode(',', $data) . ']';
        $response->setBody($data);
    }

}