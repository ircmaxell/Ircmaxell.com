<?php

namespace ircmaxell\com\Sources;

use \BadMethodCallException;
use \RuntimeException;

class REST {

    protected $url = '';

    public function __construct($url) {
        $this->url = $url;
    }

    /**
     *
     * @method string options() options($params = null, array $headers = array()) Options data to URL
     * @method string head() head($params = null, array $headers = array()) HEAD data to URL
     * @method string post() post($params = null, array $headers = array()) Post data to URL
     * @method string put() put($params = null, array $headers = array()) PUT data to URL
     * @method string delete() delete($params = null, array $headers = array()) Delete data to URL
     * @method string trace() trace($params = null, array $headers = array()) Trace data to URL
     *
     * @param string $name The method name
     * @param array  $args The arguments to the method to call
     *
     * @return string The result of the method call
     * @throws BadMethodCallException if the method isn't implemented
     */
    public function __call($name, array $args) {
        $name = strtoupper($name);
        if (in_array($name, array('OPTIONS', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE'))) {
            $callArgs = $args;
            array_unshift($callArgs, $name, $this->url);
            return call_user_func_array(array($this, 'getViaHttp'), $callArgs);
        }
        throw new BadMethodCallException('Call to unknown method: ' . $name);
    }

    public function get($params = null, array $headers = array()) {
        $uri = $this->url;
        $data = '';
        if ($params && is_array($params)) {
            $params = http_build_query($params);
            $uri .= strpos($uri, '?') !== false ? '&' : '?';
            $uri .= $params;
        } elseif ($params) {
            $data = $params;
        }
        return $this->getViaHttp($uri, 'GET', $data);
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }


    protected function getViaHttp($uri, $method = 'GET', $data = null, array $headers = array()) {
        $curl = curl_init($uri);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => 'gzip',
        ));
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if (PROXY) {
            curl_setopt($curl, CURLOPT_PROXY, PROXY);
        }
        $result = curl_exec($curl);
        if ($result) {
            return $result;
        }
        throw new RuntimeException('Request to ' . $uri . ' Failed');
    }

}