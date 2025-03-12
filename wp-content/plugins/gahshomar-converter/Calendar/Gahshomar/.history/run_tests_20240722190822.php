<?php

require 'vendor/autoload.php';

class TestRunnerWrapper
{
    public static function run()
    {
        // Get today's date
        $today = getdate();
        $gy = $today['year'];
        $gm = $today['mon'];
        $gd = $today['mday'];
        
        // Format the Gregorian date
        $gregorianDate = "$gy-$gm-$gd";

        // Display the Gregorian date
        return "Today's date in Gregorian calendar: $gregorianDate";
    }
}

echo '<pre>' . htmlspecialchars(TestRunnerWrapper::run()) . '</pre>';