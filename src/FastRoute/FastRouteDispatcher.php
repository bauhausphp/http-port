<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\FastRoute;

use Bauhaus\HttpHandler\EndpointHandlerIsInvalid;
use Bauhaus\HttpHandler\InvalidEndpoint;
use Bauhaus\HttpHandler\RouteDispatcher;
use Bauhaus\HttpHandler\RouteInfo;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use Psr\Http\Message\ServerRequestInterface;

class FastRouteDispatcher implements RouteDispatcher
{
    private const ALLOWED_METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'];

    private Dispatcher $dispatcher;

    /**
     * @param array<string, array<string, mixed>> $routeConfig
     * @throws EndpointHandlerIsInvalid
     * @throws InvalidEndpoint
     */
    public function __construct(array $routeConfig)
    {
        $collector = new RouteCollector(new RouteParser(), new DataGenerator());
        $this->addRoutesFromConfig($routeConfig, $collector);

        $this->dispatcher = new Dispatcher($collector->getData());
    }

    public function dispatch(ServerRequestInterface $request): RouteInfo
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $routeInfo = $this->dispatcher->dispatch($method, $path);

        return new FastRouteInfo($routeInfo);
    }

    /**
     * @param array<string, array<string, mixed>> $routeConfig
     * @throws EndpointHandlerIsInvalid
     * @throws InvalidEndpoint
     */
    private function addRoutesFromConfig(array $routeConfig, RouteCollector $collector): void
    {
        foreach ($routeConfig as $endpoint => $config) {
            $parsed = $this->parseEndpoint($endpoint);
            $handler = $this->getEndpointHandler($config);

            $collector->addRoute($parsed['method'], $parsed['uri'], $handler);
        }
    }

    /**
     * @return array{method: string, uri: string}
     * @throws InvalidEndpoint
     */
    private function parseEndpoint(string $endpoint): array
    {
        $countSpaces = substr_count($endpoint, ' ');

        if ($countSpaces === 0) {
            throw InvalidEndpoint::becauseOfInvalidFormat($endpoint);
        }

        if ($countSpaces > 1) {
            throw InvalidEndpoint::becauseOfMoreThenOneSpace($endpoint, $countSpaces);
        }

        [$method, $uri] = explode(' ', $endpoint);

        if (false === in_array($method, self::ALLOWED_METHODS, true)) {
            throw InvalidEndpoint::becauseOfUnknownOrInvalidMethod($endpoint, $method, self::ALLOWED_METHODS);
        }

        return ['method' => $method, 'uri' => $uri];
    }

    /**
     * @param mixed[] $config
     * @throws EndpointHandlerIsInvalid
     */
    private function getEndpointHandler(array $config): callable
    {
        $handler = $config['handler'] ?? null;

        if (null === $handler) {
            throw EndpointHandlerIsInvalid::becauseItIsMissing();
        }

        if (false === is_callable($handler)) {
            throw EndpointHandlerIsInvalid::becauseItIsNotCallable();
        }

        return $handler;
    }
}
