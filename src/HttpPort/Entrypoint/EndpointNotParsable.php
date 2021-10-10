<?php

namespace Bauhaus\HttpPort\Entrypoint;

use InvalidArgumentException;

class EndpointNotParsable extends InvalidArgumentException
{
    public function __construct(string $endpoint)
    {
        parent::__construct(
            "Provided endpoint '$endpoint' is not parsable (expected format is '<METHOD> <PATH>')"
        );
    }
}
