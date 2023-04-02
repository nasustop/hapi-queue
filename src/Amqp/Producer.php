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
namespace Nasustop\HapiQueue\Amqp;

class Producer extends \Hyperf\Amqp\Producer
{
    /**
     * set Factory.
     */
    public function setFactory(ConnectionFactory $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * get Factory.
     */
    public function getFactory(): \Hyperf\Amqp\ConnectionFactory
    {
        return $this->factory;
    }
}
