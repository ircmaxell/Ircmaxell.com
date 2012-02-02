<?php

namespace ircmaxell\com\Models\Source;

use ircmaxell\com\Models\Post\StackOverflow as StackOverflowPost;
use ircmaxell\com\Models\Post\StackOverflowComment;

use ircmaxell\com\Sources\REST;

class StackOverflow implements \ircmaxell\com\Models\Source {

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
    public function getPost($id, $type = 'answers') {
        $uri = 'http://api.stackoverflow.com/1.1/' . $type . '/' . $id;
        $params = array(
            'comments' => 'true',
            'body' => 'true'
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $source = $data ? json_decode($data, true) : array();
        switch ($type) {
            case 'comments':
                return new StackOverflowComment($source[$type][0]);
            default:
                return new StackOverflowPost($source[$type][0], substr($type, 0, -1));
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
            switch ($source['timeline_type']) {
                case 'askoranswered':
                    $result[] = $this->getPost($source['post_id'], $source['post_type'] . 's');
                    break;
                case 'badge':
                    break;
                case 'comment':
                    $result[] = $this->getPost($source['comment_id'], 'comments');
                    break;
            }
        }
        return $result;
    }

}