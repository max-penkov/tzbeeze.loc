<?php

namespace Engine\Http\Router;


use Psr\Http\Message\ServerRequestInterface;

class RouteNotFoundException extends \Exception
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct( $message = "Route not found",  $code = 0, null);
        $this->request = $request;
    }

}