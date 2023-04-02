<?php

declare(strict_types=1);
/**
 * This file is part of Hapi.
 *
 * @link     https://www.nasus.top
 * @document https://wiki.nasus.top
 * @contact  xupengfei@xupengfei.net
 * @license  https://github.com/nasustop/hapi-queue/blob/master/LICENSE
 */
namespace Nasustop\HapiQueue\Command;

use Hyperf\Command\Command as HyperfCommand;
use Nasustop\HapiQueue\Demo\DemoJob;
use Nasustop\HapiQueue\Producer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

class ProducerCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('hapi:queue:producer_test');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('测试发送队列消息');
        $this->addOption('name', '', InputOption::VALUE_OPTIONAL, '测试name.', 'name');
        $this->setHelp('php bin/hyperf.php hapi:queue:producer_test [--name [NAME]]');
    }

    public function handle()
    {
        $name = $this->input->getOption('name');
        $job = new DemoJob([
            'name' => $name,
            'date' => date('Y-m-d H:i:s'),
        ]);
        (new Producer($job))->onQueue('default_delayed_5')->dispatcher();
        $this->info('push job success');

        // 安全关闭进程，否则amqp会抛出warning异常
        $pid = posix_getpid();
        posix_kill($pid, SIGINT);
    }
}
