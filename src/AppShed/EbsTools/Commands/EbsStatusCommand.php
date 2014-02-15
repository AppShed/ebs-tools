<?php
namespace AppShed\EbsTools\Commands;

use Aws\ElasticBeanstalk\Exception\ElasticBeanstalkException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class EbsStatusCommand extends EbsCommand
{
    protected function configure()
    {
        $this
            ->setName('appshed:ebs:status')
            ->addArgument(
                'environment',
                InputArgument::REQUIRED,
                'The environment name you want to check'
            )
            ->addOption(
                'wait',
                null,
                InputOption::VALUE_NONE,
                'Wait until its green again'
            )
            ->addOption(
                'wait-time',
                null,
                InputOption::VALUE_OPTIONAL,
                'You can specify the loop wait',
                "5"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment');
        $wait = $input->getOption('wait');
        $waitTime = $input->getOption('wait-time');

        try {
            do {
                $status = $this->getStatus($environment);
            } while ($wait && $status != 'Ready' && !sleep($waitTime));
        } catch (ElasticBeanstalkException $e) {
            $this->logger->error(
                'Problem getting status',
                [
                    'environment' => $environment,
                    'e' => $e
                ]
            );
        }
    }

    protected function getStatus($environment)
    {
        $result = $this->client->describeEnvironments(
            [
                'EnvironmentNames' => [$environment],
                'InfoType' => 'tail'
            ]
        );

        if (!isset($result['Environments'][0])) {
            throw new ElasticBeanstalkException('Failed to get the information');
        }

        $this->logger->info(
            "Environment is {$result['Environments'][0]['Status']}",
            [
                'Health' => $result['Environments'][0]['Health'],
                'CNAME' => $result['Environments'][0]['CNAME']
            ]
        );

        return $result['Environments'][0]['Status'];
    }
}
