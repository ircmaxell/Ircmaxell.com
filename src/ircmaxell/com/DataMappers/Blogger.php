<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class Blogger {

    public function getPost(array $data = array()) {
        $postData = array(
            'parent_id' => null,
            'type' => 'blogger',
            'type_id' => $data['id'],
            'user' => $data['author']['displayName'],
            'type_user_id' => isset($data['author']['id']) ? $data['author']['id'] : 0,
            'title' => isset($data['title']) ? $data['title'] : substr(strip_tags($data['content']), 0, 30),
            'summary' => substr(strip_tags($data['content']), 0, 500),
            'body' => $data['content'],
            'thumbnail' => '',
            'created_at' => date('Y-m-d H:i:s', strtotime($data['published'])),
            'has_children' => !empty($data['children']),
            'source_url' => $data['url'],
            'children' => array(),
            'tags' => isset($data['labels']) ? $data['labels'] : array(),
            'rawData' => $data,
        );
        if (isset($data['children'])) {
            foreach ($data['children'] as $child) {
                $postData['children'][] = $this->getPost($child);
            }
        }
        return new PostModel($postData);
    }

}