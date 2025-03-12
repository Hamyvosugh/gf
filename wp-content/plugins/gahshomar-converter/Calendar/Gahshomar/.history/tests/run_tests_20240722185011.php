<?php

require 'vendor/autoload.php';

use PHPUnit\TextUI\Command;

class TestRunner
{
    public static function run()
    {
        $output = '';
        ob_start();
        try {
            Command::main(false);
        } catch (Exception $e) {
            $output .= ob_get_clean();
            $output .= "\nException: " . $e->getMessage();
            return $output;
        }
        $output .= ob_get_clean();
        return $output;
    }
}

echo htmlspecialchars(TestRunner::run());