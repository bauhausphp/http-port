<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\Unit\FastRoute;

use Bauhaus\HttpHandler\EndpointHandlerIsInvalid;
use Bauhaus\HttpHandler\FastRoute\FastRouteHandlerConfig;
use Bauhaus\HttpHandler\InvalidEndpoint;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class FastRouteHandlerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function whenHandlerMissingThenThrowException(): void
    {
        $config = ['GET /' => []];

        $this->expectException(EndpointHandlerIsInvalid::class);
        $this->expectExceptionMessage('Endpoint handler is missing');

        new FastRouteHandlerConfig($config);
    }

    /**
     * @test
     */
    public function whenHandlerIsNotCallableThenThrowException(): void
    {
        $config = ['GET /' => ['handler' => 'not callable']];

        $this->expectException(EndpointHandlerIsInvalid::class);
        $this->expectExceptionMessage('Endpoint handler is not callable');

        new FastRouteHandlerConfig($config);
    }

    /**
     * @test
     * @dataProvider invalidEndpointDataprovider
     */
    public function whenGivenEndpointIsInvalidThenThrowException(string $endpoint, string $message): void
    {
        $config = [$endpoint => []];

        $this->expectException(InvalidEndpoint::class);
        $this->expectExceptionMessage($message);

        new FastRouteHandlerConfig($config);
    }

    /**
     * @test
     * @dataProvider validEndpointDataprovider
     */
    public function whenGivenEndpointIsValidThenMatchEndpoint(string $endpoint, string $method, string $uri): void
    {
        $config = [$endpoint => ['handler' => fn () => 'OK']];
        $dispatcher = new FastRouteHandlerConfig($config);
        $request = $this->createRequest($method, $uri);

        $info = $dispatcher->getHandlerInfo($request);

        $this->assertEquals('OK', call_user_func($info->getHandler()));
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function invalidEndpointDataprovider(): array
    {
        $methods = 'GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH';

        return [
            'empty' => [
                'endpoint' => '',
                'message' => "The endpoint '' has an invalid format, expected format is '<METHOD> <URI>'",
            ],
            'missing method' => [
                'endpoint' => '/foo/bar',
                'message' => "The endpoint '/foo/bar' has an invalid format, expected format is '<METHOD> <URI>'",
            ],
            'missing uri' => [
                'endpoint' => 'GET',
                'message' => "The endpoint 'GET' has an invalid format, expected format is '<METHOD> <URI>'",
            ],
            'invalid config' => [
                'endpoint' => ' GET /foo/bar ',
                'message' => "The endpoint ' GET /foo/bar ' must have only 1 space, 3 found",
            ],
            'invalid invalid method get' => [
                'endpoint' => 'get /',
                'message' => "The endpoint 'get /' has an invalid method, expected is on of ({$methods}), 'get' given",
            ],
            'invalid invalid method XXX' => [
                'endpoint' => 'XXX /',
                'message' => "The endpoint 'XXX /' has an invalid method, expected is on of ({$methods}), 'XXX' given",
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function validEndpointDataprovider(): array
    {
        return [
            'GET' => ['endpoint' => 'GET /', 'method' => 'GET', 'uri' => '/'],
            'HEAD' => ['endpoint' => 'HEAD /', 'method' => 'HEAD', 'uri' => '/'],
            'POST' => ['endpoint' => 'POST /', 'method' => 'POST', 'uri' => '/'],
            'PUT' => ['endpoint' => 'PUT /', 'method' => 'PUT', 'uri' => '/'],
            'DELETE' => ['endpoint' => 'DELETE /', 'method' => 'DELETE', 'uri' => '/'],
            'CONNECT' => ['endpoint' => 'CONNECT /', 'method' => 'CONNECT', 'uri' => '/'],
            'OPTIONS' => ['endpoint' => 'OPTIONS /', 'method' => 'OPTIONS', 'uri' => '/'],
            'TRACE' => ['endpoint' => 'TRACE /', 'method' => 'TRACE', 'uri' => '/'],
            'PATCH' => ['endpoint' => 'PATCH /', 'method' => 'PATCH', 'uri' => '/'],
            'GET param' => ['endpoint' => 'GET /foo/{bar}', 'method' => 'GET', 'uri' => '/foo/abc-123'],
            'HEAD param' => ['endpoint' => 'HEAD /foo/{bar}', 'method' => 'HEAD', 'uri' => '/foo/abc-123'],
            'POST param' => ['endpoint' => 'POST /foo/{bar}', 'method' => 'POST', 'uri' => '/foo/abc-123'],
            'PUT param' => ['endpoint' => 'PUT /foo/{bar}', 'method' => 'PUT', 'uri' => '/foo/abc-123'],
            'DELETE param' => ['endpoint' => 'DELETE /foo/{bar}', 'method' => 'DELETE', 'uri' => '/foo/abc-123'],
            'CONNECT param' => ['endpoint' => 'CONNECT /foo/{bar}', 'method' => 'CONNECT', 'uri' => '/foo/abc-123'],
            'OPTIONS param' => ['endpoint' => 'OPTIONS /foo/{bar}', 'method' => 'OPTIONS', 'uri' => '/foo/abc-123'],
            'TRACE param' => ['endpoint' => 'TRACE /foo/{bar}', 'method' => 'TRACE', 'uri' => '/foo/abc-123'],
            'PATCH param' => ['endpoint' => 'PATCH /foo/{bar}', 'method' => 'PATCH', 'uri' => '/foo/abc-123'],
        ];
    }

    private function createRequest(string $method, string $path): ServerRequestInterface
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn($path);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uri);

        return $request;
    }
}
