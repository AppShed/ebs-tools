#!/usr/bin/env php
<?php
set_time_limit(0);

include __DIR__ . '/vendor/autoload.php';

use AppShed\EbsTools\Commands\EbsStatusCommand;
use AppShed\EbsTools\Commands\EbsUpdateCommand;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Application;
use Aws\Common\Aws;
use Monolog\Logger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

$input = new ArgvInput();
$output = new ConsoleOutput();

$aws = Aws::factory(__DIR__ . '/config/aws.json');

$logger = new Logger('appshed');
$logger->pushHandler(
    new ConsoleHandler($output, true, [
        OutputInterface::VERBOSITY_NORMAL => Logger::DEBUG,
    ])
);

$application = new Application();
$application->add(new EbsUpdateCommand($aws, $logger));
$application->add(new EbsStatusCommand($aws, $logger));
$application->run($input, $output);
