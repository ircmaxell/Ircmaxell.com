<?php

namespace ircmaxell\com\Models;

interface Source {

    /**
     * Get a post by the given identifier
     *
     * @param string $id The identifier to fetch the post for
     *
     * @return Post An instance of the post type for the source class
     */
    public function getPost($id);

    /**
     * Get the latest posts from the source up to the limit
     *
     * @param int $start The start position for paginating the result
     * @param int $limit The the maximum number of items to return
     *
     * @return Post[] An array of post objects for the source class
     */
    public function getLatestPosts($start = 0, $limit = 10);

}
