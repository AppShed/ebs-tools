<?php
namespace AppShed\EbsTools\Commands;


use Aws\ElasticBeanstalk\Exception\ElasticBeanstalkException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class EbsUpdateCommand extends EbsCommand
{
    protected function configure()
    {
        $this
            ->setName('appshed:ebs:update')
            ->addArgument(
                'environment',
                InputArgument::REQUIRED,
                'The environment name you want to adjust'
            )
            ->addOption(
                'min-instances',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the minimum number of instances'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment');
        $minInstances = $input->getOption('min-instances');

        $update = [
            'EnvironmentName' => $environment,
            'OptionSettings' => []
        ];

        $changes = false;

        if ($minInstances !== null) {
            $this->logger->info(
                'Setting min instances',
                [
                    'min' => $minInstances
                ]
            );

            $update['OptionSettings'][] = [
                'Namespace' => 'aws:autoscaling:asg',
                'OptionName' => 'MinSize',
                'Value' => $minInstances
            ];

            $changes = true;
        }

        if ($changes) {
            try {
                $result = $this->client->updateEnvironment($update);
                $this->logger->info(
                    'Updated Environment',
                    [
                        'result' => $result
                    ]
                );
            } catch (ElasticBeanstalkException $e) {
                $this->logger->error(
                    'Problem updating',
                    [
                        'environment' => $environment,
                        'e' => $e
                    ]
                );
            }
        } else {
            $this->logger->info('No changes specified');
        }
    }
}
