<?php

namespace Engine\Http\Router;


class RouteCollection
{
    private $routes = [];

    /**
     * @param RouteInterface $route
     */
    public function addRoute(RouteInterface $route)
    {
        $this->routes[] = $route;
    }

    /**
     * @param       $name
     * @param       $pattern
     * @param       $callback
     * @param array $methods
     * @param array $tokens
     */
    public function add($name, $pattern, $callback, array $methods, array $tokens = [])
    {
        $this->addRoute(new Route($name, $pattern, $callback, $methods, $tokens));
    }

    /**
     * @param       $name
     * @param       $pattern
     * @param       $callback
     * @param array $tokens
     */
    public function get($name, $pattern, $callback, array $tokens = [])
    {
        $this->addRoute(new Route($name, $pattern, $callback, ['GET'], $tokens));
    }

    /**
     * @param       $name
     * @param       $pattern
     * @param       $callback
     * @param array $tokens
     */
    public function post($name, $pattern, $callback, array $tokens = [])
    {
        $this->addRoute(new Route($name, $pattern, $callback, ['POST'], $tokens));
    }

    /**
     * @param       $name
     * @param       $pattern
     * @param       $callback
     * @param array $tokens
     */
    public function any($name, $pattern, $callback, array $tokens = [])
    {
        $this->addRoute(new Route($name, $pattern, $callback, [], $tokens));
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}