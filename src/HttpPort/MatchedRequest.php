<?php

namespace Bauhaus\HttpPort;

use Bauhaus\HttpPort\Router\MatchedRoute;

class MatchedRequest
{
    public function __construct(
        private IncomingRequest $incomingRequest,
        private MatchedRoute $matchedRoute,
    ) {
    }

    public function mapToMessage(): object
    {
        return $this->matchedRoute->mapRequest($this);
    }

    public function paramFromPathAsInteger(string $param): int
    {
        return $this->matchedRoute->getPathParam($param);
    }

    public function paramFromPathAsString(string $param): string
    {
        return $this->matchedRoute->getPathParam($param);
    }

    public function paramFromBodyAsInteger(string $param): int
    {
        return $this->incomingRequest->getBodyParam($param);
    }

    public function paramFromBodyAsString(string $param): string
    {
        return $this->incomingRequest->getBodyParam($param);
    }
}
