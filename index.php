<?php

use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

require_once 'vendor/autoload.php';
error_reporting(E_ALL ^ E_DEPRECATED);

function includeFiles($directoryPath)
{
    $files = glob($directoryPath . '/*.php');
    foreach ($files as $file) {
        if (strpos($file, '.php') != -1) {
            require_once($file);
        }
    }
}
SingletonServiceCreator::add(Router::class);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

includeFiles(__DIR__ . '/models');
includeFiles(__DIR__ . '/controllers');

SingletonServiceCreator::get(Router::class)->run();
