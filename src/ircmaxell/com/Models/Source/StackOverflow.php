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
        $post = $this->getPostsByIds($id, $type, true);
        if (isset($post[0])) {
            return $this->mapToObject($post[0]);
        }
        return null;
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
        $posts = array(
            'questions' => array(),
            'answers' => array(),
        );
        foreach ($sources['user_timelines'] as $source) {
            switch ($source['timeline_type']) {
                case 'askoranswered':
                case 'comment':
                    $posts[$source['post_type'] . 's'][] = $source['post_id'];
                    break;
                case 'badge':
                    break;
            }
        }
        $answer_ids = implode(';', array_unique($posts['answers']));
        foreach ($this->getPostsByIds($answer_ids, 'answers') as $answer) {
            $posts['questions'][] = $answer['question_id'];
        }
        $question_ids = implode(';', array_unique($posts['questions']));
        foreach ($this->getPostsByIds($question_ids, 'questions', true) as $question) {
            $result[] = $this->mapToObject($question);
        }
        return $result;
    }

    
    protected function getPostsByIds($ids, $type, $full = false) {
        $uri = 'http://api.stackoverflow.com/1.1/' . $type . '/' . $ids;
        if ($full) {
            $params = array(
                'comments' => 'true',
                'body' => 'true',
                'answers' => 'true',
            );
        } else {
            $params = array();
        }
        $rest = new REST($uri);
        $data = $rest->get($params);
        $source = $data ? json_decode($data, true) : array();
        return isset($source[$type]) ? $source[$type] : array();
    }
    
    protected function mapToObject(array $data) {
        $children = array();
        if (isset($data['comments'])) {
            foreach ($data['comments'] as $comment) {
                if (isset($data['tags'])) {
                    $comment['tags'] = $data['tags'];
                }
                $children[] = $this->mapToObject($comment);
            }
        }
        if (isset($data['answers'])) {
            foreach ($data['answers'] as $answer) {
                if (isset($data['tags'])) {
                    $answer['tags'] = $data['tags'];
                }
                $children[] = $this->mapToObject($answer);
            }
        }
        $data['children'] = $children;
        $data['has_children'] = !empty($children);
        return $this->mapper->getPost($data);
    }
}