<?php

namespace ircmaxell\com;

class Response {

    protected static $statusCodes = array(
        200 => '200 Ok',
        404 => '404 Not Found',
        405 => '405 Not Allowed',
        500 => '500 Internal Server Error',
    );

    protected $status = 200;

    protected $headers = array();

    protected $body = '';

    public function setBody($body) {
        $this->body = $body;
    }

    public function setStatus($new) {
        $this->status = $new;
    }

    public function render() {
        header('Status: ' . isset(static::$statusCodes[$this->status]) ? static::$statusCodes[$this->status] : $this->status, true, $this->status);
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->body;
    }

}