<?php

namespace Framework\Router;


class Router
{
    public array $list = [];
    public function __construct() {}

    public function setRoutes(array $route) {
        list($method, $uri, $controller, $action) = $route;
        $this->list[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($uri, $controller, $action) {
        $method = 'GET';
        $route = [$method,$uri,$controller,$action];
        $this->setRoutes($route);
    }

    public function post($uri, $controller, $action) {
        $method = 'POST';
        $route = [$method,$uri,$controller,$action];
        $this->setRoutes($route);
    }

    public function put($uri, $controller, $action) {
        $method = 'PUT';
        $route = [$method,$uri,$controller,$action];
        $this->setRoutes($route);
    }

    public function delete($uri, $controller, $action) {
        $method = 'DELETE';
        $route = [$method,$uri,$controller,$action];
        $this->setRoutes($route);
    }

    public function patch($uri, $controller, $action) {
        $method = 'PATCH';
        $route = [$method,$uri,$controller,$action];
        $this->setRoutes($route);
    }

    public function route($uri)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];


        foreach ($this->list as $route) {

            $uriSegments = explode('/', trim($uri, '/'));

            $routeSegments = explode('/', trim($route['uri'], '/'));

            $match = true;

            if (count($uriSegments) === count($routeSegments) && $route['method'] == $requestMethod) {

                $params = [];
                $match = true;

                for ($i=0; $i < count($uriSegments); $i++) {
                    if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }

                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }

                if ($match) {
                    $controller = "Framework\\Controllers\\{$route['controller']}";
                    $controllerMethod = $route['action'];

                    $controllerInstance = new $controller();
                    return $controllerInstance->$controllerMethod($params);
                }
            }
        }
        return "Route not found";
    }
}

