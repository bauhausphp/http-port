<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\FastRoute;

use Bauhaus\HttpHandler\HandlerInfo;
use FastRoute\Dispatcher;

class FastRouteHandlerInfo implements HandlerInfo
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

    public function handlerNotAllowed(): bool
    {
        return $this->routeInfo[0] === Dispatcher::METHOD_NOT_ALLOWED;
    }

    public function handlerNotFound(): bool
    {
        $status = $this->routeInfo[0] ?? Dispatcher::NOT_FOUND;

        return $status === Dispatcher::NOT_FOUND;
    }

    public function getHandler(): callable
    {
        return $this->routeInfo[1];
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->routeInfo[2];
    }
}
