<?php

require_once dirname(__DIR__) . '/src/bootstrap.php';

$router = new \ircmaxell\com\Router;

$router->addRoute('get', '(^/?$)', function($match, $request, $response) {
    $response->setStatus(200);
    $response->setBody(file_get_contents(__DIR__ . '/assets/templates/index.html'));
    return true;
});

$router->addRoute('*', '(^(/[^/]*)*/?$)', function($match, $request, $response) {
    $path = explode('/', trim($match[0], ' /'));
    $args = array();
    while ($path) {
        $class = '\\ircmaxell\\com\\Resources\\' . implode('\\', $path);
        if (class_exists($class) && method_exists($class, $request->getMethod())) {
            $method = $request->getMethod();
            $reflector = new \ReflectionClass($class);
            $obj = $reflector->newInstanceArgs($args);
            $obj->$method($request, $response);
            return true;
        }
        array_unshift($args, array_pop($path));
    }
    return false;
});

try {

    $router->dispatch(new \ircmaxell\com\Request, new \ircmaxell\com\Response)
        ->render();
} catch (Exception $e) {
    $response = new \ircmaxell\com\Response;
    $response->setStatus(500);
    $response->setBody('test');
    $response->render();
}
