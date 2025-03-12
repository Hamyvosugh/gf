<?php

namespace Gahshomar;

class JalaaliToGregorian
{
    public static function convert($jy, $jm, $jd)
    {
        $gy = $jy + 621;
        $days = self::jalaliToDays($jy, $jm, $jd);
        list($gy, $gm, $gd) = self::daysToGregorian($gy, $days);
        return [$gy, $gm, $gd];
    }

    private static function jalaliToDays($jy, $jm, $jd)
    {
        $days = 0;
        for ($i = 1; $i < $jm; $i++) {
            $days += self::jalaliMonthDays($jy, $i);
        }
        $days += $jd;
        return $days;
    }

    private static function jalaliMonthDays($jy, $jm)
    {
        if ($jm <= 6) {
            return 31;
        } elseif ($jm <= 11) {
            return 30;
        } else {
            return (($jy % 33) % 4 - 1 == ($jy > 979) % 33 % 4 - 1) ? 30 : 29;
        }
    }

    private static function daysToGregorian($gy, $days)
    {
        $gregorianMonths = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($i = 0; $i < 12; $i++) {
            if ($i == 1 && self::isLeapYear($gy)) {
                $gregorianMonths[1] = 29;
            }
            if ($days <= $gregorianMonths[$i]) {
                return [$gy, $i + 1, $days];
            }
            $days -= $gregorianMonths[$i];
        }
    }

    private static function isLeapYear($year)
    {
        return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    }
}