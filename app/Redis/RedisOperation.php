<?php
/**
 * Created by PhpStorm.
 * User: zouzhujia
 * Date: 2020/5/7
 * Time: 9:38 PM
 */
use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

class RedisOperation extends \Hyperf\Redis\Redis {

    protected $redis = '';


    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->redis = $container->get(RedisFactory::class)->get('default');
    }
}
