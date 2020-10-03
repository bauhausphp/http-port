<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;

class RouteInfoTest extends TestCase
{
    /**
     * @test
     */
    public function whenShouldReturnNotFound(): void
    {
        $info = new RouteInfo([0 => Dispatcher::NOT_FOUND, 1 => fn () => '', 2 => []]);

        $this->assertTrue($info->notFound());
    }

    /**
     * @test
     */
    public function whenShouldReturnNotAllowed(): void
    {
        $info = new RouteInfo([0 => Dispatcher::METHOD_NOT_ALLOWED, 1 => fn () => '', 2 => []]);

        $this->assertTrue($info->notAllowed());
    }

    public function testShouldReturnCallable(): void
    {
        $info = new RouteInfo([0 => Dispatcher::FOUND, 1 => fn () => '', 2 => []]);

        $this->assertTrue(is_callable($info->getHandler()));
    }

    public function testShouldReturnArguments(): void
    {
        $info = new RouteInfo([0 => Dispatcher::FOUND, 1 => fn () => '', 2 => ['foo' => 'bar']]);

        $this->assertEquals(['foo' => 'bar'], $info->getArguments());
    }
}
