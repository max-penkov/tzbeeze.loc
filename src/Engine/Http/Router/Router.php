<?php


namespace Engine\Http\Router;


use Psr\Http\Message\ServerRequestInterface;

class Router implements RouteInterface
{
    /**
     * @var RouteCollection
     */
    private $collection;

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Result|null
     * @throws RouteNotFoundException
     */
    public function matchRoute(ServerRequestInterface $request)
    {
        foreach ($this->collection->getRoutes() as $route) {
            $result = $route->matchRoute($request);

            if ($result){
                return $result;
            }
        }
        throw new RouteNotFoundException($request);
    }

    /**
     * @param $name
     * @param $params
     *
     * @return null|string|string[]
     */
    public function createRoute($name, $params = [])
    {
        foreach ($this->collection->getRoutes() as $route) {
            if ($url = $route->createRoute($name, array_filter($params))){
                return $url;
            }
        }
    }

}