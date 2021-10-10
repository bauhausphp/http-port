<?php

namespace Bauhaus;

use Bauhaus\HttpPort\Router\NoMatchingRouteFound;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as PsrHttpRequest;
use Psr\Http\Message\StreamInterface as PsrStream;
use Psr\Http\Message\UriInterface as PsrUri;

class HttpPortTest extends TestCase
{
    private HttpPort $httpPort;
    private MessageBus|MockObject $messageBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBus::class);

        $psrContainer = new Doubles\FakePsrContainer([
            Doubles\GetEntrypointB::class => new Doubles\GetEntrypointB(),
            Doubles\PostEntrypointA::class => new Doubles\PostEntrypointA(),
            Doubles\PostEntrypointB::class => new Doubles\PostEntrypointB(),
        ]);

        $settings = HttpPortSettings::withMessageBus($this->messageBus)
            ->withPsrContainer($psrContainer)
            ->withEntrypoints(
                new Doubles\GetEntrypointA(),
                Doubles\GetEntrypointB::class,
                Doubles\PostEntrypointA::class,
                Doubles\PostEntrypointB::class,
            );

        $this->httpPort = HttpPort::build($settings);
    }

    public function endpointsAndBodiesWithExpectedDispatchedMessages(): array
    {
        return [
            ['GET /endpoint-a', '', new Doubles\MessageFromGetEntrypointA()],
            ['GET /endpoint-b', '', new Doubles\MessageFromGetEntrypointB()],
            ['POST /endpoint-a/param/1403', '', new Doubles\MessageFromPostEntrypoint(1403, 'param')],
            [
                'POST /endpoint-b',
                '{ "f1": "param", "f2": 1403 }',
                new Doubles\MessageFromPostEntrypoint(1403, 'param'),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider endpointsAndBodiesWithExpectedDispatchedMessages
     */
    public function dispatchCorrectMessage(string $endpoint, string $body, object $message): void
    {
        $request = $this->stubRequest($endpoint, $body);

        $this->expectMessageToBeDispatched($message);

        $this->httpPort->handle($request);
    }

    /**
     * @test
     */
    public function throwExceptionIfThereIsNoEntrypointMatchingTheRequest(): void
    {
        $request = $this->stubRequest('GET /not-registered');

        $this->expectException(NoMatchingRouteFound::class);
        $this->expectExceptionMessage('Not found for \'GET /not-registered\'');

        $this->httpPort->handle($request);
    }

    /**
     * @test
     */
    public function throwExceptionIfMethodIsNotAllowedForAnMatchingRoute(): void
    {
        $request = $this->stubRequest('POST /endpoint-a');

        $this->expectException(NoMatchingRouteFound::class);
        $this->expectExceptionMessage('Method not allowed for \'POST /endpoint-a\'');

        $this->httpPort->handle($request);
    }

    private function stubRequest(string $endpoint, string $body = ''): PsrHttpRequest
    {
        [$method, $path] = explode(' ', $endpoint);

        $streamStub = $this->createStub(PsrStream::class);
        $streamStub->method('getContents')->willReturn($body);

        $uriStub = $this->createStub(PsrUri::class);
        $uriStub->method('getPath')->willReturn($path);

        $requestStub = $this->createStub(PsrHttpRequest::class);
        $requestStub->method('getBody')->willReturn($streamStub);
        $requestStub->method('getMethod')->willReturn($method);
        $requestStub->method('getUri')->willReturn($uriStub);

        return $requestStub;
    }

    private function expectMessageToBeDispatched(object $message): void
    {
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message);
    }
}
