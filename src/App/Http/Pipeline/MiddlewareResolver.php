<?php

namespace App\Http\Pipeline;


use Engine\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareResolver
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container){

        $this->container = $container;
    }
    /**
     * @param $handler
     *
     * @return callable
     */
    public function resolve($handler): callable
    {
        if (\is_array($handler)) {
            return $this->createPipe($handler);
        }

        if (\is_string($handler) && $this->container->has($handler)) {
            return function (ServerRequestInterface $request, callable $next) use ($handler) {
                $middleware = $this->resolve($this->container->get($handler));
                return $middleware($request, $next);
            };
        }

        if (\is_string($handler)) {
            return function (ServerRequestInterface $request, callable $next) use ($handler) {
                $object = new $handler();
                return $object($request, $next);
            };
        }

        return $handler;
    }

    /**
     * @param array $handlers
     *
     * @return Pipeline
     */
    private function createPipe(array $handlers): Pipeline
    {
        $pipeline = new Pipeline();
        foreach ($handlers as $handler) {
            $pipeline->pipe($this->resolve($handler));
        }
        return $pipeline;
    }
}