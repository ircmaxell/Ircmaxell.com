<?php

namespace ircmaxell\com\Resources;

use ircmaxell\com\Models\Source;

class post {

    protected $sources = array();

    public function __construct() {
        $this->sources[] = new Source\Twitter('ircmaxell');
        $this->sources[] = new Source\StackOverflow(338665);
    }

    public function get(\ircmaxell\com\Request $request, \ircmaxell\com\Response $response) {
        $limit = $request->get('limit', 10) + $request->get('start', 0);
        $posts = array();
        foreach ($this->sources as $source) {
            $posts = array_merge($posts, $source->getLatestPosts(0, $limit));
        }
        usort($posts, array($this, 'sort'));
        $result = array_slice($posts, $request->get('start', 0), $request->get('limit', 10));
        $data = '[' . implode(',', array_map(function($post) {return $post->toJson();}, $result)) . ']';
        $response->setBody($data);
    }

    protected function sort(\ircmaxell\com\Models\Post $post1, \ircmaxell\com\Models\Post $post2) {
        return $post2->getTime() - $post1->getTime();
    }

}