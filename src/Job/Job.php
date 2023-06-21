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
namespace Nasustop\HapiQueue\Job;

abstract class Job implements JobInterface
{
    /**
     * Acknowledge the message.
     */
    public const ACK = 'ack';

    /**
     * Unacknowledged the message.
     */
    public const NACK = 'nack';

    /**
     * Reject the message and requeue it.
     */
    public const REQUEUE = 'requeue';

    /**
     * Reject the message and drop it.
     */
    public const DROP = 'drop';

    protected int $attempts = 0;

    protected string $queue = 'default';

    public function getMaxAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * get Queue.
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * set Queue.
     */
    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function run()
    {
        $result = $this->handle();
        if ($result !== self::ACK) {
            throw new \RuntimeException(sprintf('%s处理失败', self::class));
        }
    }
}
