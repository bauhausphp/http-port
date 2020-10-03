<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Psr\Http\Message\ServerRequestInterface;

interface RouteDispatcher
{
    public function dispatch(ServerRequestInterface $request): RouteInfo;
}
