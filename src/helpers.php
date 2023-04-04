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
if (! function_exists('pushQueue')) {
    /**
     * 生产者,触发队列任务.
     */
    function pushQueue(Nasustop\HapiQueue\Job\JobInterface $job)
    {
        (new \Nasustop\HapiQueue\Producer($job))->onQueue($job->getQueue())->dispatcher();
    }
}
