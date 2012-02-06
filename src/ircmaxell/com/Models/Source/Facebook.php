<?php

namespace ircmaxell\com\Models\Source;

use ircmaxell\com\Sources\REST;

class Facebook implements \ircmaxell\com\Models\Source {

    
    protected $token = '';
    protected $username = '';
    protected $mapper;

    public function __construct($appId, $appSecret, $username, $mapper = null) {
        $rest = new REST('https://graph.facebook.com/oauth/access_token');
        $params = array(
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'grant_type' => 'client_credentials'
        );
        $token = $rest->get($params);
        list (, $this->token) = explode('=', $token);
        $this->username = $username;
        if (!$mapper) {
            $mapper = new \ircmaxell\com\DataMappers\GooglePlus;
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
        $uri = 'https://www.googleapis.com/plus/v1/activities/' . $id;
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
        $uri = 'https://graph.facebook.com/' . $this->username . '/feed';
        $params = array(
            'access_token' => $this->token,
        );
        $rest = new REST($uri);
        $data = $rest->get($params);
        $sources = $data ? json_decode($data, true) : array();
        $result = array();
        if (!isset($sources['data'])) {
            return array();
        }
        die();
        foreach ($sources['data'] as $source) {
            if (isset($source['object']['replies']) && $source['object']['replies']['totalItems'] > 0) {
                $source['children'] = $this->getComments($source['id']);
            }
            $result[] = $this->mapper->getPost($source);
        }
        return $result;
    }

}