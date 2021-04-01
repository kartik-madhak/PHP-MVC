# PHP MVC

Very minimal MVC framework for PHP (version 7.4 and above) with support for MySQL and APACHE server.

## NOTE

- Not tested formally. Definitely contains many bugs.
- Add a `.env` file according to the `.env.example` format.
- Changing the contents of `index.php` (in the root folder) is NOT RECOMMENDED.
- Help by contributing to this code!

## How to use

- Clone this repo into your workspace, run `composer install` inside the cloned directory.
- Point a virtual host in the directory where you have cloned the project and open it in the browser to run.

## About

- This is an MVC framework written for and in PHP with MySQL database support which makes developing websites much easier.
- This section is intended to provide information regarding the overall framework's structure and its usage.

### Models

- `Models` directory contains models which are nothing but classes which describe an instance of your database tables. For example, if you have 'users' table in your database, then `User` model would contain all the fields required to describe the table.

- All the models are required to extend the abstract class `Model` which is declared in the `Lib\database\Model` namespace. This provides the models access to `FluentDB` ORM which makes database operations easy.

- The name of the models must be singular, and the corresponding table's name must be the 'model name' with 's' at the end. See `createTable()` method below if you want to automatically generate the table.

- The models do not need to declare `id`, `created_at`, and `updated_at` fields as they are automatically included in the `Model` parent class.

- The fields declared in your models **must be public** and **must have a type hint** (currently only supports `int`, `string`, and `float`).

- There are several methods in the Model parent class that can be accessed by the child class that aids in database operations: -

  -  `public static function query(): FluentDB` returns a `FluentDB` object for that model which helps conduct various database operations.
  -  `public static function createTable(): bool` automatically creates a table for that model in your MySQL database based upon the type hints of the fields as declared inside the model. The created table has a name of 'model name' + 's'.
  -  `public static function drop()` drops the table from the MySQL database.
  -  `public function create(): void` creates a new entry in the model's table from an 'instance' of the model. No need to specify `id` (as it is autoincrement), `created_at`, and `updated_at` (since they are automatically filled).
  -  `public function save(): void` updates the existing entry in the table from an instance whose `id` is specified explicitly along with the new values of other fields.

- An example for a `User` model class would be: -

  ```php
  use Lib\database\Model;
  
  class User extends Model{
      public string $name;
      public int $roll_no;
      public string $password;
      public string $email;
  }
  ```

### Controllers

- Controllers are PHP files that declare routes and their functionality. They use the `Router` instance created by `SingletonServiceCreator` to ensure that there is only one instance of `Router` throughout the entire application. Hence, the controllers contain the following code at the beginning: -

  ```php
  use Lib\services\SingletonServiceCreator;
  
  /** @var Router $router */
  $router = SingletonServiceCreator::get(Router::class);
  ```

- This router instance is used to handle different types of requests (currently only supports 'GET' and 'POST' requests) using the following syntax: -

  ```php
  $router->get( 			// or post
      '/',				// url of the route
      [					// array of functions to be executed when this route is visited.
          function (Request $request, array $routeValues) {
              // stuff
          },
          function (Request $request, array $routeValues) {
              // stuff
          }
      ]
  );
  ```

  Whenever the route's url is requested, the router executes the functions specified in the 'array of functions' argument from top to bottom. If any of the function returns false, the execution is stopped.

  Notice the argument list accepted by the functions - `Request $request, array $routeValues`. The Request object (currently) just filters and stores various forms of Inputs (like from `$_GET`, `$_POST`, and `$_FILES`) using `htmlspecialchars()` function.

  Hence, any input should be accessed using this request object only. For example: -

  ```php
  $address = $request->inputs['POST']['address'];
  ```

  The second parameter, `$routeValues` specify the value of variables passed in the variable urls. To declare variable urls, you just need to include curly bracket around the variable name inside the route url: -

  ```php
  $router->post( 			
      '/users/{userId}',				
      [
          function (Request $request, array $routeValues) {
              $userId = $routeValues['userId']; 
              // For example, if the requested url is 'users/1', then $userId would be 1.
          }
      ]
  );
  ```

  It is important that you do not name a variable route similar to a static route. For example, a route - '/users/home' would conflict with a route - '/users/{userId}'. This would cause unpredictable behavior. Also note that a get, and a post route can have the same name since the request method is different.

- This is an example of a typical route that you would declare in a controller: -

  ```php
  $authMiddleware = function (Request $request, array $routeValues) {
  	// check authentication of users
      if ($notAuthenticated)
      {
          echo 'NOT AUTHENTICATED';
          return false;
      }
  };
  
  $router->get(
      '/home',
      [
          $authMiddleware, // functions can be stored into variables and passed as arguments like this. 
          // The functions which are executed before the main functionality of the route are termed as middlewares.
          function (Request $request, array $routeValues) {
              $inputsFromForms = $request->inputs['GET'];    
  
              $user = new User;
              $user->name = 'SomeName';
              $user->email = 'someEmail@gmail.com';
              $user->password = 'HASHED_PASSWORD_EXAMPLE';
              $user->create();
  
              include('views/home.php');  // display file 'home.php'
              // Note that the variables declared in this scope are available inside the home.php file.
          }
      ]
  );
  ```

- The Router class also contains a static method called `redirect()` which redirects the execution of current route to another route. It has an optional parameter which can be used to pass data between routes. The passed data can be accessed by the static method `getRedirectedData()`. In the background, it uses flashing of session to persist the data. Example showing this would be: -

  ```php
  $router->get(
      '/home',
      [
          function (Request $request, array $routeValues) {
  			// check for user auth
              if($notAuthenticated)
              {
                  $msg = 'NOT LOGGED IN. PLEASE LOGIN TO CONTINUE';
  				// redirect to '/login' route with '$msg' variable
                  Router::redirect('/login', compact('msg'));
              }
          }
      ]
  );
       
  $router->get(
      '/login',
      [
          function (Request $request, array $routeValues) {
              $data = Router::getRedirectedData();
              if ($data) {
                  extract($data);
  	            // '$msg' is accessible here
              }
              include('views/login.php');
          }
      ]
  );
  ```



### Views

- Views are the files that are rendered in the backend and served to the user during a request. These are PHP files that serve dynamic HTML content at runtime.

- An example of a view file that makes use of the '/home' route declared above would be: -

  ```php+html
  <html lang="en">
  <?php include('layout/head.php') ?> // For including common content across multiple view files, you put them in different layout files.
  <body>
  <h1>This is the home page</h1>
  <p>
      <?php
      echo 'Welcome ' . $user->name . '!<br>';
      ?>
  </p>
  <a href="/">Back</a>
  
  </body>
  </html>
  ```

### FluentDB

- This framework communicates with the database using a small one-file class called FluentDB, which acts as a wrapper that generates, executes, and process SQL queries. I have plans to extend the functionality of the framework using PDO, but that's a thought for future...

- You wouldn't directly declare a FluentDB object in your app, but it is accessible through Model's subclasses using the `query` method. It is used to execute a query on a table specified by a certain model. For example: -

  ```php
  $users = User::query()->select(['id', 'name'])->get();
  ```

  would return all the users in the 'users' table as array where each element is a dictionary having key-values representing `User` class's fields and their corresponding values. Thus, it can be used like: -

  ```php
  foreach($users as $user){
      echo 'user id = ' . $user['id'] . ', name = ' . $user['name'] . '<br>';
  }
  ```

- Apart from executing 'SELECT' queries, it can also 'INSERT' and 'UPDATE' entries in a table. However, the insertion and deletion should be ideally handled by the `create()` and `save()` methods in the model class to avoid errors and to make it more intuitive.

- The sub query can also be formed using `where()` and `orWhere()` methods, which apply condition to the query using the where operator. Currently, only supports '=' operator. For example: -

  ```php
  $myUser = User::query()->select()->where('id', 1)->get();
  ```

- After forming a query, it can be executed and its result can be returned using the `get()` or `getFirstOrFalse()` method. `get()` method always returns an array while `getFirstOrFalse()` would return the first element of array or `false` if array is empty.

- Note that multiple where conditions can be appended like this: -

  ```php
  $myUser = User::query()->select()
      ->where('id', 1)
      ->where('name', 'testUser')
      ->get();
  ```

### AJAX

- There is no special functionality for handling AJAX, but this framework makes it much easier to handle complicated AJAX requests which involve user authentication, database fetching, etc.
- This makes it easier to build Single Page Applications or API endpoints using this framework.

## Features

- Router that supports variables routes.
- Models extend abstract class `Lib\database\Model` which contains an ORM for MYSQL (called FluentDB) for very easy database operations.
- Dynamic views using PHP.
- Minimal in size.
- Very easy to use.
- Good for building Single Page Applications, API endpoints, and dynamic website.

## Future plans

- Extend the functionality of FluentDB to support more queries. Also add more operators in conditional queries.

- Replace mysqli with PDO.

- Add support for multiple data types. Possibly, also change the way Models have to be declared (since type hinting is required right now).

- Add more functionality to `Request` and `Router`. Adding routes at runtime?

- Improve the namespace's naming convention.

- Support more routes like 'PATCH', 'DELETE', etc. (Possibly add functionality to support custom routes).

- Add support for Nginx server (maybe).

- Add PHP scripts to automate migration, generation of model, and controllers (maybe).

    
