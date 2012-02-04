<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class StackOverflow {

    /**
     * Get a post by the given identifier
     *
     * @param string $id The identifier to fetch the post for
     *
     * @return Post An instance of the post type for the source class
     */
    public function getPost(array $data = array()) {
        $postData = array(
            'parent_id' => null,
            'type' => 'stackoverflow',
            'type_id' => '',
            'thumbnail' => '',
            'body' => $data['body'],
            'summary' => $data['body'],
            'created_at' => date('Y-m-d H:i:s', $data['creation_date']),
            'has_children' => isset($data['comments']) && count($data['comments']) > 0,
        );
        if (isset($data['comment_id'])) {
            $postData['type'] = 'stackoverflow_comment';
            $postData['type_id'] = $data['comment_id'];
            $postData['title'] = substr(strip_tags($data['body']), 0, 30);
            $postData['summary'] = $data['body'];
        } elseif (isset($data['answer_id'])) {
            $postData['type'] = 'stackoverflow_answer';
            $postData['type_id'] = $data['answer_id'];
            $postData['title'] = $data['title'];
            $postData['summary'] = $data['body'];
        } elseif (isset($data['question_id'])) {
            $postData['type'] = 'stackoverflow_question';
            $postData['type_id'] = $data['question_id'];
            $postData['title'] = $data['title'];
            $postData['summary'] = $data['body'];
        }

        return new PostModel($postData);
    }

}