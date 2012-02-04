<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class Twitter {

    public function __construct() {
    }

    public function getPost(array $data = array()) {
        $postData = array(
            'parent_id' => null,
            'type' => 'twitter',
            'type_id' => $data['id_str'],
            'user' => $data['user']['name'],
            'type_user_id' => $data['user']['id_str'],
            'title' => substr(strip_tags($data['text']), 0, 30),
            'summary' => $data['text'],
            'body' => $data['text'],
            'thumbnail' => '',
            'created_at' => date('Y-m-d H:i:s', strtotime($data['created_at'])),
            'has_children' => false,
        );
        return new PostModel($postData);
    }

}