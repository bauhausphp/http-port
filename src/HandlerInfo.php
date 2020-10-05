<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

interface HandlerInfo
{
    public function handlerNotAllowed(): bool;

    public function handlerNotFound(): bool;

    public function getHandler(): callable;

    /**
     * @return mixed[]
     */
    public function getArguments(): array;
}
