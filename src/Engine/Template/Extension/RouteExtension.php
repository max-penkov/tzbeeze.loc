<?php

namespace Engine\Template\Extension;

use Engine\Http\Router\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouteExtension extends AbstractExtension
{
    private $router;

    /**
     * RouteExtension constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'generatePath']),
        ];
    }

    /**
     * @param       $name
     * @param array $params
     *
     * @return string
     */
    public function generatePath($name, array $params = []): string
    {
        return $this->router->createRoute($name, $params);
    }
}
