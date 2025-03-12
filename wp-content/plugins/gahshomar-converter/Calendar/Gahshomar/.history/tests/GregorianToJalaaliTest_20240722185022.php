<?php

use PHPUnit\Framework\TestCase;
use Gahshomar\Gahshomar;

class GregorianToJalaaliTest extends TestCase
{
    public function testConversion()
    {
        $result = Gahshomar::toJalaali(2021, 3, 21);
        $this->assertEquals([1400, 1, 1], $result);
    }
}