<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Psr\Http\Message\ServerRequestInterface;

interface HandlerConfig
{
    public function getHandlerInfo(ServerRequestInterface $request): HandlerInfo;
}
