<?php

namespace Gahshomar;

class JalaaliToGregorian
{
    const CALENDARS = [
        'شاهنشاهی' => 1180,
        'ایلامی' => 3821,
        'زرتشتی' => 2359,
        'کردی' => 1321,
        'ایران نو' => -1396
    ];

    private static $jalaali_month_names = [
        "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور",
        "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند"
    ];

    private static $gregorian_month_names = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    private static $gregorian_month_names_persian = [
        "ژانویه", "فوریه", "مارس", "آوریل", "مه", "ژوئن",
        "ژوئیه", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"
    ];

    public static function convert($jy, $jm, $jd)
    {
        if ($jy >= 979) {
            return self::modernJalaaliToGregorian($jy, $jm, $jd);
        } else {
            return self::ancientJalaaliToGregorian($jy, $jm, $jd);
        }
    }

    private static function modernJalaaliToGregorian($jy, $jm, $jd)
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $jy -= 979;
        $jm -= 1;
        $jd -= 1;

        $j_day_no = 365 * $jy + floor($jy / 33) * 8 + floor(($jy % 33 + 3) / 4);

        for ($i = 0; $i < $jm; ++$i) {
            $j_day_no += $j_days_in_month[$i];
        }

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * floor($g_day_no / 146097);
        $g_day_no %= 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100 * floor($g_day_no / 36524);
            $g_day_no %= 36524;

            if ($g_day_no >= 365) {
                $g_day_no++;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * floor($g_day_no / 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;
            $g_day_no--;
            $gy += floor($g_day_no / 365);
            $g_day_no %= 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++) {
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        }

        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return [$gy, $gm, $gd];
    }

    private static function ancientJalaaliToGregorian($jy, $jm, $jd)
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy = $jy + 621;
        if ($jm < 4 || ($jm == 4 && $jd < 21)) {
            $gy--;
        }

        $j_day_of_year = $jd;
        for ($i = 0; $i < $jm - 1; ++$i) {
            $j_day_of_year += $j_days_in_month[$i];
        }

        $g_day_of_year = $j_day_of_year + 79;
        if ($g_day_of_year > 365) {
            $g_day_of_year -= 365;
            $gy++;
        }

        $gm = 0;
        while ($g_day_of_year > $g_days_in_month[$gm]) {
            $g_day_of_year -= $g_days_in_month[$gm];
            $gm++;
        }
        $gd = $g_day_of_year;

        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            if ($gm == 2 && $gd == 29) {
                $gm++;
                $gd = 1;
            } else if ($gm > 2) {
                $gd++;
            }
        }

        return [$gy, $gm + 1, $gd];
    }

    public static function getJalaaliMonthName($monthNumber)
    {
        return self::$jalaali_month_names[$monthNumber - 1];
    }

    public static function getGregorianMonthName($monthNumber)
    {
        return self::$gregorian_month_names[$monthNumber - 1];
    }

    public static function getGregorianMonthNamePersian($monthNumber)
    {
        return self::$gregorian_month_names_persian[$monthNumber - 1];
    }

    public static function formatGregorianDate($gy, $gm, $gd)
    {
        $month_name = self::getGregorianMonthNamePersian($gm);
        return "$gd $month_name $gy";
    }

    // New Methods for Additional Calendars

    public static function convertFromJalaali($jy, $calendar) {
        if (!isset(self::CALENDARS[$calendar])) {
            throw new \Exception("Unsupported calendar: $calendar");
        }
        return $jy + self::CALENDARS[$calendar];
    }

    public static function convertToJalaali($year, $calendar) {
        if (!isset(self::CALENDARS[$calendar])) {
            throw new \Exception("Unsupported calendar: $calendar");
        }
        return $year - self::CALENDARS[$calendar];
    }
    
    public static function formatCustomCalendarDate($year, $month, $day, $calendar) {
        $jalaaliDate = self::convertToJalaali($year, $calendar);
        list($jy, $jm, $jd) = $jalaaliDate;
        $month_name = self::getJalaaliMonthName($jm);
        return "$jd $month_name $jy";
    }
}