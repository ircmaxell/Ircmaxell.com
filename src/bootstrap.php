<?php

define('PATH_ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class);
    $path = PATH_ROOT . '/src/' . $file . '.php';
    if (file_exists($path)) {
        require $path;
    }
    $path = PATH_ROOT . '/lib/' . $file . '.php';
    if (file_exists($path)) {
        require $path;
    }
});