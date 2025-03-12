<?php

require 'vendor/autoload.php';

use Gahshomar\Gahshomar;

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
            
            // Convert to Jalaali
            list($jy, $jm, $jd) = Gahshomar::toJalaali($gy, $gm, $gd);

            // Format the Jalaali date
            $jalaaliDate = Gahshomar::formatJalaali($jy, $jm, $jd);

            // Display the Jalaali date
            $output = "Today's date in Jalaali calendar: $jalaaliDate";
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