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
use Hyperf\Contract\ConfigInterface;
use Nasustop\HapiQueue\Consumer;
use Symfony\Component\Console\Input\InputArgument;

class ConsumerCommand extends HyperfCommand
{
    protected ConfigInterface $config;

    public function __construct()
    {
        parent::__construct('hapi:queue:work');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('执行队列监听');
        $this->addArgument('queue', InputArgument::REQUIRED, '队列名称');
        $this->setHelp('php bin/hyperf.php hapi:queue:work [queue]');
        $this->addUsage('[queue]队列名称');
    }

    public function handle()
    {
        $queue = $this->input->getArgument('queue');
        $config = $this->getConfig()->get(sprintf('queue.queue.%s', $queue));
        if (! $config) {
            $this->error(sprintf('[%s]队列配置不存在', $queue));
            return;
        }
        (new Consumer())->setQueue($queue)->handle();
    }

    protected function getConfig(): ConfigInterface
    {
        if (empty($this->config)) {
            $this->config = make(ConfigInterface::class);
        }
        return $this->config;
    }
}
