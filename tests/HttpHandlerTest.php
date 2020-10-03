<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use Bauhaus\HttpHandler\Double\MockResponseFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class HttpHandlerTest extends TestCase
{
    private ResponseFactoryInterface $responseFactory;

    protected function setUp(): void
    {
        $this->responseFactory = new MockResponseFactory($this->createMock(ResponseInterface::class));
    }

    /**
     * @test
     */
    public function whenRouteDoesNotExistThenReturnNotFound(): void
    {
        $dispatcher = new RouteDispatcher([]);
        $handler = new HttpHandler($dispatcher, $this->responseFactory);
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
        $dispatcher = new RouteDispatcher($this->createBasicGetEndpointConfig());
        $handler = new HttpHandler($dispatcher, $this->responseFactory);
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
        $dispatcher = new RouteDispatcher($this->createBasicGetEndpointConfig());
        $handler = new HttpHandler($dispatcher, $this->responseFactory);
        $request = $this->createRequest('GET', '/');

        $response = $handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function whenRouteIsMatchedThenCallHandlerWithArguments(): void
    {
        $datetime = $this->createMock(DateTimeImmutable::class);
        $routeConfig = [
            'GET /datetime/{format}' => [
                'handler' => fn (string $format) => $datetime->format($format),
            ],
        ];

        $dispatcher = new RouteDispatcher($routeConfig);
        $handler = new HttpHandler($dispatcher, $this->responseFactory);
        $request = $this->createRequest('GET', '/datetime/Y-m-d');

        $datetime->expects($this->once())
            ->method('format')
            ->with('Y-m-d');

        $handler->handle($request);
    }

    /**
     * @return array<string, array<string, callable>>
     */
    private function createBasicGetEndpointConfig(): array
    {
        return [
            'GET /' => [
                'handler' => fn () => 'OK',
            ],
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
