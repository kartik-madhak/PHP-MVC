<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$authMiddleware = function (Request $request, array $routeValues) {
    echo 'Testing auth middleware... <br>';
    echo 'PROGRAMMED TO BE ALWAYS SUCCESSFUL!!!';
};
    $router->get(
        '/',
        [
            function (Request $request, array $routeValues) {
                include('views/index.php');
            }
        ]
    );

    $router->get(
        '/home',
        [
            $authMiddleware,
            function (Request $request, array $routeValues) {
                $inputsFromForms = $request->inputs;

                if (isset($inputsFromForms['GET'])) {
                    include('views/home.php');
                }
            }
        ]
    );

    $router->post(
        '/home',
        [
            function (Request $request, array $routeValues) {
                $msg = 'POST REQUEST SUCCESSFUL';
                include('views/index.php');
            }
        ]
    );

    $router->get(
        '/testingAjax',
        [
            function (Request $request, array $routeValues) {
                echo json_encode(['data' => 'IT SEEMS TO BE WORKING FINE!']);
            }
        ]
    );

    $router->get(
        '/migration',
        [
            function (Request $request, array $routeValues) {
                include('views/migrationHandler.php');
            }
        ]
    );

