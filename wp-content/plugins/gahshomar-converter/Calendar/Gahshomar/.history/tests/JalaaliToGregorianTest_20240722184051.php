<?php

use PHPUnit\Framework\TestCase;
use Gahshomar\Gahshomar;

class JalaaliToGregorianTest extends TestCase
{
    public function testConversion()
    {
        $result = Gahshomar::toGregorian(1400, 1, 1);
        $this->assertEquals([2021, 3, 21], $result);
    }
}