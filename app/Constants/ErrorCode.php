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

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    const METHOD_ERROR = 100001;

    const PARAM_ERROR = 100002;

    const LOGIN_ERROR = 100003;

    const INSERT_ERROR = 100004;

    const CODE_ERROR = 100005;

    const MAIL_ERROR = 100006;

    const ARTICLE_EMPTY_ERROR = 100007;

    const SUCCESS = 0;

    public static function getMessage($code){
        switch ($code){
            case 0:
                $message = '请求成功';
                break;
            case 100001:
                $message = '请求方法错误';
                break;
            case 100002:
                $message = '参数错误';
                break;
            case 100003:
                $message = '登录失败';
                break;
            case 100004:
                $message = '插入数据失败';
                break;
            case 100005:
                $message = '验证码输入错误';
                break;
            case 100006:
                $message = '邮件发送失败';
                break;
            case 100007:
                $message = '已经到底了';
                break;
            default:
                $message = '未知错误';
        }
        return $message;
    }
}
