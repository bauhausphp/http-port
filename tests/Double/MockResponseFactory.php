<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\Double;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class MockResponseFactory implements ResponseFactoryInterface
{
    /** @var ResponseInterface|MockObject */
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        assert($response instanceof MockObject);
        $this->response = $response;
    }

    /**
     * @inheritDoc
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $this->response->method('getStatusCode')->willReturn($code);
        $this->response->method('getReasonPhrase')->willReturn($reasonPhrase);

        return $this->response;
    }
}
