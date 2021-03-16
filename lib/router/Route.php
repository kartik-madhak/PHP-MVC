<?php

namespace Lib\router;

use Closure;

class Route
{
    // components are thee individual parts of the url separated by '/'. This variable is used for matching two routes.
    private array $components;

    // The original url pointed by the route
    private string $url;

    // 'GET', 'SET', 'POST' etc...
    private string $method;

    // Middlewares to call before executing callback function
    /**
     * @var $actions Closure[]
     */
    private array $actions;

    private function splitter($url): array
    {
        $url = trim($url, '/');
        if ($url == '') {
            return [];
        }
        return explode('/', $url);
    }

    public function __construct(string $url, string $method, array $callbacks)
    {
        $this->components = $this->splitter($url);
        $this->url = $url;
        $this->method = $method;
        $this->actions = $callbacks;
    }

    public
    function matches(
        $url, $method
    ) {
        if ($method !== $this->method)
            return false;
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
        $actions = $this->actions;
        return compact('values', 'actions');
    }
}