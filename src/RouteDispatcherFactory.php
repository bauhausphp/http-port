<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;

class RouteDispatcherFactory
{
    /** @var array<string, array<string, mixed>> */
    private array $routeConfig;

    /**
     * @param array<string, array<string, mixed>> $routeConfig
     */
    public function __construct(array $routeConfig)
    {
        $this->routeConfig = $routeConfig;
    }

    public function create(): Dispatcher
    {
        $collector = new RouteCollector(
            new RouteParser(),
            new DataGenerator(),
        );

        foreach (array_keys($this->routeConfig) as $endpoint) {
            [$method, $uri] = explode(' ', $endpoint);

            $collector->addRoute($method, $uri, fn () => 'OK');
        }

        return new Dispatcher($collector->getData());
    }
}
