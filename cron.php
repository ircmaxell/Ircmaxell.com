<?php

require_once 'src/bootstrap.php';

$config = require_once PATH_ROOT . '/config/settings.php';

defined('PROXY') OR define('PROXY', isset($config['proxy']) ? $config['proxy'] : false);

$mysqli = new ircmaxell\com\Sources\MySQLi($config['database']);

$mapper = new ircmaxell\com\DataMappers\Post($mysqli);

foreach ($config['sources'] as $name => $config) {
    $config += array('constructorArgs' => array(), 'frequency' => 0);
    $sql = 'SELECT `value` FROM cron_info WHERE `name` = ?';
    $result = $mysqli->query($sql, array($name . '_last_run'))->fetch_assoc();
    if ($result && $result['value'] + $config['frequency'] > time()) {
        continue;
    }
    $r = new ReflectionClass('\\ircmaxell\\com\\Models\\Source\\' . $name);
    $source = $r->newInstanceArgs($config['constructorArgs']);
    $data = $source->getLatestPosts(0, 10);
    echo "Saving " . count($data) . " Items From $name\n";
    $mapper->saveAll($data);
    $sql = 'INSERT INTO `cron_info` (`name`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);';
    $mysqli->query($sql, array($name . '_last_run', time()));
}
