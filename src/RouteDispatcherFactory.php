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

        foreach ($this->routeConfig as $key => $config) {
            // avoid multiple spaces between method and uri
            $endpoint = trim((string) preg_replace('/\s+/', ' ', $key));
            [$method, $uri] = explode(' ', $endpoint);
            $handler = $config['handler'] ?? fn () => 'OK';

            $collector->addRoute($method, $uri, $handler);
        }

        return new Dispatcher($collector->getData());
    }
}
