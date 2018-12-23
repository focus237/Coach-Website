<?php

namespace App\Router;


class Router
{

    private $url;
    private $routes = [];
    public $namedRoutes = [];
    public static $controllersPath = "App\\Controller\\";

    public function __construct(string $url = null)
    {
        if (is_null($url)) {
            $this->url = $_SERVER['REQUEST_URI'];
        } else {
            $this->url = $url;
        }
    }

    public function get(string $path, $callable, string $name = null) : Route
    {
        return $this->add($path, $callable, 'GET', $name);
    }

    public function post(string $path, $callable, string $name = null) : Route
    {
        return $this->add($path, $callable, 'POST', $name);
    }

    private function add(string $path, $callable, string $method, string $name = null) : Route
    {
        $route = new Route($this, $path, $callable, self::$controllersPath);
        $this->routes[$method][] = $route;

        if (is_string($callable) && $name === null) {
            $name = $callable;
        }

        if ($name) {
            $this->namedRoutes[$name] = $route;
        }

        return $route;
    }

    public function run()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new RouterException('REQUEST_METHOD does not exist');
        }

        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }

        throw new RouterException('No matching routes');
    }

    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name ' . $name);
        }

        return $this->namedRoutes[$name]->getUrl($params);
    }

    public function redirect(string $name, array $params = []): void
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name ' . $name);
        }

        header('location: ' . $this->namedRoutes[$name]->getUrl($params));
    }

    public static function setDefaultNamespace(string $namespace): void
    {
        self::$controllersPath = $namespace;
    }

}