<?php

namespace ircmaxell\com\Resources\Post;

use ircmaxell\com\Models\Source\Twitter as TwitterSource;

class twitter {

    protected $id = 0;
    protected $source;

    public function __construct($id = 0) {
        $this->source = new TwitterSource('ircmaxell');
        $this->id = $id;
    }

    public function get(\ircmaxell\com\Request $request, \ircmaxell\com\Response $response) {
        $data = '';
        if ($this->id) {
            $data = $this->source->getPost($this->id)->toJson();
        } else {
            $data = '[' .
                implode(
                    ',',
                    array_map(
                        $this->source->getPosts(
                            $request->get('start', 0),
                            $request->get('limit', 10)
                        ),
                        function($post) {
                            return $post->toJson();
                        }
                    )
                ) .
                ']';
        }
        $response->setBody($data);
    }

}