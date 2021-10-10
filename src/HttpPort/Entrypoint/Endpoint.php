<?php

namespace Bauhaus\HttpPort\Entrypoint;

use Attribute;

#[Attribute]
class Endpoint
{
    private const METHOD_PATTERN = '(OPTIONS)|(HEAD)|(GET)|(POST)|(DELETE)|(PUT)|(PATCH)|(CONNECT)|(TRACE)';
    private const PATH_PATTERN = '/[a-zA-Z\d\-\/\{\}]*';
    private const PATTERN = '%^(?<method>' . self::METHOD_PATTERN . ') (?<path>' . self::PATH_PATTERN . ')$%';
    private const MATCH_SUCCESS = 1;

    public readonly string $method;
    public readonly string $path;

    public function __construct(string $endpoint)
    {
        $matches = $this->match($endpoint);

        $this->method = $matches['method'];
        $this->path = $matches['path'];
    }

    private function match(string $endpoint): array
    {
        $matches = [];
        $result = preg_match(self::PATTERN, $endpoint, $matches);

        return self::MATCH_SUCCESS === $result ? $matches : throw new EndpointNotParsable($endpoint);
    }
}
