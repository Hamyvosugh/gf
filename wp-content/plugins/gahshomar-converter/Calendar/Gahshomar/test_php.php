<?php
require_once __DIR__ . '/Calendar/Gahshomar/src/JalaaliToGregorian.php';
require_once __DIR__ . '/Calendar/Gahshomar/src/GregorianToJalaali.php';

// Example conversion and display logic
$gy = -400; // Replace with actual Gregorian year input
$gm = 7; // Replace with actual Gregorian month input
$gd = 12; // Replace with actual Gregorian day input

// Convert Gregorian to Jalaali
list($jy, $jm, $jd) = \Gahshomar\GregorianToJalaali::convert($gy, $gm, $gd);
$jalaaliDate = \Gahshomar\GregorianToJalaali::formatJalaaliDate($jy, $jm, $jd, \Gahshomar\GregorianToJalaali::getWeekdayName($gy, $gm, $gd));

// Convert Jalaali to Gregorian
list($converted_gy, $converted_gm, $converted_gd) = \Gahshomar\JalaaliToGregorian::convert($jy, $jm, $jd);
$gregorianDate = \Gahshomar\GregorianToJalaali::formatGregorianDate($converted_gy, $converted_gm, $converted_gd, \Gahshomar\GregorianToJalaali::getWeekdayName($converted_gy, $converted_gm, $converted_gd));

echo "Gregorian date: " . \Gahshomar\GregorianToJalaali::formatGregorianDate($gy, $gm, $gd, \Gahshomar\GregorianToJalaali::getWeekdayName($gy, $gm, $gd)) . "<br>";
echo "Jalaali date: " . $jalaaliDate . "<br>";
echo "Converted back to Gregorian: " . $gregorianDate . "<br>";
?>