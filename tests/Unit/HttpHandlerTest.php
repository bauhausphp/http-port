<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\Unit;

use Bauhaus\HttpHandler\Double\MockResponseFactory;
use Bauhaus\HttpHandler\FastRoute\FastRouteDispatcher;
use Bauhaus\HttpHandler\HttpHandler;
use Bauhaus\HttpHandler\RouteDispatcher;
use Bauhaus\HttpHandler\RouteInfo;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class HttpHandlerTest extends TestCase
{
    /** @var RouteInfo|MockObject */
    private RouteInfo $routeInfo;

    private ResponseFactoryInterface $responseFactory;
    private HttpHandler $handler;

    protected function setUp(): void
    {
        $this->routeInfo = $this->createMock(RouteInfo::class);
        $dispatcher = $this->createMock(RouteDispatcher::class);
        $dispatcher->method('dispatch')->willReturn($this->routeInfo);
        $this->responseFactory = new MockResponseFactory($this->createMock(ResponseInterface::class));

        $this->handler = new HttpHandler($dispatcher, $this->responseFactory);
    }

    /**
     * @test
     */
    public function whenRouteDoesNotExistThenReturnNotFound(): void
    {
        $dispatcher = new FastRouteDispatcher([]);
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
        $dispatcher = new FastRouteDispatcher($this->createBasicGetEndpointConfig());
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
        $dispatcher = new FastRouteDispatcher($this->createBasicGetEndpointConfig());
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

        $dispatcher = new FastRouteDispatcher($routeConfig);
        $handler = new HttpHandler($dispatcher, $this->responseFactory);
        $request = $this->createRequest('GET', '/datetime/Y-m-d');

        $datetime->expects($this->once())
            ->method('format')
            ->with('Y-m-d');

        $handler->handle($request);
    }

    /**
     * @test
     */
    public function whenHandlerThrowsAnExceptionThenReturnInternalServerError(): void
    {
        $this->routeInfo->method('notFound')->willReturn(false);
        $this->routeInfo->method('notAllowed')->willReturn(false);
        $this->routeInfo->method('getHandler')->willReturn(function (): void {
            throw new Exception('something went wrong');
        });

        $response = $this->handler->handle($this->createMock(ServerRequestInterface::class));

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Internal Server Error', $response->getReasonPhrase());
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
