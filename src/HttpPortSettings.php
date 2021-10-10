<?php

namespace Bauhaus;

use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\Entrypoint\LazyEntrypoint;
use Psr\Container\ContainerInterface as PsrContainer;

class HttpPortSettings
{
    private function __construct(
        public readonly MessageBus $messageBus,
        public readonly ?PsrContainer $psrContainer,
        /** @var HttpEntrypoint[] */ public readonly array $entrypoints,
    ) {
    }

    public static function withMessageBus(MessageBus $messageBus): self
    {
        return new self($messageBus, null, []);
    }

    public function withPsrContainer(PsrContainer $psrContainer): self
    {
        return new self($this->messageBus, $psrContainer, $this->entrypoints);
    }

    public function withEntrypoints(string|HttpEntrypoint ...$entrypoints): self
    {
        $entrypoints = array_map(
            fn (string|HttpEntrypoint $e): HttpEntrypoint => $this->handleEntrypoint($e),
            $entrypoints,
        );

        return new self($this->messageBus, $this->psrContainer, $entrypoints);
    }

    private function handleEntrypoint(string|HttpEntrypoint $e): HttpEntrypoint
    {
        if (is_object($e)) {
            return $e;
        }

        // TODO check if psrContainer was provided

        return new LazyEntrypoint($this->psrContainer, $e);
    }
}
