<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\Unit\FastRoute;

use Bauhaus\HttpHandler\FastRoute\FastRouteHandlerInfo;
use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;

class FastRouteHandlerHandlerInfoTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnNotFound(): void
    {
        $info = new FastRouteHandlerInfo([0 => Dispatcher::NOT_FOUND, 1 => fn () => '', 2 => []]);

        $this->assertTrue($info->handlerNotFound());
        $this->assertFalse($info->handlerNotAllowed());
    }

    /**
     * @test
     */
    public function whenRouteInfoHasNothingThenReturnNotFound(): void
    {
        $info = new FastRouteHandlerInfo([]);

        $this->assertTrue($info->handlerNotFound());
    }

    /**
     * @test
     */
    public function shouldReturnNotAllowed(): void
    {
        $info = new FastRouteHandlerInfo([0 => Dispatcher::METHOD_NOT_ALLOWED, 1 => fn () => '', 2 => []]);

        $this->assertTrue($info->handlerNotAllowed());
        $this->assertFalse($info->handlerNotFound());
    }

    public function testShouldReturnCallable(): void
    {
        $info = new FastRouteHandlerInfo([0 => Dispatcher::FOUND, 1 => fn () => '', 2 => []]);

        $this->assertTrue(is_callable($info->getHandler()));
    }

    public function testShouldReturnArguments(): void
    {
        $info = new FastRouteHandlerInfo([0 => Dispatcher::FOUND, 1 => fn () => '', 2 => ['foo' => 'bar']]);

        $this->assertEquals(['foo' => 'bar'], $info->getArguments());
    }
}
