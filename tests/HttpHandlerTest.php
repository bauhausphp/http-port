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
    private ResponseFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new MockResponseFactory($this->createMock(ResponseInterface::class));
    }

    public function testWhenRouteIsNotFoundThenThrowException(): void
    {
        $dispatcher = (new RouteDispatcherFactory())->create();
        $handler = new HttpHandler($dispatcher, $this->factory);
        $request = $this->createRequest('GET', '/');

        $response = $handler->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
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
