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

use Hyperf\Amqp\ConnectionFactory;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;

class ConsumerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Consumer(
            $container,
            $container->get(ConnectionFactory::class),
            $container->get(StdoutLoggerInterface::class)
        );
    }
}
