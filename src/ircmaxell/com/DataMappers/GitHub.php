<?php

namespace ircmaxell\com\DataMappers;

use ircmaxell\com\Models\Post as PostModel;

class GitHub {

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
            'type' => 'github',
            'type_id' => $data['id'],
            'user' => $data['actor']['login'],
            'type_user_id' => $data['actor']['id'],
            'thumbnail' => '',
            'title' => '',
            'body' => '',
            'summary' => '',
            'source_url' => '',
            'created_at' => date('Y-m-d H:i:s', strtotime($data['created_at'])),
            'has_children' => false,
            'children' => array(),
            'rawData' => $data,
        );
        switch ($data['type']) {
            case 'PushEvent':
                $postData['title'] = 'Pushed Commits To ' . $data['repo']['name'];
                $postData['summary'] = '<ul>';
                foreach ($data['payload']['commits'] as $commit) {
                    $postData['summary'] .= '<li>' . $commit['message'] . '</li>';
                }
                $postData['summary'] .= '</ul>';
                $postData['body'] = $postData['summary'];
                break;
            case 'CreateEvent':
                $postData['title'] = 'Created New Repository: ' . $data['repo']['name'];
                $postData['summary'] = $postData['body'] = $postData['title'];
                $postData['source_url'] = 'https://www.github.com/' . $data['repo']['name'];
                break;
            case 'GistEvent':
                $postData['title'] = 'Created New Gist';
                $postData['summary'] = $data['payload']['gist']['description'];
                $postData['body'] = $postData['summary'];
                $postData['source_url'] = $data['payload']['gist']['url'];
                break;
            case 'PullRequestEvent':
                $postData['title'] = 'Submitted New Pull Request To: ' . $data['repo']['name'];
                $postData['summary'] = $data['payload']['pull_request']['title'];
                $postData['body'] = $data['payload']['pull_request']['body'];
                $postData['source_url'] = $data['payload']['pull_request']['issue_url'];
                break;
            case 'ForkEvent':
                $postData['title'] = 'Forked A Repository: ' . $data['repo']['name'];
                $postData['summary'] = $postData['body'] = $postData['title'];
                $postData['source_url'] = $data['payload']['forkee']['html_url'];
                break;
            default:
                return null;
        }
        return new PostModel($postData);
    }

}