<?php

namespace Gahshomar;

class MadiToGregorian {

    const MADI_YEAR_OFFSET = 1321;

    private static $jalaali_month_names = [
        "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور",
        "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند"
    ];

    private static $gregorian_month_names = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    private static $weekdays = [
        "مهر شید (یکشنبه)", "مه شید (دوشنبه)", "بهرام شید (سه شنبه)", "تیرشید (چهار شنبه)", "هرمزشید (پنج شنبه)", "ناهید شید (آدینه)", "کیوان شید (شنبه)"
    ];

    public static function convert($my, $mm, $md) {
        $jy = $my - self::MADI_YEAR_OFFSET;
        list($gy, $gm, $gd) = JalaaliToGregorian::convert($jy, $mm, $md);
        return [$gy, $gm, $gd];
    }

    public static function getWeekdayName($gy, $gm, $gd) {
        return JalaaliToGregorian::getWeekdayName($gy, $gm, $gd);
    }

    public static function formatGregorianDate($gy, $gm, $gd, $weekday) {
        $month_name = self::getGregorianMonthName($gm);
        $year = abs($gy);
        $formatted_date = "$weekday $gd $month_name $year";
        if ($gy < 0) {
            $formatted_date .= " سال پیش از میلادی";
        }
        return $formatted_date;
    }

    public static function getGregorianMonthName($monthNumber) {
        return self::$gregorian_month_names[$monthNumber - 1];
    }
}