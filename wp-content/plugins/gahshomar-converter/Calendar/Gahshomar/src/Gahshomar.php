<?php

namespace Gahshomar;

class Gahshomar
{
    private static $months = [
        "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور", 
        "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند"
    ];
    
    private static $days = [
        "کیوان روز", "مهر روز", "مه روز", "بهرام روز", 
        "تیرروز", "اورمزدروز", "ناهید روز"
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

    // New methods for additional calendars

    public static function toCustomCalendar($year, $month, $day, $calendar)
    {
        $jalaaliDate = self::toJalaali($year, $month, $day);
        $customYear = GregorianToJalaali::convertFromJalaali($jalaaliDate[0], $calendar);
        return [$customYear, $jalaaliDate[1], $jalaaliDate[2]];
    }

    public static function fromCustomCalendar($year, $month, $day, $calendar)
    {
        $jalaaliYear = GregorianToJalaali::convertToJalaali($year, $calendar);
        return self::toGregorian($jalaaliYear, $month, $day);
    }

    public static function formatCustomCalendar($year, $month, $day, $calendar)
    {
        $jalaaliDate = GregorianToJalaali::convertToJalaali($year, $calendar);
        return self::formatJalaali($jalaaliDate, $month, $day);
    }
}