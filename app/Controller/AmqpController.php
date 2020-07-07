<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Amqp\Producer;
use Hyperf\Di\Annotation\Inject;
use App\Amqp\Producer\AppProducer;

class AmqpController extends AbstractController
{

    private static $logger;

    /**
     * @Inject()
     * @var Producer
     */
    private static $producer;

    private static $topic = [
        'email'
    ];

    public function __construct(ContainerInterface $container)
    {
        self::$logger = $container->get(StdoutLoggerInterface::class);
    }

    public static function handleProducer($data){
        if(empty($data) || !is_array($data)){
            self::$logger->info('数据获取失败');
        }
        if(empty($data['exchange']) || !in_array($data['exchange'], self::$topic)){
            self::$logger->info('topic获取失败');
        }
        switch ($data['exchange']){
            case 'email':
                break;
            default:
                self::$logger->info('topic获取失败1');
        }
        self::$producer->produce($data);
    }
}
