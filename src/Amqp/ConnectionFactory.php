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

class ConnectionFactory extends \Hyperf\Amqp\ConnectionFactory
{
    protected function getConfig(string $pool): array
    {
        $key = 'queue.amqp';
        if (! $this->config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        return $this->config->get($key);
    }
}
