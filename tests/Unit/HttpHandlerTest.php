<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\Unit;

use Bauhaus\HttpHandler\Double\MockResponseFactory;
use Bauhaus\HttpHandler\HandlerConfig;
use Bauhaus\HttpHandler\HandlerInfo;
use Bauhaus\HttpHandler\HttpHandler;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpHandlerTest extends TestCase
{
    /** @var HandlerInfo|MockObject */
    private HandlerInfo $routeInfo;

    /** @var ServerRequestInterface|MockObject */
    private ServerRequestInterface $request;

    private HttpHandler $handler;

    protected function setUp(): void
    {
        $this->routeInfo = $this->createMock(HandlerInfo::class);
        $this->request = $this->createMock(ServerRequestInterface::class);

        $dispatcher = $this->createMock(HandlerConfig::class);
        $dispatcher->method('getHandlerInfo')->willReturn($this->routeInfo);
        $responseFactory = new MockResponseFactory($this->createMock(ResponseInterface::class));

        $this->handler = new HttpHandler($dispatcher, $responseFactory);
    }

    /**
     * @test
     */
    public function whenRouteDoesNotExistThenReturnNotFound(): void
    {
        $this->routeInfo->method('handlerNotFound')->willReturn(true);

        $response = $this->handler->handle($this->request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function whenRouteExistsForADifferentMethodThenReturnNotAllowed(): void
    {
        $this->routeInfo->method('handlerNotAllowed')->willReturn(true);

        $response = $this->handler->handle($this->request);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('Method Not Allowed', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function whenRouteExistsForTheRequestedMethodThenReturnOk(): void
    {
        $this->routeInfo->method('getHandler')->willReturn(fn () => 'OK');

        $response = $this->handler->handle($this->request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function whenRouteIsMatchedThenCallHandlerWithArguments(): void
    {
        $datetime = $this->createMock(DateTimeImmutable::class);
        $this->routeInfo->method('getHandler')->willReturn(fn (string $format) => $datetime->format($format));
        $this->routeInfo->method('getArguments')->willReturn(['format' => 'Y-m-d']);

        $datetime->expects($this->once())
            ->method('format')
            ->with('Y-m-d');

        $this->handler->handle($this->request);
    }

    /**
     * @test
     */
    public function whenHandlerThrowsAnExceptionThenReturnInternalServerError(): void
    {
        $this->routeInfo->method('handlerNotFound')->willReturn(false);
        $this->routeInfo->method('handlerNotAllowed')->willReturn(false);
        $this->routeInfo->method('getHandler')->willReturn(function (): void {
            throw new Exception('something went wrong');
        });

        $response = $this->handler->handle($this->createMock(ServerRequestInterface::class));

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Internal Server Error', $response->getReasonPhrase());
    }
}
