<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Bauhaus\HttpHandler\Double\MockResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class HttpHandlerTest extends TestCase
{
    private const GET_SLASH_ENDPOINT = [
        'GET /' => [],
    ];

    private ResponseFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new MockResponseFactory($this->createMock(ResponseInterface::class));
    }

    /**
     * @test
     */
    public function whenRouteDoesNotExistThenReturnNotFound(): void
    {
        $dispatcher = (new RouteDispatcherFactory([]))->create();
        $handler = new HttpHandler($dispatcher, $this->factory);
        $request = $this->createRequest('GET', '/');

        $response = $handler->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function whenRouteExistsForADifferentMethodThenReturnNotAllowed(): void
    {
        $dispatcher = (new RouteDispatcherFactory(self::GET_SLASH_ENDPOINT))->create();
        $handler = new HttpHandler($dispatcher, $this->factory);
        $request = $this->createRequest('POST', '/');

        $response = $handler->handle($request);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('Method Not Allowed', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function whenRouteExistsForTheRequestedMethodThenReturnOk(): void
    {
        $dispatcher = (new RouteDispatcherFactory(self::GET_SLASH_ENDPOINT))->create();
        $handler = new HttpHandler($dispatcher, $this->factory);
        $request = $this->createRequest('GET', '/');

        $response = $handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
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
