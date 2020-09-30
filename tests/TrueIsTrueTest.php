<?php

declare(strict_types=1);

namespace App\Test;

use PHPUnit\Framework\TestCase;

class TrueIsTrueTest extends TestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }
}
