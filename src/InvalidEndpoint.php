<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Exception;

class InvalidEndpoint extends Exception
{
    public static function becauseOfInvalidFormat(string $endpoint): self
    {
        return new self("The endpoint '{$endpoint}' has an invalid format, expected format is '<METHOD> <URI>'");
    }

    public static function becauseOfMoreThenOneSpace(string $endpoint, int $count): self
    {
        return new self("The endpoint '{$endpoint}' must have only 1 space, {$count} found");
    }

    /**
     * @param array<int, string> $allowed
     */
    public static function becauseOfUnknownOrInvalidMethod(string $endpoint, string $method, array $allowed): self
    {
        return new self(
            sprintf(
                "The endpoint '{$endpoint}' has an invalid method, expected is on of (%s), '{$method}' given",
                implode(', ', $allowed),
            )
        );
    }
}
