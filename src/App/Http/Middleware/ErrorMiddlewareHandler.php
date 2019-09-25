<?php

namespace App\Http\Middleware;


use DomainException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class ErrorMiddlewareHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param callable               $next
     *
     * @return JsonResponse
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return $next($request);
        } catch (DomainException $e) {
            return new JsonResponse([
                'error'   => 'error',
                'code'    => $e->getCode(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return new JsonResponse([
                'error'   => 'error',
                'code'    => $e->getCode(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}