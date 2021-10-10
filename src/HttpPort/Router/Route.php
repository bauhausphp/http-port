<?php

namespace Bauhaus\HttpPort\Router;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\Entrypoint\LazyEntrypoint;
use ReflectionClass;

class Route
{
    private Endpoint $endpoint;

    public function __construct(
        public readonly HttpEntrypoint $entrypoint,
    ) {
        $this->extractAttributes();
    }

    public function method(): string
    {
        return $this->endpoint->method;
    }

    public function path(): string
    {
        return $this->endpoint->path;
    }

    private function extractAttributes(): void
    {
        $rClass = $this->createReflectionClass();

        $this->endpoint = $rClass->getAttributes(Endpoint::class)[0]->newInstance();
    }

    private function createReflectionClass(): ReflectionClass
    {
        if ($this->entrypoint instanceof LazyEntrypoint) {
            return new ReflectionClass($this->entrypoint->className);
        }

        return new ReflectionClass($this->entrypoint);
    }
}
