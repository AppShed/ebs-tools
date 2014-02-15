<?php
namespace AppShed\EbsTools\Commands;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Aws\Common\Aws;
use Aws\ElasticBeanstalk\ElasticBeanstalkClient;

abstract class EbsCommand extends Command
{
    /**
     * @var ElasticBeanstalkClient
     */
    protected $ebs;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Aws $aws, LoggerInterface $logger)
    {
        parent::__construct();
        $this->client = $aws->get('ElasticBeanstalk');
        $this->logger = $logger;
    }
}
