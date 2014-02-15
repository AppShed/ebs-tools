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
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;

$input = new ArgvInput();
$output = new ConsoleOutput();

// Create a cache adapter that stores data on the filesystem
$cacheAdapter = new DoctrineCacheAdapter(new FilesystemCache(__DIR__ . '/cache'));

$aws = Aws::factory(__DIR__ . '/config/aws.json', [
    'services' => [
        'default_settings' => [
            'params' => [
                'credentials.cache' => $cacheAdapter
            ]
        ]
    ]
]);

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
