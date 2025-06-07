<?php
use core\Cache;
use core\Core;
date_default_timezone_set('Europe/Kiev');
function autoload($className)
{
    $filePath = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($filePath)) {
        require_once $filePath;
    }
}
spl_autoload_register("autoload");


$cache = new Cache(__DIR__ . '/./cache');
Core::get()->setComponent('cache', $cache);

$route = isset($_GET['route']) ? $_GET['route'] : '';

$core = Core::get();
$core->run($route);
$core->done();
