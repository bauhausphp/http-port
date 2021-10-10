<?php

namespace Bauhaus\HttpPort;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;

class IncomingRequest
{
    private array $parsedBody;

    public function __construct(
        private PsrServerRequest $psrServerRequest,
    ) {
        // TODO check if body does not exist in GET (and other methods)
        // TODO check if content type is json (header and body)

        $bodyContent = $this->psrServerRequest->getBody()->getContents();

        $this->parsedBody = '' === $bodyContent ? [] : json_decode($bodyContent, true);
    }

    public function method(): string
    {
        return $this->psrServerRequest->getMethod();
    }

    public function path(): string
    {
        return $this->psrServerRequest->getUri()->getPath();
    }

    public function getBodyParam(string $name): mixed
    {
        return $this->parsedBody[$name];
    }
}
