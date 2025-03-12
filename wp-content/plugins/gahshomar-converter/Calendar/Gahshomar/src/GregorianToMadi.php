<?php

namespace Gahshomar;

class GregorianToMadi {

    const MADI_YEAR_OFFSET = 1321;

    private static $jalaali_month_names = [
        "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور",
        "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند"
    ];

    private static $weekdays = [
        "مهر شید (یکشنبه)", "مه شید (دوشنبه)", "بهرام شید (سه شنبه)", "تیرشید (چهار شنبه)", "هرمزشید (پنج شنبه)", "ناهید شید (آدینه)", "کیوان شید (شنبه)"
    ];

    public static function convert($gy, $gm, $gd) {
        list($jy, $jm, $jd) = GregorianToJalaali::convert($gy, $gm, $gd);
        $jy += self::MADI_YEAR_OFFSET;
        return [$jy, $jm, $jd];
    }

    public static function getWeekdayName($gy, $gm, $gd) {
        return GregorianToJalaali::getWeekdayName($gy, $gm, $gd);
    }

    public static function formatMadiDate($jy, $jm, $jd, $weekday) {
        $month_name = self::getJalaaliMonthName($jm);
        return "$weekday $jd $month_name $jy سال کردی";
    }

    public static function getJalaaliMonthName($monthNumber) {
        return self::$jalaali_month_names[$monthNumber - 1];
    }
}