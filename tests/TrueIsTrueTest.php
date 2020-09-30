<?php

declare(strict_types=1);

namespace Bauhaus\HttpHandler;

use PHPUnit\Framework\TestCase;

class TrueIsTrueTest extends TestCase
{
    public function testTrue(): void
    {
        $sunshine = new ThisIsTrue();

        $this->assertTrue($sunshine->returnTrue());
    }
}
