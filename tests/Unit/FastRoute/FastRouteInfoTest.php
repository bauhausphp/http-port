<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler\Unit\FastRoute;

use Bauhaus\HttpHandler\FastRoute\FastRouteInfo;
use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;

class FastRouteInfoTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnNotFound(): void
    {
        $info = new FastRouteInfo([0 => Dispatcher::NOT_FOUND, 1 => fn () => '', 2 => []]);

        $this->assertTrue($info->notFound());
        $this->assertFalse($info->notAllowed());
    }

    /**
     * @test
     */
    public function whenRouteInfoHasNothingThenReturnNotFound(): void
    {
        $info = new FastRouteInfo([]);

        $this->assertTrue($info->notFound());
    }

    /**
     * @test
     */
    public function shouldReturnNotAllowed(): void
    {
        $info = new FastRouteInfo([0 => Dispatcher::METHOD_NOT_ALLOWED, 1 => fn () => '', 2 => []]);

        $this->assertTrue($info->notAllowed());
        $this->assertFalse($info->notFound());
    }

    public function testShouldReturnCallable(): void
    {
        $info = new FastRouteInfo([0 => Dispatcher::FOUND, 1 => fn () => '', 2 => []]);

        $this->assertTrue(is_callable($info->getHandler()));
    }

    public function testShouldReturnArguments(): void
    {
        $info = new FastRouteInfo([0 => Dispatcher::FOUND, 1 => fn () => '', 2 => ['foo' => 'bar']]);

        $this->assertEquals(['foo' => 'bar'], $info->getArguments());
    }
}
