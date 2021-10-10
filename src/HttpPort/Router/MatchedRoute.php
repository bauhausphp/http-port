<?php

namespace Bauhaus\HttpPort\Router;

use Bauhaus\HttpPort\MatchedRequest;

/**
 * @internal
 */
class MatchedRoute
{
    public function __construct(
        private Route $route,
        private array $pathParams,
    ) {
    }

    public function mapRequest(MatchedRequest $request): object
    {
        return $this->route->entrypoint->mapRequest($request);
    }

    public function getPathParam(string $name): mixed
    {
        return $this->pathParams[$name];
    }
}
