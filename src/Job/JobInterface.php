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

interface JobInterface extends \Hyperf\AsyncQueue\JobInterface
{
    public function setQueue(string $queue): self;

    public function getQueue(): string;

    public function handle(): string;
}
