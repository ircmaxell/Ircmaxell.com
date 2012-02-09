<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class Twitter {

    public function __construct() {
    }

    public function getPost(array $data = array()) {
        $postData = array(
            'type' => 'twitter',
            'type_id' => $data['id_str'],
            'user' => $data['user']['name'],
            'type_user_id' => $data['user']['id_str'],
            'title' => substr(strip_tags($data['text']), 0, 30),
            'summary' => $data['text'],
            'body' => $data['text'],
            'thumbnail' => '',
            'created_at' => date('Y-m-d H:i:s', strtotime($data['created_at'])),
            'source_url' => 'https://www.twitter.com/#!/' . $data['user']['name'] . '/status/' . $data['id_str'],
            'rawData' => $data,
            'parent' => null,
            'children' => array(),
            'tags' => array(),
        );
        foreach ($data['entities']['hashtags'] as $tag) {
            $postData['tags'][] = $tag['text'];
        }
        return new PostModel($postData);
    }

}