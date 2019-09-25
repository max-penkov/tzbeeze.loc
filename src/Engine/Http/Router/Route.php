<?php

namespace Engine\Http\Router;

use Psr\Http\Message\ServerRequestInterface;

class Route implements RouteInterface
{
    public $name;
    public $pattern;
    public $callback;
    public $tokens;
    public $methods;

    /**
     * Route constructor.
     *
     * @param       $name
     * @param       $pattern
     * @param       $callback
     * @param array $methods
     * @param array $tokens
     */
    public function __construct($name, $pattern, $callback, array $methods, array $tokens = [])
    {
        $this->name     = $name;
        $this->pattern  = $pattern;
        $this->callback = $callback;
        $this->tokens   = $tokens;
        $this->methods  = $methods;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Result|null
     */
    public function matchRoute(ServerRequestInterface $request)
    {
        if ($this->methods && !\in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }
        $pattern = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) {
            $argument = $matches[1];
            $replace  = $this->tokens[$argument] ?? '[^}]*';
            return '(?P<' . $argument . '>' . $replace . ')';
        }, $this->pattern);
        $path    = $request->getUri()->getPath();
        if (!preg_match('~^' . $pattern . '$~i', $path, $matches)) {
            return null;
        }

        return new Result(
            $this->name,
            $this->callback,
            array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY)
        );
    }

    /**
     * @param       $name
     * @param array $params
     *
     * @return null|string|string[]
     */
    public function createRoute($name, $params = [])
    {
        $arguments = array_filter($params);

        if ($name !== $this->name) {
            return null;
        }
        $url = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) use (&$arguments) {
            $argument = $matches[1];
            if ($this->tokens[$argument] != '\s*|\w+') {
                if (!array_key_exists($argument, $arguments)) {
                    throw new \InvalidArgumentException('Missing attribute "' . $argument . '"');
                }
            }
            return $arguments[$argument] ?? '';
        }, $this->pattern);

        return $url;
    }
}