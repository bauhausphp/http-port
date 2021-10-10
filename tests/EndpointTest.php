<?php

namespace Bauhaus;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\EndpointNotParsable;
use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    public function notParsableStrings(): array
    {
        return [
            'empty string' => [''],
            'missing path' => ['GET'],
            'missing method' => ['/path'],
            'missing space between method and path' => ['GET/path'],
            'with too many spaces between method and path #1' => ['GET  /path'],
            'with too many spaces between method and path #2' => ['GET   /path'],
            'unsupported method' => ['INVALID /path'],
            'supported method in lower case' => ['get /path'],
            'path with space' => ['GET /path path'],
        ];
    }

    /**
     * @test
     * @dataProvider notParsableStrings
     */
    public function throwExceptionIfStringCannotBeParsed(string $endpoint): void
    {
        $this->expectException(EndpointNotParsable::class);
        $this->expectExceptionMessage(<<<MSG
            Provided endpoint '$endpoint' is not parsable (expected format is '<METHOD> <PATH>')
            MSG);

        new Endpoint($endpoint);
    }

    public function validEndpointStrings(): array
    {
        return [
            'GET and simple path' => ['GET /path', 'GET', '/path'],
            'GET and double path' => ['GET /path/path', 'GET', '/path/path'],
            'GET and path with dash ' => ['GET /path-path', 'GET', '/path-path'],
            'GET and path with numbers #1' => ['GET /path123', 'GET', '/path123'],
            'GET and path with numbers #2' => ['GET /path1/path2', 'GET', '/path1/path2'],
            'GET and path with parameter' => ['GET /path/{param}', 'GET', '/path/{param}'],
            'POST and simple path' => ['POST /path', 'POST', '/path'],
            'POST and simple path with upper case chars' => ['POST /pAtH', 'POST', '/pAtH'],
            'HEAD and simple path' => ['HEAD /path', 'HEAD', '/path'],
            'PUT and simple path' => ['PUT /path', 'PUT', '/path'],
            'DELETE and simple path' => ['DELETE /path', 'DELETE', '/path'],
            'CONNECT and simple path' => ['CONNECT /path', 'CONNECT', '/path'],
            'OPTIONS and simple path' => ['OPTIONS /path', 'OPTIONS', '/path'],
            'TRACE and simple path' => ['TRACE /path', 'TRACE', '/path'],
            'PATCH and simple path' => ['PATCH /path', 'PATCH', '/path'],
        ];
    }

    /**
     * @test
     * @dataProvider validEndpointStrings
     */
    public function parseEndpointString(string $endpoint, string $method, string $path): void
    {
        $endpoint = new Endpoint($endpoint);

        $this->assertEquals($method, $endpoint->method);
        $this->assertEquals($path, $endpoint->path);
    }
}
