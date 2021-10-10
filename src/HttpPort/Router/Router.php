<?php

namespace Bauhaus\HttpPort\Router;

use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\IncomingRequest;
use FastRoute\DataGenerator\GroupCountBased as FastRouteDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as FastRouteDispatcher;
use FastRoute\RouteCollector as FastRouteRouteCollection;
use FastRoute\RouteParser\Std as FastRouteRouteParser;

class Router
{
    /** @var Route[] */ private array $routes;

    private function __construct(Route ...$routes)
    {
        $this->routes = $routes;
    }

    public static function fromEntrypoints(HttpEntrypoint ...$entrypoints): self
    {
        return new self(...array_map(
            fn (HttpEntrypoint $e): Route => new Route($e),
            $entrypoints,
        ));
    }

    public function match(IncomingRequest $request): MatchedRoute
    {
        $fastRouteResult = $this->dispatchFastRoute($request);

        return match ($fastRouteResult[0]) {
            FastRouteDispatcher::FOUND => new MatchedRoute($fastRouteResult[1], $fastRouteResult[2]),
            FastRouteDispatcher::NOT_FOUND => throw NoMatchingRouteFound::notFound($request),
            FastRouteDispatcher::METHOD_NOT_ALLOWED => throw NoMatchingRouteFound::methodNotAllowed($request),
        };
    }

    private function dispatchFastRoute(IncomingRequest $request): array
    {
        $collection = $this->buildFastRouteCollection();
        $dispatcher = new FastRouteDispatcher($collection->getData());

        return $dispatcher->dispatch($request->method(), $request->path());
    }

    private function buildFastRouteCollection(): FastRouteRouteCollection
    {
        $collector = new FastRouteRouteCollection(
            new FastRouteRouteParser(),
            new FastRouteDataGenerator(),
        );

        foreach ($this->routes as $route) {
            $collector->addRoute($route->method(), $route->path(), $route);
        }

        return $collector;
    }
}
