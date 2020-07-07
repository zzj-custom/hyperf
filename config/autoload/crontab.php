<?php
use Hyperf\Crontab\Crontab;

return [
    // 是否开启定时任务
    'enable' => true,
    'crontab' => [
        // Callback类型定时任务（默认）
//        (new Crontab())->setName('handleArticleCurl')
//            ->setRule('*/10 * * * *')
//            ->setCallback([\App\Controller\CrontabController::class, 'handleArticleCurl'])
//            ->setMemo('处理爬取数据'),
//        (new Crontab())->setName('handleImageCurl')
//            ->setRule('*/5 * * * *')
//            ->setCallback([\App\Controller\CrontabController::class, 'handleImageCurl'])
//            ->setMemo('处理爬取数据'),
//        (new Crontab())->setName('handleArticleBlogCurl')
//            ->setRule('*/2 * * * *')
//            ->setCallback([\App\Controller\CrontabController::class, 'handleArticleBlogCurl'])
//            ->setMemo('处理爬取数据'),
    ],
];
