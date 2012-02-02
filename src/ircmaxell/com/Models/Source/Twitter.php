<?php

namespace ircmaxell\com\Models\Source;

use ircmaxell\com\Models\Post\Twitter as TwitterPost;

use ircmaxell\com\Sources\REST;

class Twitter implements \ircmaxell\com\Models\Source {

    protected $username = '';

    public function __construct($username) {
        $this->username = $username;
    }

    /**
     * Get a post by the given identifier
     *
     * @param string $id The identifier to fetch the post for
     *
     * @return Post An instance of the post type for the source class
     */
    public function getPost($id) {
        $uri = 'https://api.twitter.com/1/statuses/show/' . $id . '.json';
        $params = array(
            'include_entities' => '1',
            'trim_user' => '0',
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $source = $data ? json_decode($data, true) : array();
        return new TwitterPost($source);
    }

    /**
     * Get the latest posts from the source up to the limit
     *
     * @param int $start The start position for paginating the result
     * @param int $limit The the maximum number of items to return
     *
     * @return Post[] An array of post objects for the source class
     */
    public function getLatestPosts($start = 0, $limit = 10) {
        $uri = 'https://api.twitter.com/1/statuses/user_timeline.json';
        $params = array(
            'screen_name' => $this->username,
            'count' => $limit,
            'page' => ($start / $limit) + 1,
            'include_rts' => '1',
            'include_entities' => '1',
            'exclude_replies' => '0',
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $sources = $data ? json_decode($data, true) : array();
        $result = array();
        foreach ($sources as $source) {
            $result[] = new TwitterPost($source);
        }
        return $result;
    }

}