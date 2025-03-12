<?php

namespace Gahshomar;

class LeapYearChecker
{
    private static $chinese_zodiac_animals = [
        "موش", "گاو", "ببر", "خرگوش", "اژدها", "مار", "اسب", "بز",
        "میمون", "خروس", "سگ", "خوک"
    ];

    private static $chinese_zodiac_elements = [
        "چوب", "آتش", "زمین", "فلز", "آب"
    ];

    private static $hindu_year_names = [
        "پربهاوا", "ویبهاوا", "شوکلا", "پرامودوتا", "پراجوته‌پاتی", "آنگیراسا", "شریمخا", "باوا", "یووا", 
        "داتا", "ایشوارا", "باهودانیا", "پراماتی", "ویکراما", "وریشاپراجا", "چیترا‌بانو", "سوبهانوی",
        "تارانگا", "پاردهامان", "پرابهادرا", "سوباها", "شوباکریتا", "گروهاپراتی", "ویروپا", "وتسا",
        "جیته", "بهرجا", "شریه", "دانیا", "پرابهاواتی", "ویبهاواتی", "ویجایاپاتا", "پربال", "ویجایی",
        "سیتها", "پاورنا", "پرامودا", "ویروشا", "پراتاپا", "پاپا", "پریتی", "کیرتی", "روچکا",
        "پریما", "آنجانا", "شاراتی", "کرتا", "ویکرتا", "بدری", "کریتی", "ویسوا", "سامی",
        "ویروجا", "کانتا", "پرابهاوا", "سانکوشا", "راغا", "پریمیشتا", "دهادار", "شیبا"
    ];

    private static $birthstones = [
        "گارنت", "آمتیست", "آکوامارین", "الماس", "زمرد", "مروارید/الکساندریت", "یاقوت سرخ",
        "پریدوت", "یاقوت کبود", "اوپال/تورمالین", "توپاز/سیترین", "فیروزه/زیرکن/تانزانیت"
    ];

    private static $western_zodiac_signs = [
        ["حمل", "بره"], ["ثور", "گاو"], ["جوزا", "دوپیکر"], ["سرطان", "خرچنگ"],
        ["اسد", "شیر"], ["سنبله", "دوشیزه"], ["میزان", "ترازو"], ["عقرب", "کژدم"],
        ["قوس", "کمان"], ["جدی", "بز"], ["دلو", "آب‌ریز"], ["حوت", "ماهی"]
    ];

    private static $zoroastrian_day_names = [
        "هرمزد", "بهمن", "اردیبهشت", "شهریور", "اسپندارمذ", "خرداد", "امرداد",
        "دی‌به‌آذر", "آذر", "آبان", "خورشید", "ماه", "تیر", "گوش", "دی‌به‌مهر",
        "مهر", "سروش", "رشن", "فروردین", "بهرام", "رام", "باد", "دی‌به‌دین",
        "دین", "اشیش‌ونگه", "اشتاد", "آسمان", "زمین", "ماه‌سپند", "انغران"
    ];

    public static function isGregorianLeapYear($year)
    {
        return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    }

    public static function isJalaaliLeapYear($year)
    {
        $normalized_year = $year - (($year > 0) ? 474 : 473);
        $cycle_year = $normalized_year % 2820 + 474;
        return ($cycle_year % 33 % 4 == 1);
    }

    public static function getChineseZodiacAnimal($year, $month, $day)
    {
        // Lunar New Year dates for recent years
        $lunarNewYearDates = [
            2023 => '01-22',
            2024 => '02-10',
            2025 => '01-29',
            2026 => '02-17',
            2027 => '02-06',
            2028 => '01-26',
            2029 => '02-13',
            2030 => '02-03',
            // Add more dates as needed
        ];

        if (isset($lunarNewYearDates[$year])) {
            $lunarNewYear = $lunarNewYearDates[$year];
            list($lunarNewYearMonth, $lunarNewYearDay) = explode('-', $lunarNewYear);
            
            if (($month < $lunarNewYearMonth) || ($month == $lunarNewYearMonth && $day < $lunarNewYearDay)) {
                $year--;
            }
        }

        $animal_index = ($year - 4) % 12;
        return self::$chinese_zodiac_animals[$animal_index];
    }

    public static function getChineseZodiacElement($year)
    {
        $element_index = ($year - 4) % 10;
        return self::$chinese_zodiac_elements[$element_index / 2];
    }

    public static function getHinduYearName($year)
    {
        $year_index = $year % 60;
        return self::$hindu_year_names[$year_index];
    }

    public static function getBirthstone($month)
    {
        return self::$birthstones[$month - 1];
    }

    public static function getWesternZodiacSign($month, $day)
    {
        if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) {
            return self::$western_zodiac_signs[0]; // Aries
        } elseif (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
            return self::$western_zodiac_signs[1]; // Taurus
        } elseif (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) {
            return self::$western_zodiac_signs[2]; // Gemini
        } elseif (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) {
            return self::$western_zodiac_signs[3]; // Cancer
        } elseif (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) {
            return self::$western_zodiac_signs[4]; // Leo
        } elseif (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
            return self::$western_zodiac_signs[5]; // Virgo
        } elseif (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) {
            return self::$western_zodiac_signs[6]; // Libra
        } elseif (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) {
            return self::$western_zodiac_signs[7]; // Scorpio
        } elseif (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) {
            return self::$western_zodiac_signs[8]; // Sagittarius
        } elseif (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) {
            return self::$western_zodiac_signs[9]; // Capricorn
        } elseif (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
            return self::$western_zodiac_signs[10]; // Aquarius
        } elseif (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
            return self::$western_zodiac_signs[11]; // Pisces
        }
        return null;
    }

    public static function getZoroastrianDayName($day)
    {
        return self::$zoroastrian_day_names[$day - 1];
    }
}