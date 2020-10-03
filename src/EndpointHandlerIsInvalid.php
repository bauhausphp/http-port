<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Exception;

class EndpointHandlerIsInvalid extends Exception
{
    public static function becauseItIsMissing(): self
    {
        return new self('Endpoint handler is missing');
    }

    public static function becauseItIsNotCallable(): self
    {
        return new self('Endpoint handler is not callable');
    }
}
