<?php

namespace Espresso\Http;

use stdClass;

class Router
{
    private array $routes = [];

    public function get(string $route, callable ...$routes): void
    {
        $this->routes['get'] = $this->routes['get'] ?? [];
        $this->routes['get'][$route] = $routes;
    }

    public function post(string $route, callable ...$routes): void
    {
        $this->routes['post'] = $this->routes['post'] ?? [];
        $this->routes['post'][$route] = $routes;
    }

    public function put(string $route, callable ...$routes): void
    {
        $this->routes['put'] = $this->routes['put'] ?? [];
        $this->routes['put'][$route] = $routes;
    }

    public function patch(string $route, callable ...$routes): void
    {
        $this->routes['patch'] = $this->routes['patch'] ?? [];
        $this->routes['patch'][$route] = $routes;
    }

    public function delete(string $route, callable ...$routes): void
    {
        $this->routes['delete'] = $this->routes['delete'] ?? [];
        $this->routes['delete'][$route] = $routes;
    }

    public function getRoute(string $route, string $method = 'get'): ?stdClass
    {
        $route_object = (object) [
            'routes' => null,
            'id' => null,
            'query_string' => null,
        ];

        $method = strtolower($method);

        if (!isset($this->routes[$method])) {
            return null;
        }

        if (isset($this->routes[$method][$route])) {
            $route_object->routes = $this->routes[$method][$route];

            return $route_object;
        }

        if (isset($this->routes[$method]["$route/"])) {
            $route_object->routes = $this->routes[$method]["$route/"];

            return $route_object;
        }

        $uri = explode('?', $route);
        $path = $uri[0] ?? null;
        $route_object->query_string = $uri[1] ?? null;

        if (isset($this->routes[$method][$path])) {
            $route_object->routes = $this->routes[$method][$path];

            return $route_object;
        }

        if (isset($this->routes[$method]["$path/"])) {
            $route_object->routes = $this->routes[$method]["$path/"];

            return $route_object;
        }

        $paths = explode('/', $path);
        $route_object->id = array_splice($paths, count($paths) - 1, 1)[0] ?? null;
        $path_id_sample = implode('/', $paths) . '/:id';

        if (isset($this->routes[$method][$path_id_sample])) {
            $route_object->routes = $this->routes[$method][$path_id_sample];

            if (empty($route_object->id)) {
                $route_object->id = null;
            }

            if (empty($route_object->query_string)) {
                $route_object->query_string = null;
            }
        }

        return $route_object;
    }
}
