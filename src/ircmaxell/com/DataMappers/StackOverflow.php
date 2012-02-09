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
            'type' => 'stackoverflow',
            'type_id' => '',
            'user' => $data['owner']['display_name'],
            'type_user_id' => $data['owner']['user_id'],
            'thumbnail' => '',
            'title' => isset($data['title']) ? $data['title'] : substr(strip_tags($data['body']), 0, 30),
            'body' => $data['body'],
            'summary' => $data['body'],
            'created_at' => date('Y-m-d H:i:s', $data['creation_date']),
            'source_url' => '',
            'children' => $data['children'],
            'parent' => null,
            'rawData' => $data,
            'tags' => $data['tags'],
        );
        if (isset($data['comment_id'])) {
            $postData['type'] = 'stackoverflow_comment';
            $postData['type_id'] = $data['comment_id'];
            $postData['title'] = substr(strip_tags($data['body']), 0, 30);
            $postData['summary'] = $data['body'];
            $postData['source_url'] = 'http://www.stackoverflow.com/';
            if ($data['post_type'] == 'answer') {
                $postData['source_url'] .= 'a/' . $data['post_id'] . '/' . $data['owner']['user_id'];
            } else {
                $postData['source_url'] .= 'q/' . $data['post_id'] . '/' . $data['owner']['user_id'];
            }
        } elseif (isset($data['answer_id'])) {
            $postData['type'] = 'stackoverflow_answer';
            $postData['type_id'] = $data['answer_id'];
            $postData['title'] = $data['title'];
            $postData['summary'] = $data['body'];
            $postData['source_url'] = '';
            $postData['source_url'] = 'http://www.stackoverflow.com/a/' . $data['answer_id'] . '/' . $data['owner']['user_id'];
        } elseif (isset($data['question_id'])) {
            $postData['type'] = 'stackoverflow_question';
            $postData['type_id'] = $data['question_id'];
            $postData['title'] = $data['title'];
            $postData['summary'] = $data['body'];
            $postData['source_url'] = 'http://www.stackoverflow.com/q/' . $data['question_id'] . '/' . $data['owner']['user_id'];
        }
        return new PostModel($postData);
    }

}