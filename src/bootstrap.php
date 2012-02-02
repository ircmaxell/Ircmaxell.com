<?php

spl_autoload_register(function ($class) {
    $file = substr(str_replace('\\', '/', $class), $nslen);
    $path = __DIR__ . $file . '.php';
    if (file_exists($path)) {
        require $path;
    }
    $path = dirname(__DIR__) . '/lib/' . $file . '.php';
    if (file_exists($path)) {
        require $path;
    }
});
