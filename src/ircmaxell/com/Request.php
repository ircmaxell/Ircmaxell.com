<?php

namespace ircmaxell\com;

class Request {
    
    protected $get = array();
    protected $post = array();
    protected $cookies = array();
    
    protected $uri = '';
    protected $https = false;
    protected $method = 'get';
    
    public function __construct() {
        $this->uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $this->https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get';
        
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookies = $_COOKIE;
    }
    
    public function get($name) {
        return isset($this->get[$name]) ? $this->get[$name] : null;
    }
    
    public function post($name) {
        return isset($this->post[$name]) ? $this->post[$name] : null;
    }
    
    public function cookie($name) {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
    }
    
    public function getBody() {
        return file_get_contents('php://input');
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getUri() {
        return $this->uri;
    }

    public function isHTTPS() {
        return $this->https;
    }
    
    
}