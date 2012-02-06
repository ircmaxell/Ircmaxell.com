<?php

require_once dirname(__DIR__) . '/src/bootstrap.php';

$config = require_once PATH_ROOT . '/config/settings.php';

defined('PROXY') OR define('PROXY', isset($config['proxy']) ? $config['proxy'] : false);

$mysqli = new ircmaxell\com\Sources\MySQLi($config['database']);

$postMapper = new ircmaxell\com\DataMappers\Post($mysqli);

$loader = new ircmaxell\com\ResourceLoader($config['resources'], array($mysqli, $postMapper));

$router = new \ircmaxell\com\Router;

$router->addRoute('get', '(^/?$)', function($match, $request, $response) {
    $response->setStatus(200);
    $response->setBody(file_get_contents(__DIR__ . '/assets/templates/index.html'));
    return true;
});

$loader->registerRoutes($router);

try {

    $router->dispatch(new \ircmaxell\com\Request, new \ircmaxell\com\Response)
        ->render();
} catch (Exception $e) {
    $response = new \ircmaxell\com\Response;
    $response->setStatus(500);
    $response->setBody($e->getMessage());
    $response->render();
}
