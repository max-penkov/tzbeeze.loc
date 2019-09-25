<?php

namespace App\Http\Middleware;

use App\Http\Pipeline\MiddlewareResolver;
use Engine\Http\Router\RouteNotFoundException;
use Engine\Http\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class RouteMiddleware
{
    private $router;
    private $resolver;

    public function __construct(Router $router, MiddlewareResolver $resolver)
    {
        $this->router = $router;
        $this->resolver = $resolver;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable               $next
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            $result = $this->router->matchRoute($request);
            foreach ($result->getAttributes() as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }
            $middleware = $this->resolver->resolve($result->getHandler());
            return $middleware($request, $next);
        } catch (RouteNotFoundException $e){
            return $next($request);
        }
    }
}