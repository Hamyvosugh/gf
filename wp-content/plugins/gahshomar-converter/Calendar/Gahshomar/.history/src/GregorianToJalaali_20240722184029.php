<?php

namespace Gahshomar;

class GregorianToJalaali
{
    public static function convert($gy, $gm, $gd)
    {
        $jy = $gy - 621;
        $days = self::gregorianToDays($gy, $gm, $gd);
        list($jy, $jm, $jd) = self::daysToJalali($jy, $days);
        return [$jy, $jm, $jd];
    }

    private static function gregorianToDays($gy, $gm, $gd)
    {
        $days = 0;
        for ($i = 0; $i < $gm - 1; $i++) {
            $days += self::gregorianMonthDays($gy, $i + 1);
        }
        $days += $gd;
        return $days;
    }

    private static function gregorianMonthDays($gy, $gm)
    {
        $gregorianMonths = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if ($gm == 2 && self::isLeapYear($gy)) {
            return 29;
        }
        return $gregorianMonths[$gm - 1];
    }

    private static function daysToJalali($jy, $days)
    {
        for ($i = 1; $i <= 12; $i++) {
            $monthDays = self::jalaliMonthDays($jy, $i);
            if ($days <= $monthDays) {
                return [$jy, $i, $days];
            }
            $days -= $monthDays;
        }
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

    private static function isLeapYear($year)
    {
        return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    }
}