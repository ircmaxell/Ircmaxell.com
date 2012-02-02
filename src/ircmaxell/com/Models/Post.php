<?php

namespace ircmaxell\com\Models;

interface Post {

    public function getBody();

    public function getChildren();

    public function getIcon();

    public function getSummary();

    public function getThumbnail();

    public function getTime();

    public function getTitle();

    public function hasChildren();

    public function toJSON();

}
