<?php

require 'vendor/autoload.php';

class TestRunnerWrapper
{
    public static function run()
    {
        $output = '';
        ob_start();
        try {
            // Execute PHPUnit from the command line
            $command = 'vendor/bin/phpunit --configuration phpunit.xml';
            exec($command, $outputLines, $returnVar);
            $output = implode("\n", $outputLines);
            if ($returnVar !== 0) {
                $output .= "\n\nErrors occurred during test execution.";
            }
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