<?php

namespace Engine\Http\Router;


use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    public function matchRoute(ServerRequestInterface $request);

    public function createRoute($name, $params = []);

}