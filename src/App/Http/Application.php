<?php


namespace App\Http;


use App\Http\Pipeline\MiddlewareResolver;
use App\Http\Pipeline\Pipeline;
use Psr\Http\Message\ServerRequestInterface;

class Application extends Pipeline
{
    private $resolver;
    /**
     * @var callable
     */
    private $default;

    public function __construct(MiddlewareResolver $resolver, callable $default)
    {
        parent::__construct();
        $this->resolver = $resolver;
        $this->default = $default;
    }

    /**
     * @param callable $middleware
     */
    public function pipe($middleware): void
    {
        parent::pipe($this->resolver->resolve($middleware));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function run(ServerRequestInterface $request)
    {
        return $this($request, $this->default);
    }
}