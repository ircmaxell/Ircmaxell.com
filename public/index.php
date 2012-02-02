<?php

require_once dirname(__DIR__) . '/src/bootstrap.php';

$router = new \ircmaxell\com\Router;

$router->addRoute('get', '(^/?$)', function($match, $request, $response) {
    
});
$router->addRoute('*', '(^(.*?)(?:\/(\d+))?$)', function($match, $request, $response) {
    $class = '\\ircmaxell\\com\\Resources\\' . $match[1];
    $id = $match[2];
    if (class_exists($class)) {
        $obj = new $class($id);
        $method = $request->getMethod();
        if (method_exists($obj, $method)) {
            $obj->$method($request, $response);
            return true;
        }
    }
    return false;
});


$router->dispatch(new \ircmaxell\com\Request, new \ircmaxell\com\Response)
    ->render();