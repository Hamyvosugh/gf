<?php

namespace Gahshomar;

class Gahshomar
{
    private static $months = [
        "فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", 
        "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند"
    ];
    
    private static $days = [
        "شنبه", "یک‌شنبه", "دو‌شنبه", "سه‌شنبه", 
        "چهارشنبه", "پنج‌شنبه", "جمعه"
    ];

    public static function toJalaali($year, $month, $day)
    {
        return GregorianToJalaali::convert($year, $month, $day);
    }

    public static function toGregorian($year, $month, $day)
    {
        return JalaaliToGregorian::convert($year, $month, $day);
    }

    public static function getMonthName($month)
    {
        return self::$months[$month - 1];
    }

    public static function getDayName($day)
    {
        return self::$days[$day - 1];
    }

    public static function formatJalaali($year, $month, $day)
    {
        $monthName = self::getMonthName($month);
        return "$day $monthName $year";
    }

    public static function formatGregorian($year, $month, $day)
    {
        return "$year-$month-$day";
    }
}