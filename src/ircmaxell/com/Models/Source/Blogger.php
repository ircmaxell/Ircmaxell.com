<?php

namespace ircmaxell\com\Models\Source;

use ircmaxell\com\Sources\REST;

class Blogger implements \ircmaxell\com\Models\Source {

    protected $apiKey = '';
    protected $blogId = '';
    protected $mapper;

    public function __construct($apiKey, $blogId, $mapper = null) {
        $this->apiKey = $apiKey;
        $this->blogId = $blogId;
        if (!$mapper) {
            $mapper = new \ircmaxell\com\DataMappers\Blogger;
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
    public function getPost($id) {
        $uri = 'https://www.googleapis.com/blogger/v2/blogs/' . $this->blogId . '/posts/' . $id;
        $params = array(
            'key' => $this->apiKey,
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $source = $data ? json_decode($data, true) : array();
        if (isset($source['object']['replies']) && $source['object']['replies']['totalItems'] > 0) {
            $source['children'] = $this->getComments($source['id']);
        }
        return $this->mapper->getPost($source);
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
        $uri = 'https://www.googleapis.com/blogger/v2/blogs/' . $this->blogId . '/posts';
        $params = array(
            'key' => $this->apiKey,
            'maxResults' => $limit
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $sources = $data ? json_decode($data, true) : array();
        $result = array();
        if (!isset($sources['items'])) {
            return array();
        }
        foreach ($sources['items'] as $source) {
            if (isset($source['replies']) && $source['replies']['totalItems'] > 0) {
                $source['children'] = $this->getComments($source['replies']['selfLink']);
            }
            $result[] = $this->mapper->getPost($source);
        }
        return $result;
    }

    protected function getComments($uri) {
        $params = array(
            'key' => $this->apiKey,
            'maxResults' => 100
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $sources = $data ? json_decode($data, true) : array();
        if (isset($sources['items'])) {
            return $sources['items'];
        }
        return array();
    }

}