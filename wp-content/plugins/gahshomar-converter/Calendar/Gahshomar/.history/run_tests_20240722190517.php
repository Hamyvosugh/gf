<?php

require 'vendor/autoload.php';

class TestRunnerWrapper
{
    public static function run()
    {
        $output = '';
        ob_start();
        try {
            // Get today's date
            $today = getdate();
            $gy = $today['year'];
            $gm = $today['mon'];
            $gd = $today['mday'];
            
            // Format the Gregorian date
            $gregorianDate = "$gy-$gm-$gd";

            // Display the Gregorian date
            $output = "Today's date in Gregorian calendar: $gregorianDate";
        } catch (Exception $e) {
            $output .= ob_get_clean();
            $output .= "\nException: " . $e->getMessage();
            return $output;
        }
        $output .= ob_get_clean();
        return $output;
    }
}

echo '<pre>' . htmlspecialchars(TestRunnerWrapper::run()) . '</pre>';