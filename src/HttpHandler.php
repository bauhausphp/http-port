<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased as RouteDispatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpHandler implements RequestHandlerInterface
{
    private RouteDispatcher $routerDispatcher;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(RouteDispatcher $routerDispatcher, ResponseFactoryInterface $responseFactory)
    {
        $this->routerDispatcher = $routerDispatcher;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $routeInfo = $this->routerDispatcher->dispatch($method, $path);

        switch ($routeInfo[0]) {
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = $this->responseFactory->createResponse(405, 'Method Not Allowed');

                break;
            case Dispatcher::FOUND:
                $response = $this->executeHandler($routeInfo[1], $routeInfo[2]);

                break;
            default:
                $response = $this->responseFactory->createResponse(404, 'Not Found');
        }

        return $response;
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
