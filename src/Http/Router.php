<?php

namespace Espresso\Http;

class Router
{
    private array $routes = [];

    public function get(string $route, callable $callback): void
    {
        $this->routes['get'] = $this->routes['get'] ?? [];
        $this->routes['get'][$route] = $callback;
    }

    public function post(string $route, callable $callback): void
    {
        $this->routes['post'] = $this->routes['post'] ?? [];
        $this->routes['post'][$route] = $callback;
    }

    public function put(string $route, callable $callback): void
    {
        $this->routes['put'] = $this->routes['put'] ?? [];
        $this->routes['put'][$route] = $callback;
    }

    public function patch(string $route, callable $callback): void
    {
        $this->routes['patch'] = $this->routes['patch'] ?? [];
        $this->routes['patch'][$route] = $callback;
    }

    public function delete(string $route, callable $callback): void
    {
        $this->routes['delete'] = $this->routes['delete'] ?? [];
        $this->routes['delete'][$route] = $callback;
    }

    public function getRoute(string $route, string $method = 'get'): ?callable
    {
        $method = strtolower($method);

        if (isset($this->routes[$method]) && isset($this->routes[$method][$route])) {
            return $this->routes[$method][$route];
        }

        return null;
    }
}
