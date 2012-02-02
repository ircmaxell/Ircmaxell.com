<?php

spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class);
    $path = __DIR__ . '/' . $file . '.php';
    if (file_exists($path)) {
        require $path;
    }
    $path = dirname(__DIR__) . '/lib/' . $file . '.php';
    if (file_exists($path)) {
        require $path;
    }
});

require_once dirname(__DIR__) . '/config/config.php';

defined('PROXY') OR define('PROXY', false);