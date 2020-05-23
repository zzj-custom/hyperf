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
use App\Exception\BusinessException;
use App\Model\User;
use function GuzzleHttp\Psr7\str;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\RedisFactory;
use Hyperf\HttpServer\Contract\ResponseInterface;

class BaseController extends AbstractController
{
    protected $logger;

    protected $param;

    protected $redis;

    /**
     * @var
     */
    protected $transport;

    /**
     * @var
     */

    protected $mailer;


    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        RedisFactory $redis,
        StdoutLoggerInterface $logger)
    {
        $this->request = $request;
        $this->response = $response;

        if (!$this->request->isMethod('post')) {
            throw new BusinessException(ErrorCode::METHOD_ERROR, '请求方法错误');
        }

        //$container = ApplicationContext::getContainer();

        // 通过 DI 容器获取或直接注入 RedisFactory 类
        //$redis = $container->get($redis)->get('default');
        $this->redis = $redis->get('default');

        $this->logger = $logger;

        $this->param = $this->request->all();
        $this->logger->info(json_encode($this->request->all()) . 'param');
    }

    protected function checkLogin($uid)
    {
        return $this->getUserInfoByRedis($uid)['id'] ?? false;
    }

    protected function getUserInfoByRedis($uid)
    {
        return $this->redis->get("uid:{$uid}");
    }

    protected function getUserIdByMobile()
    {
        return User::getUserInfoByMobile($this->param['mobile'])->id;
    }

    protected function response($data, $code = ErrorCode::SUCCESS)
    {
        return $this->response->json([
            'errCode' => $code,
            'errMsg' => 'success',
            'data' => $data
        ]);
    }

    /**
     * 发送邮件类 参数 $data 需要三个必填项 包括 邮件主题`$data['subject']`、接收邮件的人`$data['to']`和邮件内容 `$data['content']`
     * @param Array $data
     * @return bool $result 发送成功 or 失败
     */
    protected function sendMail($data): bool
    {
        $this->transport = (new \Swift_SmtpTransport('smtp.qq.com', 465))
            ->setEncryption(env('MAIL_ENCRYPTION', 'ssl'))
            ->setUsername(env('MAIL_USER_NAME', '1844066417@qq.com'))
            ->setPassword(env('MAIL_PASSWORD'));
        $this->mailer = new \Swift_Mailer($this->transport);

        $message = (new \Swift_Message())
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody("{$data['content']}", 'text/html', 'utf-8');

        $this->mailer->protocol = env('MAIL_TYPE');
        $result = $this->mailer->send($message);

        // 释放
        $this->destroy();
        if (!$result) {
            throw new BusinessException(ErrorCode::MAIL_ERROR);
        }
        return true;
    }

    protected function destroy()
    {
        $this->transport = null;
        $this->mailer = null;
    }
}
