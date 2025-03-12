<?php

namespace Gahshomar;

class Gahshomar_Converter {

    public static function convert_and_display($gy, $gm, $gd) {
        require_once __DIR__ . '/../Calendar/Gahshomar/src/LeapYearChecker.php';
        require_once __DIR__ . '/../Calendar/Gahshomar/src/GregorianToJalaali.php';

        // Convert to Jalaali
        list($jy, $jm, $jd) = GregorianToJalaali::convert($gy, $gm, $gd);

        // Get weekday name
        $weekday = GregorianToJalaali::getWeekdayName($gy, $gm, $gd);

        // Get month names
        $gregorianMonthName = GregorianToJalaali::getGregorianMonthName($gm);
        $jalaaliMonthName = GregorianToJalaali::getJalaaliMonthName($jm);

        // Additional information
        $isLeapYear = LeapYearChecker::isGregorianLeapYear($gy);
        $chineseZodiacAnimal = LeapYearChecker::getChineseZodiacAnimal($gy);
        $chineseZodiacElement = LeapYearChecker::getChineseZodiacElement($gy);
        $hinduYearName = LeapYearChecker::getHinduYearName($jy);
        $birthstone = LeapYearChecker::getBirthstone($gm);
        $westernZodiacSign = LeapYearChecker::getWesternZodiacSign($gm, $gd);
        $zoroastrianDayName = LeapYearChecker::getZoroastrianDayName($jd);

        // Format the Jalaali date
        $jalaaliDate = GregorianToJalaali::formatJalaaliDate($jy, $jm, $jd, $weekday);

        // Display the Jalaali date and additional information
        $output = "Given date in Gregorian calendar: $gy-$gm-$gd ($weekday, $gregorianMonthName)<br>";
        $output .= "Converted date in Jalaali calendar: $jalaaliDate ($jalaaliMonthName)<br>";
        $output .= "Leap Year: " . ($isLeapYear ? 'Yes' : 'No') . "<br>";
        $output .= "Chinese Zodiac: $chineseZodiacAnimal ($chineseZodiacElement)<br>";
        $output .= "Hindu Year Name: $hinduYearName<br>";
        $output .= "Birthstone: $birthstone<br>";
        $output .= "Western Zodiac Sign: $westernZodiacSign<br>";
        $output .= "Zoroastrian Day Name: $zoroastrianDayName<br>";
        return $output;
    }

    public static function convert_jalaali_to_gregorian_and_display($jy, $jm, $jd) {
        require_once __DIR__ . '/../Calendar/Gahshomar/src/LeapYearChecker.php';
        require_once __DIR__ . '/../Calendar/Gahshomar/src/JalaaliToGregorian.php';

        // Convert to Gregorian
        list($gy, $gm, $gd) = JalaaliToGregorian::convert($jy, $jm, $jd);

        // Get weekday name
        $weekday = GregorianToJalaali::getWeekdayName($gy, $gm, $gd);

        // Get month names
        $gregorianMonthName = GregorianToJalaali::getGregorianMonthName($gm);
        $jalaaliMonthName = GregorianToJalaali::getJalaaliMonthName($jm);

        // Additional information
        $isLeapYear = LeapYearChecker::isGregorianLeapYear($gy);
        $chineseZodiacAnimal = LeapYearChecker::getChineseZodiacAnimal($gy);
        $chineseZodiacElement = LeapYearChecker::getChineseZodiacElement($gy);
        $hinduYearName = LeapYearChecker::getHinduYearName($jy);
        $birthstone = LeapYearChecker::getBirthstone($gm);
        $westernZodiacSign = LeapYearChecker::getWesternZodiacSign($gm, $gd);
        $zoroastrianDayName = LeapYearChecker::getZoroastrianDayName($jd);

        // Display the Gregorian date and additional information
        $output = "Given date in Jalaali calendar: $jy-$jm-$jd ($weekday, $jalaaliMonthName)<br>";
        $output .= "Converted date in Gregorian calendar: $gy-$gm-$gd ($gregorianMonthName)<br>";
        $output .= "Leap Year: " . ($isLeapYear ? 'Yes' : 'No') . "<br>";
        $output .= "Chinese Zodiac: $chineseZodiacAnimal ($chineseZodiacElement)<br>";
        $output .= "Hindu Year Name: $hinduYearName<br>";
        $output .= "Birthstone: $birthstone<br>";
        $output .= "Western Zodiac Sign: $westernZodiacSign<br>";
        $output .= "Zoroastrian Day Name: $zoroastrianDayName<br>";
        return $output;
    }

    public static function display_current_date() {
        $currentDate = date('Y-m-d');
        return "Today's date in Gregorian calendar: $currentDate";
    }
}