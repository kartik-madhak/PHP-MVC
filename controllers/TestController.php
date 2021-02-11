<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$router->add(
    '/',
    function (Request $request, array $routeValues) {
        include('views/index.php');
    }
);

$router->add(
    '/home',
    function (Request $request, array $routeValues) {
        $inputsFromForms = $request->inputs;

        if (isset($inputsFromForms['GET'])) {
            $res = \Lib\database\User::query()->select()->where('name', $inputsFromForms['GET']['name'])->get();
            if (!isset($res[0])) {
                $error = 'No user named ' . $inputsFromForms['GET']['name'];
                include('views/error.php');
            } else {
                $user = $res[0];
                include('views/home.php');
            }
        }
    }
);



$router->add(
    '/add',
    function (Request $request, array $routeValues) {
        $inputsFromForms = $request->inputs;

        if (isset($inputsFromForms['POST'])){
            {
                $user = new \Lib\database\User();
                $user->name = $inputsFromForms['POST']['name'];
                $user->create();
            }
            $msg = 'User added successfully';
            include ('views/index.php');
        }
    }
);