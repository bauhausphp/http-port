<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use FastRoute\Dispatcher;

class RouteInfo
{
    /** @var mixed[] */
    private array $routeInfo;

    /**
     * @param mixed[] $routeInfo
     */
    public function __construct(array $routeInfo)
    {
        $this->routeInfo = $routeInfo;
    }

    public function notAllowed(): bool
    {
        return $this->routeInfo[0] === Dispatcher::METHOD_NOT_ALLOWED;
    }

    public function notFound(): bool
    {
        $status = $this->routeInfo[0] ?? Dispatcher::NOT_FOUND;

        return $status === Dispatcher::NOT_FOUND;
    }

    public function getHandler(): callable
    {
        return $this->routeInfo[1];
    }

    /**
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->routeInfo[2];
    }
}
