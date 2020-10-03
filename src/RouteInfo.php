<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

interface RouteInfo
{
    public function notAllowed(): bool;

    public function notFound(): bool;

    public function getHandler(): callable;

    /**
     * @return mixed[]
     */
    public function getArguments(): array;
}
