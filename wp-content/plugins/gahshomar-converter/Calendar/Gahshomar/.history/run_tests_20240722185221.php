<?php

require 'vendor/autoload.php';

use PHPUnit\TextUI\CliArguments\Builder;
use PHPUnit\TextUI\TestRunner;
use PHPUnit\TextUI\XmlConfiguration\Loader;

class TestRunnerWrapper
{
    public static function run()
    {
        $output = '';
        ob_start();
        try {
            $argv = ['phpunit', '--configuration', 'phpunit.xml'];
            $cliArguments = (new Builder())->fromParameters($argv);
            $configuration = (new Loader())->load($cliArguments->configuration());

            $runner = new TestRunner;
            $runner->run($configuration, $cliArguments);
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