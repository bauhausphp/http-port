<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class HttpHandler implements RequestHandlerInterface
{
    private HandlerConfig $routeConfig;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(HandlerConfig $routeConfig, ResponseFactoryInterface $responseFactory)
    {
        $this->routeConfig = $routeConfig;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $info = $this->routeConfig->getHandlerInfo($request);

        if ($info->handlerNotAllowed()) {
            return $this->responseFactory->createResponse(405, 'Method Not Allowed');
        }

        if ($info->handlerNotFound()) {
            return $this->responseFactory->createResponse(404, 'Not Found');
        }

        try {
            return $this->executeRouteHandler($info->getHandler(), $info->getArguments());
        } catch (Throwable $e) {
            return $this->handleException();
        }
    }

    /**
     * @param mixed[] $arguments
     */
    private function executeRouteHandler(callable $handler, array $arguments): ResponseInterface
    {
        call_user_func_array($handler, $arguments);

        return $this->responseFactory->createResponse(200, 'OK');
    }

    private function handleException(): ResponseInterface
    {
        return $this->responseFactory->createResponse(500, 'Internal Server Error');
    }
}
