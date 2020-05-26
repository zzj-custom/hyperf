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

use App\Constants\ErrorCode;
use App\Constants\FunctionCommon;
use App\Exception\BusinessException;
use App\Model\User;

class LoginController extends BaseController
{

    public function Login()
    {
        $param = $this->param;
        $userInfo = User::getUserInfoByMobile($param['mobile']);
        $code = mt_rand(1111, 9999);
        if(empty($userInfo)){
            //插入数据
            $userInfo = [
                'name' => '镜花水月',
                'head_url' => 'http://q9u5z6n0s.bkt.clouddn.com/9127940611305c03d3b4032acf36d0e1.gif',
                'ext_info' => '最是人间留不住，朱颜辞镜花辞树',
            ];
            if(FunctionCommon::pattern_email($param['mobile'])){
                $userInfo['email'] = $param['mobile'];
            }elseif(FunctionCommon::pattern_mobile($param['mobile'])){
                $userInfo['mobile'] = $param['mobile'];
            }
            $insert_id = User::insertData($userInfo);
            if(!$insert_id){
                throw new BusinessException(ErrorCode::INSERT_ERROR);
            }
            $userInfo['id'] = $insert_id;
            $this->redis->set("uid:$insert_id", json_encode($userInfo));
        }
        if(FunctionCommon::pattern_email($param['mobile'])){
            $this->sendLoginMail($param['mobile'], $code);
        }
        $this->redis->set("code:{$userInfo['id']}", $code, 60*5);
        $response = [
            'id' => $userInfo['id'],
            'code' => $code,
        ];
        return $this->response($response);
    }

    public function checkCode(){
        $param = $this->param;
        if(!isset($param['code']) || empty($param['code'])){
            throw new BusinessException(ErrorCode::CODE_ERROR);
        }
        $code = $this->redis->get("code:{$param['id']}");
        $this->logger->info($code);
        if($code !== $param['code']){
            throw new BusinessException(ErrorCode::CODE_ERROR);
        }
        return $this->response(true);
    }

    public function sendLoginMail($email, $code){
        //发送邮件
        $mail = [
            'subject' => 'movie邮箱验证',
            'from' => [env('MAIL_USER_NAME', '1844066417@qq.com') => 'movie创建者'],
            'to' => [$email => 'movie创建者'],
            'content' => str_replace('%code%', $code, config('mail_body', ''))
        ];
        $this->sendMail($mail);
    }
}
