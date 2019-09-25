<?php

namespace App\Http\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $delegate = new Next(clone $this->queue, $next);
        return $delegate($request);
    }

    /**
     * @param $middleware
     */
    public function pipe($middleware): void
    {
        $this->queue->enqueue($middleware);
    }
}