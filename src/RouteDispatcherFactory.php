<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;

class RouteDispatcherFactory
{
    private const ALLOWED_METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'];

    /** @var array<string, array<string, mixed>> */
    private array $routeConfig;

    /**
     * @param array<string, array<string, mixed>> $routeConfig
     */
    public function __construct(array $routeConfig)
    {
        $this->routeConfig = $routeConfig;
    }

    /**
     * @throws InvalidEndpoint
     */
    public function create(): Dispatcher
    {
        $collector = new RouteCollector(new RouteParser(), new DataGenerator());
        $this->addRoutesFromConfig($collector);

        return new Dispatcher($collector->getData());
    }

    /**
     * @throws InvalidEndpoint
     */
    private function addRoutesFromConfig(RouteCollector $collector): void
    {
        foreach (array_keys($this->routeConfig) as $endpoint) {
            $parsed = $this->parseEndpoint($endpoint);

            $collector->addRoute($parsed['method'], $parsed['uri'], fn () => 'OK');
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
}
