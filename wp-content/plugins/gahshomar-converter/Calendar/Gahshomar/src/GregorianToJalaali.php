<?php
namespace Gahshomar;

class GregorianToJalaali {

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

    private static $weekdays = [
        "مهر شید (یکشنبه)", "مه شید (دوشنبه)", "بهرام شید (سه شنبه)", "تیرشید (چهار شنبه)", "هرمزشید (پنج شنبه)", "ناهید شید (آدینه)", "کیوان شید (شنبه)"
    ];

    public static function convert($gy, $gm, $gd) {
        if ($gy >= 1600) {
            return self::modernGregorianToJalaali($gy, $gm, $gd);
        } else {
            return self::ancientGregorianToJalaali($gy, $gm, $gd);
        }
    }

    private static function modernGregorianToJalaali($gy, $gm, $gd) {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy -= 1600;
        $gm -= 1;
        $gd -= 1;
    
        $g_day_no = 365 * $gy + floor(($gy + 3) / 4) - floor(($gy + 99) / 100) + floor(($gy + 399) / 400);
    
        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days_in_month[$i];
        }
    
        if ($gm > 1 && LeapYearChecker::isGregorianLeapYear($gy + 1600)) {
            $g_day_no++;
        }
    
        $g_day_no += $gd;
    
        $j_day_no = $g_day_no - 79;
    
        $j_np = floor($j_day_no / 12053);
        $j_day_no %= 12053;
    
        $jy = 979 + 33 * $j_np + 4 * floor($j_day_no / 1461);
    
        $j_day_no %= 1461;
    
        if ($j_day_no >= 366) {
            $jy += floor(($j_day_no - 1) / 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }
    
        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
            $j_day_no -= $j_days_in_month[$i];
        }
        $jm = $i + 1;
        $jd = $j_day_no + 1;
    
        return [$jy, $jm, $jd];
    }
    
    private static function ancientGregorianToJalaali($gy, $gm, $gd) {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    
        $jy = $gy - 621;
        if ($gm > 3 || ($gm == 3 && $gd >= 21)) {
            $jy++;
        }
    
        $g_day_of_year = $gd;
        for ($i = 0; $i < $gm - 1; ++$i) {
            $g_day_of_year += $g_days_in_month[$i];
        }
    
        if ($gm > 2 && LeapYearChecker::isGregorianLeapYear($gy)) {
            $g_day_of_year++;
        }
    
        $j_day_of_year = $g_day_of_year - 79;
        if ($j_day_of_year <= 0) {
            $j_day_of_year += 365;
            if (LeapYearChecker::isJalaaliLeapYear($jy - 1)) {
                $j_day_of_year++;
            }
            $jy--;
        }
    
        $jm = 0;
        while ($j_day_of_year > $j_days_in_month[$jm]) {
            $j_day_of_year -= $j_days_in_month[$jm];
            $jm++;
        }
        $jd = $j_day_of_year;
    
        return [$jy, $jm + 1, $jd];
    }
    
    public static function getJalaaliMonthName($monthNumber) {
        return self::$jalaali_month_names[$monthNumber - 1];
    }
    
    public static function getGregorianMonthName($monthNumber) {
        return self::$gregorian_month_names[$monthNumber - 1];
    }
    
    public static function getWeekdayName($gy, $gm, $gd) {
        $date = \DateTime::createFromFormat('Y-m-d', "$gy-$gm-$gd");
        $weekday_index = $date->format('w');
        return self::$weekdays[$weekday_index];
    }
    
    public static function formatJalaaliDate($jy, $jm, $jd, $weekday) {
        $month_name = self::getJalaaliMonthName($jm);
        return "$weekday $jd $month_name $jy";
    }
    
    public static function formatGregorianDate($gy, $gm, $gd) {
        $month_name = self::getGregorianMonthName($gm);
        $year = abs($gy);
        $formatted_date = "$gd $month_name $year";
        return $formatted_date;
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
    
    public static function formatCustomCalendarDate($year, $month, $day, $calendar, $weekday) {
        $jalaaliDate = self::convertToJalaali($year, $calendar);
        list($jy, $jm, $jd) = $jalaaliDate;
        return self::formatJalaaliDate($jy, $jm, $jd, $weekday);
    }
}