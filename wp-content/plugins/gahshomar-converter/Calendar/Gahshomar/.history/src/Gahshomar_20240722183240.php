<?php

namespace Gahshomar;

class Gahshomar
{
    public static function toJalaali($year, $month, $day)
    {
        return GregorianToJalaali::convert($year, $month, $day);
    }

    public static function toGregorian($year, $month, $day)
    {
        return JalaaliToGregorian::convert($year, $month, $day);
    }
}