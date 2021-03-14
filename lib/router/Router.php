<?php

namespace Lib\router;

use Closure;

class Router
{
    /**
     * @var $routes Route[]
     */
    private array $routes = [];

    public function __construct()
    {
    }

    public function add(string $url, string $method, Closure $callback)
    {
        array_push($this->routes, new Route($url, $method, $callback));
    }

    public function get(string $url, Closure $callback)
    {
        $this->add($url, 'GET', $callback);
    }

    public function post(string $url, Closure $callback)
    {
        $this->add($url, 'POST', $callback);
    }

    public function hasRoute($url, $method){
        foreach ($this->routes as $route) {
            $hasURL = $route->matches($url, $method);
            if ($hasURL != false) {
                return $hasURL;
            }
        }
        return false;
    }

    public function run()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $has = $this->hasRoute($url, $method);
        if ($has) {
            $request = new Request(['GET' => $_GET, 'POST' => $_POST]);
//            echo $request->toString();
            $has['callback'](
                $request,
                $has['values']
            );
        } else {
            $error = 'Are you sure you visited the right page?';
            http_response_code(404);
            include ('views/404.php');
        }
    }
}