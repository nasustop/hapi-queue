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
use Hyperf\Utils\ApplicationContext;
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

    public function __construct(protected ContainerInterface $container)
    {
        if (empty($this->queue)) {
            $this->queue = self::class;
        }
        $this->initQueue();
        $this->logger = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
        parent::__construct($container);
    }

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        $this->initQueue();
        return $this;
    }

    public function isEnable($server): bool
    {
        return (bool) $this->getConfig('queue.open_process', false);
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
        $queueDriver = $this->getConfig('queue.driver', 'redis');
        switch ($queueDriver) {
            case 'redis':
                $this->driverRedis();
                break;
            case 'amqp':
                $this->driverAmqp();
                break;
        }
    }

    protected function getConfig(string $key, $default = null)
    {
        $config = $this->container->get(ConfigInterface::class);
        return $config->get($key, $default);
    }

    protected function initQueue()
    {
        $this->name = "queue.{$this->queue}";
        $this->nums = (int) $this->getConfig(sprintf('queue.queue.%s.process', $this->queue), $this->nums);
    }

    protected function driverRedis()
    {
        $info = sprintf('Redis Queue Consumer [%s] start listen...', $this->name);
        $this->logger->info($info);
        $this->getRedisMessage()->onQueue($this->queue)->consume();
    }

    /**
     * @throws \Throwable
     */
    protected function driverAmqp()
    {
        $consumer = new BaseConsumer(
            $this->container,
            $this->container->get(ConnectionFactory::class),
            $this->container->get(StdoutLoggerInterface::class)
        );
        $message = $this->getAmqpMessage()->onQueue($this->queue);
        $info = sprintf('Amqp Queue Consumer [%s] start listen...', $this->name);
        $this->logger->info($info);
        $factory = $this->container->get(ConnectionFactory::class);
        $consumer->setFactory($factory)->consume($message);
    }
}
