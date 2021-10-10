<?php

namespace Bauhaus;

use Bauhaus\HttpPort\MatchedRequest;
use Bauhaus\HttpPort\IncomingRequest;
use Bauhaus\HttpPort\Router\Router;
use Psr\Http\Server\RequestHandlerInterface as PsrServerRequestHandler;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;

class HttpPort //implements PsrServerRequestHandler
{
    private function __construct(
        private MessageBus $messageBus,
        private Router $router,
    ) {
    }

    public static function build(HttpPortSettings $settings): self
    {
        return new self(
            $settings->messageBus,
            Router::fromEntrypoints(...$settings->entrypoints),
        );
    }

    public function handle(PsrServerRequest $psrServerRequest): void
    {
        $matchedRequest = $this->match($psrServerRequest);
        $mappedMessage = $matchedRequest->mapToMessage();
        // TODO map NoMatchingRouteFound exception

        $this->messageBus->dispatch($mappedMessage);
    }

    private function match(PsrServerRequest $psrServerRequest): MatchedRequest
    {
        $incomingRequest = new IncomingRequest($psrServerRequest);
        $matchedRoute = $this->router->match($incomingRequest);

        return new MatchedRequest($incomingRequest, $matchedRoute);
    }
}
