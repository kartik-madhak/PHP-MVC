<?php

namespace Lib\router;

use Closure;
use JetBrains\PhpStorm\ArrayShape;


class Route
{
    private array $components;
    private string $url;
    private Closure $callback;

    private function splitter($url): array
    {
        $url = trim($url, '/');
        if ($url == '') {
            return [];
        }
        return explode('/', $url);
    }

    public function __construct(string $url, Closure $callback)
    {
        $this->components = $this->splitter($url);
        $this->url = $url;
        $this->callback = $callback;
    }

    public
    function matches($url) {
        $pages = $this->splitter($url);
        $pagesLength = count($pages);
        $values = [];
        if ($pagesLength != count($this->components)) {
            return false;
        }
        for ($i = 0; $i < $pagesLength; ++$i) {
            if (strlen($this->components[$i]) != 0) {
                if ($this->components[$i][0] != '{') {
                    if ($this->components[$i] != $pages[$i]) {
                        return false;
                    }
                } else {
                    $values[trim($this->components[$i], '{}')] = $pages[$i];
                }
            }
        }
        $callback = $this->callback;
        return compact('values', 'callback');
    }
}

class Router
{
    /**
     * @var $routes Route[]
     */
    private array $routes = [];

    public function __construct()
    {
    }

    public function add(string $url, Closure $callback)
    {
        array_push($this->routes, new Route($url, $callback));
    }

    public function hasRoute($url){
        foreach ($this->routes as $route) {
            $hasURL = $route->matches($url);
            if ($hasURL != false) {
                return $hasURL;
            }
        }
        return false;
    }

    public function run()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $has = $this->hasRoute($url);
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