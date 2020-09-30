<?php

declare(strict_types=1);

namespace App\Test;

use App\ThisIsTrue;
use PHPUnit\Framework\TestCase;

class TrueIsTrueTest extends TestCase
{
    public function testTrue(): void
    {
        $sunshine = new ThisIsTrue();

        $this->assertTrue($sunshine->returnTrue());
    }
}
