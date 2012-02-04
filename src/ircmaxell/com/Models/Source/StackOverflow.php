<?php

namespace ircmaxell\com\Models\Source;

use ircmaxell\com\Sources\REST;

class StackOverflow implements \ircmaxell\com\Models\Source {

    protected $username = '';
    protected $mapper;

    public function __construct($username, $mapper = null) {
        $this->username = $username;
        if (!$mapper) {
            $mapper = new \ircmaxell\com\DataMappers\StackOverflow;
        }
        $this->mapper = $mapper;
    }

    /**
     * Get a post by the given identifier
     *
     * @param string $id The identifier to fetch the post for
     *
     * @return Post An instance of the post type for the source class
     */
    public function getPost($id, $type = 'answers') {
        $uri = 'http://api.stackoverflow.com/1.1/' . $type . '/' . $id;
        $params = array(
            'comments' => 'true',
            'body' => 'true'
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $source = $data ? json_decode($data, true) : array();
        if (isset($source[$type]) && isset($source[$type][0])) {
            return $this->mapper->getPost($source[$type][0]);
        } else {
            return null;
        }

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
        $uri = 'http://api.stackoverflow.com/1.1/users/' . $this->username . '/timeline';
        $params = array(
            'pagesize' => $limit,
            'page' => ($start / $limit) + 1,
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $sources = $data ? json_decode($data, true) : array();
        $result = array();
        if (!isset($sources['user_timelines'])) {
            return $result;
        }
        foreach ($sources['user_timelines'] as $source) {
            $post = false;
            switch ($source['timeline_type']) {
                case 'askoranswered':
                    $post = $this->getPost($source['post_id'], $source['post_type'] . 's');
                    break;
                case 'badge':
                    break;
                case 'comment':
                    $post = $this->getPost($source['comment_id'], 'comments');
                    break;
            }
            if ($post) {
                $result[] = $post;
            }
        }
        return $result;
    }

}