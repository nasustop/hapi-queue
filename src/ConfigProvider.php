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

use Nasustop\HapiQueue\Amqp\Consumer;
use Nasustop\HapiQueue\Amqp\ConsumerFactory;
use Nasustop\HapiQueue\Amqp\Producer;
use Nasustop\HapiQueue\Command\ConsumerCommand;
use Nasustop\HapiQueue\Command\ProducerCommand;
use Nasustop\HapiQueue\Listener\QueueHandleListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Consumer::class => ConsumerFactory::class,
                Producer::class => Producer::class,
            ],
            'commands' => [
                ConsumerCommand::class,
                ProducerCommand::class,
            ],
            'listeners' => [
                QueueHandleListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'queue',
                    'description' => 'The config for queue.',
                    'source' => __DIR__ . '/../publish/queue.php',
                    'destination' => BASE_PATH . '/config/autoload/queue.php',
                ],
            ],
        ];
    }
}
