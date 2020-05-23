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
class FunctionCommon extends AbstractConstants
{
    public static function pattern_mobile($mobile): bool
    {
        if(preg_match('/^1[23456789]\d{9}$/', $mobile)){
            return true;
        }else{
            return false;
        }
    }
    public static function pattern_email($email){
        if(preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/', $email)){
            return true;
        }else{
            return false;
        }
    }
}
