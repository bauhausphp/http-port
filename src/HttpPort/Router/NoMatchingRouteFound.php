<?php

namespace Bauhaus\HttpPort\Router;

use Bauhaus\HttpPort\IncomingRequest;
use RuntimeException;

/**
 * @internal
 */
class NoMatchingRouteFound extends RuntimeException
{
    private function __construct(string $message, IncomingRequest $request)
    {
        parent::__construct("$message for '{$request->method()} {$request->path()}'");
    }

    public static function methodNotAllowed(IncomingRequest $request): self
    {
        return new self('Method not allowed', $request);
    }

    public static function notFound(IncomingRequest $request): self
    {
        return new self('Not found', $request);
    }
}
