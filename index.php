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
?>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<?php
SingletonServiceCreator::add(Router::class, new Router);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

includeFiles('models');
includeFiles('controllers');

SingletonServiceCreator::get(Router::class)->run();
