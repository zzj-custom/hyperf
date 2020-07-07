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

use App\Exception\BusinessException;
use Hyperf\DbConnection\Db;
use Psr\Container\ContainerInterface;
use QL\QueryList;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Filesystem\FilesystemFactory;

class CurlController extends AbstractController
{
    private static $requestUrl = 'https://www.zhihu.com/api/v3/feed/topstory/hot-lists/total?limit=100&desktop=true';

    private static $requestImgUrl = 'https://www.duitang.com/napi/blog/list/by_search/?kw=%E5%86%99%E7%9C%9F'; //https://www.duitang.com/napi/blog/list/by_search/?kw=%E6%A0%A1%E8%8A%B1';

    public static $imgArray = [
        'jpg', 'jpeg', 'png', 'gif'
    ];

    private $logger;


    /**
     * @var FilesystemFactory
     */
    private $factory;

    public function __construct(ContainerInterface $container, FilesystemFactory $factory)
    {
        $this->logger = $container->get(StdoutLoggerInterface::class);
        $this->factory = $factory;
    }

    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        $ql = QueryList::get('https://www.zhihu.com', [
            'headers' => [
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:75.0) Gecko/20100101 Firefox/75.0',
                //填写从浏览器获取到的cookie
                'cookie' => 'q_c1=3821590e813643d5a0d3fa418f42b590|1578498283000|1573868989; _zap=6e92fa25-75ac-48c0-91e2-252dc09e09cc; _xsrf=vR7DYmO3DlnNXWmIus7ifHK4gVv6cjyH; d_c0="ABChuyCNXBCPTgBRQinTEOHnThGxqQSujsY=|1573868918"; Hm_lvt_98beee57fd2ef70ccdd5ca52b9740c49=1588261771,1588261788,1588262153,1588263783; q_c1=3abc8a498a844630af2dc5e3bcbc3d54|1588261529000|1574697976000; _ga=GA1.2.1451345859.1584367024; Hm_lpvt_98beee57fd2ef70ccdd5ca52b9740c49=1588348699; KLBRSID=af132c66e9ed2b57686ff5c489976b91|1588348697|1588345703; tst=r; _gid=GA1.2.2096148567.1588260298; SESSIONID=6Ta5DMtnUTCa6tF0if8SFvJEdEZ1qHCPgI4Dns6bVIH; JOID=WlkVBEtETKYeJJh4YUsteR143U14exnALGjcDQgZEeR4FeQ3UH0KCk8lkH5hXeY7v1sFtri3wCaIatOMw09EUfo=; osd=UVEUCk5PRKcQIZNwYEUochV500hzcxjOKWPUDAYcGux5G-E8WHwED0QtkXBkVu46sV4Ovrm5xS2Aa92JyEdFX_8=; capsion_ticket="2|1:0|10:1588260901|14:capsion_ticket|44:ODAxNmUzY2YzZTcyNGM2M2I2Nzk3NDgzNTc3OThhNjQ=|df09928eca144eca3c12616cf0416a6af034c4c22e4d6f7a161ad66213787f6e"; z_c0="2|1:0|10:1588261029|4:z_c0|92:Mi4xaE5RRUJRQUFBQUFBRUtHN0lJMWNFQ1lBQUFCZ0FsVk5wVDZZWHdEOHNib1VqZWVaQ0hIWHRNRFRCQ2pUaUQ1a253|5cc8cd9f4941271041f3523160564fd0ecbe0487b855af69b97c474bb77b0d4f"; tshl='
            ]
        ]);
        return $ql->getHtml();

    }

    public function handleLogin()
    {
        return QueryList::post("https://www.zhihu.com/api/v3/oauth/sign_in", [
            [
                'username' => '18689223002',
                'password' => 'zqp113217',
                '_xsrf' => 'vR7DYmO3DlnNXWmIus7ifHK4gVv6cjyH',
                'x-zse-83' => '3_2.0'
            ],
            'header' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:75.0) Gecko/20100101 Firefox/75.0',
                'Cookie' => '_zap=6e92fa25-75ac-48c0-91e2-252dc09e09cc; _xsrf=vR7DYmO3DlnNXWmIus7ifHK4gVv6cjyH; d_c0="ABChuyCNXBCPTgBRQinTEOHnThGxqQSujsY=|1573868918"; Hm_lvt_98beee57fd2ef70ccdd5ca52b9740c49=1588261788,1588262153,1588263783,1588348967; q_c1=3abc8a498a844630af2dc5e3bcbc3d54|1588261529000|1574697976000; _ga=GA1.2.1451345859.1584367024; Hm_lpvt_98beee57fd2ef70ccdd5ca52b9740c49=1588350169; KLBRSID=af132c66e9ed2b57686ff5c489976b91|1588350178|1588345703; tst=r; _gid=GA1.2.2096148567.1588260298; SESSIONID=6Ta5DMtnUTCa6tF0if8SFvJEdEZ1qHCPgI4Dns6bVIH; JOID=WlkVBEtETKYeJJh4YUsteR143U14exnALGjcDQgZEeR4FeQ3UH0KCk8lkH5hXeY7v1sFtri3wCaIatOMw09EUfo=; osd=UVEUCk5PRKcQIZNwYEUochV500hzcxjOKWPUDAYcGux5G-E8WHwED0QtkXBkVu46sV4Ovrm5xS2Aa92JyEdFX_8=; capsion_ticket="2|1:0|10:1588350178|14:capsion_ticket|44:NzRiYjFmNmFmYzI3NDZkOWIyN2M0Y2NlYmEzNGZkOTI=|5d05a02565d162c8f48156ff1573a8e853cf7821e2f38af7ddd00294f9f05750"; tshl=; l_cap_id="YTRiOWUxZWM2ZmQ5NGUwNDk0OGU2MDNkMzcwOGNkMTY=|1588349983|58b162e7641eeacee70b443365dbdf02ec4534af"; _gat_gtag_UA_149949619_1=1'
            ]
        ])->getHtml();
    }

    /**
     * @return array
     * @var StdoutLoggerInterface
     * @定日任务获取知乎热门数据
     */

    public function handleCurl()
    {
        $result = QueryList::get(self::$requestUrl, [
            'header' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:75.0) Gecko/20100101 Firefox/75.0',
                'Cookie' => '_zap=6e92fa25-75ac-48c0-91e2-252dc09e09cc; _xsrf=vR7DYmO3DlnNXWmIus7ifHK4gVv6cjyH; d_c0="ABChuyCNXBCPTgBRQinTEOHnThGxqQSujsY=|1573868918"; Hm_lvt_98beee57fd2ef70ccdd5ca52b9740c49=1588261788,1588262153,1588263783,1588348967; q_c1=3abc8a498a844630af2dc5e3bcbc3d54|1588261529000|1574697976000; _ga=GA1.2.1451345859.1584367024; Hm_lpvt_98beee57fd2ef70ccdd5ca52b9740c49=1588350169; KLBRSID=af132c66e9ed2b57686ff5c489976b91|1588350178|1588345703; tst=r; _gid=GA1.2.2096148567.1588260298; SESSIONID=6Ta5DMtnUTCa6tF0if8SFvJEdEZ1qHCPgI4Dns6bVIH; JOID=WlkVBEtETKYeJJh4YUsteR143U14exnALGjcDQgZEeR4FeQ3UH0KCk8lkH5hXeY7v1sFtri3wCaIatOMw09EUfo=; osd=UVEUCk5PRKcQIZNwYEUochV500hzcxjOKWPUDAYcGux5G-E8WHwED0QtkXBkVu46sV4Ovrm5xS2Aa92JyEdFX_8=; capsion_ticket="2|1:0|10:1588350178|14:capsion_ticket|44:NzRiYjFmNmFmYzI3NDZkOWIyN2M0Y2NlYmEzNGZkOTI=|5d05a02565d162c8f48156ff1573a8e853cf7821e2f38af7ddd00294f9f05750"; tshl=; l_cap_id="YTRiOWUxZWM2ZmQ5NGUwNDk0OGU2MDNkMzcwOGNkMTY=|1588349983|58b162e7641eeacee70b443365dbdf02ec4534af"; _gat_gtag_UA_149949619_1=1'
            ]
        ])->getHtml();
        $result = json_decode($result, true);
        if (empty($result)) {
            throw new BusinessException(10001, '获取数据失败');
        }
        if (!$result['data']) {
            throw new BusinessException(10001, 'data获取失败');
        }
        $this->logger->debug(json_encode($result));
        $insert_data = [];
        foreach ($result['data'] as $key => $value) {
            $res = Db::table('article')->where(['title_id' => $value['target']['id']])->first();
            if (!empty($res)) {
                continue;
            }
            $insert_data[] = [
                'title_id' => $value['target']['id'],
                'information' => $value['target']['excerpt'],
                'agree' => $value['target']['follower_count'],
                'content' => $value['target']['comment_count'],
                'title' => $value['target']['title'],
                'user_name' => $value['target']['author']['name'],
                'avatar' => $value['target']['author']['avatar_url'],
                'article_img' => $value['children'][0]['thumbnail'],
            ];
        }
        $response = Db::table('article')->insert($insert_data);
        if (empty($response)) {
            throw new BusinessException(10001, '插入失败');
        }
        return [
            'code' => 00000,
            'msg' => '添加成功',
            'data' => $response
        ];
    }

    public function handleImgCurl()
    {
        $result = QueryList::get(self::$requestImgUrl . "&start=0&limit=1")->getHtml();
        $result = json_decode($result, true);
        if (empty($result)) {
            throw new BusinessException(10001, '获取数据失败');
        }
        if (!$result['data']) {
            throw new BusinessException(10001, 'data获取失败');
        }
        $total = $result['data']['total'];
        if (intval($total) == 0) {
            throw new BusinessException(10001, '获取数据失败');
        }
        $count = ceil($total / 100);

        for ($i = 0; $i < $count; $i++) {
            $result = QueryList::get(self::$requestImgUrl . "&start=" . ($i * 100) . "&limit=100")->getHtml();
            $result = json_decode($result, true);
            $this->logger->debug(json_encode($result));
            $insert_data = [];
            foreach ($result['data']['object_list'] as $key => $value) {
                $res = Db::table('image')->where(['img_id' => $value['id']])->first();
                if (!empty($res)) {
                    continue;
                }
                if (!in_array(substr($value['photo']['path'], strrpos($value['photo']['path'], '.') + 1), self::$imgArray)) {
                    continue;
                }
                $dataImage = file_get_contents($value['photo']['path']);
                if(empty($dataImage)){
                    continue;
                }
                $data = file_get_contents($value['photo']['path']);
                if(empty($data)){
                    continue;
                }
                $image = substr($value['photo']['path'], strrpos($value['photo']['path'], '/' ) + 1);
                if(file_exists(BASE_PATH . '/public/image/' . $image)){
                    continue;
                }
                $this->logger->info($image);
                file_put_contents(BASE_PATH . '/public/image/' . $image, $data);
                $insert_data[] = [
                    'img_url' => $image,
                    'width' => $value['photo']['width'],
                    'height' => $value['photo']['height'],
                    'img_id' => $value['id'],
                ];
            }
            $response = Db::table('image')->insert($insert_data);
            if (empty($response)) {
                throw new BusinessException(10001, '插入失败');
            }
        }
        return [
            'code' => 00000,
            'msg' => '添加成功',
            'data' => $response
        ];

    }

    public function getArticle()
    {
        $result = Db::table('article')->get();
        return [
            'errCode' => 0,
            'errMsg' => '成功',
            'data' => json_encode($result)
        ];
    }

    public function getImage()
    {
        $result = Db::table('image')->get();
        $result = json_decode(json_encode($result), true);
        foreach ($result as &$value) {
            $value['url'] = $value['img_url'];
        }
        return [
            'errCode' => 0,
            'errMsg' => '成功',
            'data' => json_encode($result)
        ];
    }

    /**
     * @param string $img_url
     * @param string $type
     * @param FilesystemFactory $factory
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadFile($img_url, $type)
    {
        $data = file_get_contents($img_url);
        $qiniu = $this->factory->get('qiniu');
        $file_path = "{$type}/" . substr($img_url, strrpos($img_url, '/') + 1);
//        if($qiniu->has($file_path)){
//            return false;
//        }
        $result = $qiniu->write($file_path, $data);
        if(!$result){
            throw new BusinessException(10001, '文件获取失败');
        }
        $this->logger->debug(env('QINBIU_HOST'). $file_path);
        return env('QINBIU_HOST'). $file_path;
    }

}
