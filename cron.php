<?php

require_once 'src/bootstrap.php';

$config = require_once PATH_ROOT . '/config/settings.php';

defined('PROXY') OR define('PROXY', isset($config['proxy']) ? $config['proxy'] : false);

$mysqli = new ircmaxell\com\Sources\MySQLi($config['database']);

$mapper = new ircmaxell\com\DataMappers\Post($mysqli);

foreach ($config['sources'] as $name => $config) {
    if ($name != 'GooglePlus') continue;
    $r = new ReflectionClass('\\ircmaxell\\com\\Models\\Source\\' . $name);
    $source = $r->newInstanceArgs($config);
    $data = $source->getLatestPosts(0, 100);
    echo "Saving " . count($data) . " Items From $name\n";
    $mapper->saveAll($data);
}
