# HapiMemcached
hyperf的队列组件，兼容`hyperf/async-queue`和`hyperf/amqp`

## 安装
```
composer require nasustop/hapi-queue
```

## 声称配置文件
```
php bin/hyperf.php vendor:publish nasustop/hapi-queue
```

## 调用队列的方式
```php
$job = new DemoJob(['name' => 'hapi']);
(new Producer($job))->onQueue('test')->dispatcher();
```

## 监听队列

### 命令行
```shell
php bin/hyperf.php hapi:queue:work [queue]
```

### Process进程
```shell
# 新建process文件DemoProcess.php
# queue配置文件中设置了几个队列，就建立几个process文件，$queue 为配置中的队列名称

<?php

namespace App\Demo;

use Nasustop\HapiQueue\Consumer;

class DemoConsumer extends Consumer
{
    protected string $queue = 'default';
}

# 将DemoProcess加入到process配置文件中
# cat config/autoload/processes.php
<?php

return [
    App\Process\Queue\QueueDefaultProcess::class,
];

```