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

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

$mail_body = <<<Eof
        <!doctype html>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <title></title>
        </head>
        <body>
        <table width="700" border="0" align="center" cellspacing="0" style="width:700px;">
            <tbody>
            <tr>
                <td>
                    <div style="width:700px;margin:0 auto;border-bottom:1px solid #ccc;margin-bottom:30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="700" height="39" style="font:12px Tahoma, Arial, 宋体;">
                            <tbody>
                            <tr>
                                <td width="210">
                                   
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width:680px;padding:0 10px;margin:0 auto;">
                        <div style="line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;">
                            <strong style="display:block;margin-bottom:15px;">
                                亲爱的会员：
                                <span style="color:#f60;font-size: 16px;"></span>您好！
                            </strong>
        
                            <strong style="display:block;margin-bottom:15px;">
                                您正在进行安全邮箱，请在验证码输入框中输入：
                                <span style="color:#f60;font-size: 24px">%code%</span>，以完成操作。
                            </strong>
                        </div>
        
                        <div style="margin-bottom:30px;">
                            <small style="display:block;margin-bottom:20px;font-size:12px;">
                                <p style="color:#747474;">
                                    注意：此操作可能会修改您的密码、登录邮箱或绑定手机。如非本人操作，请及时登录并修改密码以保证帐户安全
                                    <br>（工作人员不会向你索取此验证码，请勿泄漏！)
                                </p>
                            </small>
                        </div>
                    </div>
                    <div style="width:700px;margin:0 auto;">
                        <div style="padding:10px 10px 0;border-top:1px solid #ccc;color:#747474;margin-bottom:20px;line-height:1.3em;font-size:12px;">
                            <p>此为系统邮件，请勿回复<br>
                                请保管好您的邮箱，避免账号被他人盗用
                            </p>
                            <p>movie版权所有1999-2014</p>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        </body>
        </html>
Eof;

return [
    'app_name' => env('APP_NAME', 'skeleton'),
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            //LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
    'pixabay_key' => env('PIXABAY_KEY', '16436593-17310cab0e1eac314b3493a85'),
    'mail_body' => $mail_body
];
