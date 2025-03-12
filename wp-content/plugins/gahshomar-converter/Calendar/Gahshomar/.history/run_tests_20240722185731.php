<?php

require 'vendor/autoload.php';

use PHPUnit\TextUI\Command;

class TestRunnerWrapper
{
    public static function run()
    {
        $output = '';
        ob_start();
        try {
            // Execute PHPUnit with CLI arguments
            $argv = ['phpunit', '--configuration=phpunit.xml'];
            Command::main(false, $argv);
        } catch (Exception $e) {
            $output .= ob_get_clean();
            $output .= "\nException: " . $e->getMessage();
            return $output;
        }
        $output .= ob_get_clean();
        return $output;
    }
}

echo htmlspecialchars(TestRunnerWrapper::run());