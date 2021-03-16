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

    public function add(string $url, string $method, array $callbacks)
    {
        array_push($this->routes, new Route($url, $method, $callbacks));
    }

    public function get(string $url, array $callbacks)
    {
        $this->add($url, 'GET', $callbacks);
    }

    public function post(string $url, array $callbacks)
    {
        $this->add($url, 'POST', $callbacks);
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
            $count = count($has['actions']);
            $i = 0;
            while($i < $count)
            {
                $actions = $has['actions'][$i];
                $ret = $actions($request, $has['values']);
                if ($ret === false)
                    break;
                $i++;
            }
        } else {
            $error = 'Are you sure you visited the right page?';
            http_response_code(404);
            include ('views/404.php');
        }
    }
}