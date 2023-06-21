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
namespace Nasustop\HapiQueue;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Process\AbstractProcess;
use Nasustop\HapiQueue\Amqp\ConnectionFactory;
use Nasustop\HapiQueue\Amqp\Consumer as BaseConsumer;
use Nasustop\HapiQueue\Message\AmqpMessage;
use Nasustop\HapiQueue\Message\RedisMessage;
use Psr\Container\ContainerInterface;

class Consumer extends AbstractProcess
{
    protected string $queue = '';

    protected RedisMessage $redisMessage;

    protected AmqpMessage $amqpMessage;

    protected StdoutLoggerInterface $logger;

    protected ContainerInterface $container;

    protected ConfigInterface $config;

    protected ConnectionFactory $connectionFactory;

    public function __construct()
    {
        if (empty($this->queue)) {
            $this->queue = self::class;
        }
        $this->initQueue();
        parent::__construct($this->getContainer());
    }

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        $this->initQueue();
        return $this;
    }

    public function isEnable($server): bool
    {
        return (bool) $this->getConfig()->get('queue.open_process', false);
    }

    /**
     * get RedisMessage.
     */
    public function getRedisMessage(): RedisMessage
    {
        if (empty($this->redisMessage)) {
            $this->setRedisMessage(new RedisMessage());
        }
        return $this->redisMessage;
    }

    /**
     * set RedisMessage.
     */
    public function setRedisMessage(RedisMessage $redisMessage): self
    {
        $this->redisMessage = $redisMessage;
        return $this;
    }

    /**
     * get AmqpMessage.
     */
    public function getAmqpMessage(): AmqpMessage
    {
        if (empty($this->amqpMessage)) {
            $this->setAmqpMessage(new AmqpMessage());
        }
        return $this->amqpMessage;
    }

    /**
     * set AmqpMessage.
     */
    public function setAmqpMessage(AmqpMessage $amqpMessage): self
    {
        $this->amqpMessage = $amqpMessage;
        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        $queueDriver = $this->getConfig()->get('queue.driver', 'redis');
        switch ($queueDriver) {
            case 'redis':
                $this->driverRedis();
                break;
            case 'amqp':
                $this->driverAmqp();
                break;
        }
    }

    protected function getConnectionFactory(): ConnectionFactory
    {
        if (empty($this->connectionFactory)) {
            $this->connectionFactory = make(ConnectionFactory::class);
        }
        return $this->connectionFactory;
    }

    protected function getLogger(): StdoutLoggerInterface
    {
        if (empty($this->logger)) {
            $this->logger = make(StdoutLoggerInterface::class);
        }
        return $this->logger;
    }

    protected function getContainer(): ContainerInterface
    {
        if (empty($this->container)) {
            $this->container = make(ContainerInterface::class);
        }
        return $this->container;
    }

    protected function getConfig(): ConfigInterface
    {
        if (empty($this->config)) {
            $this->config = make(ConfigInterface::class);
        }
        return $this->config;
    }

    protected function initQueue()
    {
        $this->name = "queue.{$this->queue}";
        $this->nums = (int) $this->getConfig()->get(sprintf('queue.queue.%s.process', $this->queue), $this->nums);
    }

    protected function driverRedis()
    {
        $info = sprintf('Redis Queue Consumer [%s] start listen...', $this->name);
        $this->getLogger()->info($info);
        $this->getRedisMessage()->onQueue($this->queue)->consume();
    }

    /**
     * @throws \Throwable
     */
    protected function driverAmqp()
    {
        $consumer = new BaseConsumer(
            $this->getContainer(),
            $this->getConnectionFactory(),
            $this->getLogger(),
        );
        $message = $this->getAmqpMessage()->onQueue($this->queue);
        $info = sprintf('Amqp Queue Consumer [%s] start listen...', $this->name);
        $this->getLogger()->info($info);
        $consumer->setFactory($this->getConnectionFactory())->consume($message);
    }
}
