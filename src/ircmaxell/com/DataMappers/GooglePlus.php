<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class GooglePlus {

    public function getPost(array $data = array()) {
        $postData = array(
            'parent_id' => null,
            'type' => 'googleplus',
            'type_id' => $data['id'],
            'user' => $data['actor']['displayName'],
            'type_user_id' => $data['actor']['id'],
            'title' => isset($data['title']) ? $data['title'] : $data['object']['content'],
            'summary' => $data['object']['content'],
            'body' => $data['object']['content'],
            'thumbnail' => '',
            'created_at' => date('Y-m-d H:i:s', strtotime($data['published'])),
            'has_children' => !empty($data['children']),
            'children' => array()
        );
        if (!empty($data['object']['attachments'])) {
            foreach ($data['object']['attachments'] as $attachment) {
                if ($attachment['objectType'] == 'photo') {
                    $postData['thumbnail'] = $attachment['image']['url'];
                    break;
                }
            }
        }
        if (isset($data['children'])) {
            foreach ($data['children'] as $child) {
                $postData['children'][] = $this->getPost($child);
            }
        }
        return new PostModel($postData);
    }

}