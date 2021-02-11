<?php

use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

require_once 'vendor/autoload.php';
error_reporting(E_ALL ^ E_DEPRECATED);


SingletonServiceCreator::add(Router::class, new Router);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$files = glob('controllers/*.php');
foreach ($files as $file) {
    if (strpos($file, '.php') != -1) {
        @require_once($file);
    }
}

SingletonServiceCreator::get(Router::class)->run();
