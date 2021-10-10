<?php

namespace Bauhaus\HttpPort\Entrypoint;

use Bauhaus\HttpPort\MatchedRequest;
use Psr\Container\ContainerInterface as PsrContainer;

/**
 * @internal
 */
class LazyEntrypoint implements HttpEntrypoint
{
    public function __construct(
        private PsrContainer $psrContainer,
        public readonly string $className,
    ) {
        // TODO validate serviceId is HttpEntrypoint
        // TODO validate psrContainer has serviceId
    }

    public function mapRequest(MatchedRequest $request): object
    {
        return $this->loadEntrypoint()->mapRequest($request);
    }

    private function loadEntrypoint(): HttpEntrypoint
    {
        return $this->psrContainer->get($this->className);
    }
}
