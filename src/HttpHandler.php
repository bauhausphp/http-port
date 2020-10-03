<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpHandler implements RequestHandlerInterface
{
    private RouteDispatcher $dispatcher;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(RouteDispatcher $routerDispatcher, ResponseFactoryInterface $responseFactory)
    {
        $this->dispatcher = $routerDispatcher;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeInfo = $this->dispatcher->dispatch($request);

        if ($routeInfo->notAllowed()) {
            return $this->responseFactory->createResponse(405, 'Method Not Allowed');
        }

        if ($routeInfo->notFound()) {
            return $this->responseFactory->createResponse(404, 'Not Found');
        }

        return $this->executeHandler($routeInfo->getHandler(), $routeInfo->getArguments());
    }

    /**
     * @param mixed[] $arguments
     */
    private function executeHandler(callable $handler, array $arguments): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200, 'OK');
        call_user_func_array($handler, $arguments);

        return $response;
    }
}
