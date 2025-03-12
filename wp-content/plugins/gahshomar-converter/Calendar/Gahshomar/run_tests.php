<?php

require 'vendor/autoload.php';

use Gahshomar\GregorianToJalaali;
use Gahshomar\JalaaliToGregorian;
use Gahshomar\LeapYearChecker;

class TestRunnerWrapper
{
    public static function run($gy, $gm, $gd, $calendar)
    {
        // Convert to Jalaali
        list($jy, $jm, $jd) = GregorianToJalaali::convert($gy, $gm, $gd);

        // Get month names
        $g_month_name = GregorianToJalaali::getGregorianMonthName($gm);
        $j_month_name = GregorianToJalaali::getJalaaliMonthName($jm);

        // Get weekday name
        $weekday = GregorianToJalaali::getWeekdayName($gy, $gm, $gd);

        // Check for leap year
        $is_g_leap = LeapYearChecker::isGregorianLeapYear($gy) ? " (Leap Year)" : "";
        $is_j_leap = LeapYearChecker::isJalaaliLeapYear($jy) ? " (کبیسه)" : "";

        // Get Chinese zodiac
        $chinese_zodiac_animal = LeapYearChecker::getChineseZodiacAnimal($gy);
        $chinese_zodiac_element = LeapYearChecker::getChineseZodiacElement($gy);
        $chinese_zodiac = "$chinese_zodiac_element $chinese_zodiac_animal";

        // Get Hindu year name
        $hindu_year_name = LeapYearChecker::getHinduYearName($gy);

        // Get birthstone
        $birthstone = LeapYearChecker::getBirthstone($gm);

        // Get Western zodiac sign
        list($western_zodiac_sign, $western_zodiac_symbol) = LeapYearChecker::getWesternZodiacSign($gm, $gd);

        // Get Zoroastrian day name
        $zoroastrian_day_name = LeapYearChecker::getZoroastrianDayName($gd);

        // Format the Jalaali date
        $jalaaliDate = GregorianToJalaali::formatJalaaliDate($jy, $jm, $jd, $weekday);

        // Inform the user about potential inaccuracies for older dates
        $note = ($gy < 1600) ? "\nNote: The conversion for dates before 1600 is based on a logical algorithm and may not be scientifically accurate." : "";

        // Custom Calendar Conversion
        $customYear = GregorianToJalaali::convertFromJalaali($jy, $calendar);
        $customCalendarLabel = $calendar;
        $customCalendarDate = "$customYear-$jm-$jd";

        // Display the Jalaali date
        return <<<EOT
Given date in Gregorian calendar: $gy-$gm-$gd ($g_month_name)$is_g_leap
Converted date in Jalaali calendar: $jalaaliDate$is_j_leap
Converted date in $customCalendarLabel calendar: $customCalendarDate
Chinese Zodiac: $chinese_zodiac
Hindu Year Name: $hindu_year_name
Birthstone for $g_month_name: $birthstone
Western Zodiac Sign: $western_zodiac_sign ($western_zodiac_symbol)
Zoroastrian Day Name: $zoroastrian_day_name
$note
EOT;
    }
}

$gy = isset($_GET['gy']) ? (int)$_GET['gy'] : date('Y');
$gm = isset($_GET['gm']) ? (int)$_GET['gm'] : date('m');
$gd = isset($_GET['gd']) ? (int)$_GET['gd'] : date('d');
$calendar = isset($_GET['calendar']) ? $_GET['calendar'] : 'شاهنشاهی';

echo '<pre>' . htmlspecialchars(TestRunnerWrapper::run($gy, $gm, $gd, $calendar)) . '</pre>';