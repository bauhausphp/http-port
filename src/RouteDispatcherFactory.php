<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;

class RouteDispatcherFactory
{
    public function create(): Dispatcher
    {
        $routeCollector = new RouteCollector(
            new RouteParser(),
            new DataGenerator(),
        );

        return new Dispatcher($routeCollector->getData());
    }
}
