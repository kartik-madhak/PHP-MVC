<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$authMiddleware = function (Request $request, array $routeValues) {
    echo 'Testing auth middleware... <br>';
    echo 'PROGRAMMED TO BE ALWAYS SUCCESSFUL!!!';
    // return false if don't want to continue to next middleware.
};

$router->get(
    '/',
    [
        function (Request $request, array $routeValues) {
            $data = Router::getRedirectedData();
            if ($data) {
                extract($data); // Get data if any sent thru redirect...
            }

            include('views/index.php');
        }
    ]
);

$router->get(
    '/home',
    [
        $authMiddleware, // Write the middlewares here which are functions which you would like to call before
        // executing the main functionality of the route.
        function (Request $request, array $routeValues) {
            $inputsFromForms = $request->inputs['GET'];    // get inputs submitted thru form.

            $no_of_users = count(User::query()->select()->get());
            // User::query()->select()->get() returns an array...

            $user = new User;
            $user->name = 'SomeName_' . $no_of_users;
            $user->email = 'someEmail@gmail.com';
            $user->password = 'HASHED_PASSWORD_EXAMPLE';
            $user->create();

            /*
             * If you are creating new data, you do not need to assign $user->id since it will be automatically
             * assigned by the mysql database. Also, you do not need to assign $user->created_at and $user->updated_at
             * fields, they are automatically assigned too. After assigning necessary fields, you should call
             * $user->create() to create a new entry and $user->save() to update existing entry with id $user->id
             * */

            include('views/home.php');  // display file 'home.php'
        }
    ]
);

$router->post(
    '/home',
    [
        function (Request $request, array $routeValues) {
            $msg = 'POST REQUEST SUCCESSFUL'; // Extra variables if you want to declare...
            Router::redirect('/', compact('msg')); // Used to redirect to an already existing get route.
        }
    ]
);

$router->get(
    '/testingAjax',
    [
        function (Request $request, array $routeValues) {
            $msg = 'WORKING!!!';
            echo json_encode(compact('msg'));
        }
    ]
);

$router->get(
    '/user-migration', // Add routes like this for debugging purposes
    [
        function (Request $request, array $routeValues) {
            User::drop();       // drops `users` table from mysql database.
            User::createTable(); // creates fresh `users` table from mysql database as per User model.
            echo 'SUCCESS!, SEE YOUR MYSQL TABLES...';
        }
    ]
);

