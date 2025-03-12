<?php

require 'vendor/autoload.php';

class TestRunnerWrapper
{
    public static function run()
    {
        // Get today's date
        $today = getdate();
        $gy = $today['year'];
        $gm = str_pad($today['mon'], 2, '0', STR_PAD_LEFT); // Pad month with leading zero
        $gd = str_pad($today['mday'], 2, '0', STR_PAD_LEFT); // Pad day with leading zero
        
        // Format the Gregorian date
        $gregorianDate = "$gy-$gm-$gd";

        // Return the Gregorian date
        return "Today's date in Gregorian calendar: $gregorianDate";
    }
}

echo '<pre>' . htmlspecialchars(TestRunnerWrapper::run()) . '</pre>';