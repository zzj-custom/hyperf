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
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlFactory;
use http\QueryString;
use Hyperf\DbConnection\Db;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;
use PhpParser\Node\Expr\Array_;
use Psr\Container\ContainerInterface;
use QL\QueryList;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Filesystem\FilesystemFactory;

class CrontabController extends AbstractController
{

    private $logger;

    /**
     * @var FilesystemFactory
     */
    private $factory;

    private static $requestUrl = 'https://www.zhihu.com/api/v3/feed/topstory/hot-lists/total?limit=100&desktop=true';

    private static $recommandUrl = 'https://www.zhihu.com/api/v3/feed/topstory/recommend';

    private static $blogUrl = 'http://wcf.open.cnblogs.com/news/recent/paged/';

    private static $limit = 3;

    private $clientFactory;

    private static $avatorUrl = 'http://q9u5z6n0s.bkt.clouddn.com/9127940611305c03d3b4032acf36d0e1.gif';


    public function __construct(
        ContainerInterface $container,
        FilesystemFactory $factory,
        ResponseInterface $response,
        RequestInterface $request,
        ClientFactory $clientFactory
    )
    {
        $this->logger = $container->get(StdoutLoggerInterface::class);
        $this->factory = $factory;
        $this->response = $response;
        $this->request = $request;
        $this->clientFactory = $clientFactory;
    }


    /**
     * @return array
     * @var StdoutLoggerInterface
     * @定日任务获取知乎热门数据
     */

    public function handleArticleCurl()
    {
        $this->logger->info('开始获取文章');
        $result = QueryList::get(self::$requestUrl, [
            'header' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:75.0) Gecko/20100101 Firefox/75.0',
                'Cookie' => '_zap=6e92fa25-75ac-48c0-91e2-252dc09e09cc; _xsrf=vR7DYmO3DlnNXWmIus7ifHK4gVv6cjyH; d_c0="ABChuyCNXBCPTgBRQinTEOHnThGxqQSujsY=|1573868918"; Hm_lvt_98beee57fd2ef70ccdd5ca52b9740c49=1588261788,1588262153,1588263783,1588348967; q_c1=3abc8a498a844630af2dc5e3bcbc3d54|1588261529000|1574697976000; _ga=GA1.2.1451345859.1584367024; Hm_lpvt_98beee57fd2ef70ccdd5ca52b9740c49=1588350169; KLBRSID=af132c66e9ed2b57686ff5c489976b91|1588350178|1588345703; tst=r; _gid=GA1.2.2096148567.1588260298; SESSIONID=6Ta5DMtnUTCa6tF0if8SFvJEdEZ1qHCPgI4Dns6bVIH; JOID=WlkVBEtETKYeJJh4YUsteR143U14exnALGjcDQgZEeR4FeQ3UH0KCk8lkH5hXeY7v1sFtri3wCaIatOMw09EUfo=; osd=UVEUCk5PRKcQIZNwYEUochV500hzcxjOKWPUDAYcGux5G-E8WHwED0QtkXBkVu46sV4Ovrm5xS2Aa92JyEdFX_8=; capsion_ticket="2|1:0|10:1588350178|14:capsion_ticket|44:NzRiYjFmNmFmYzI3NDZkOWIyN2M0Y2NlYmEzNGZkOTI=|5d05a02565d162c8f48156ff1573a8e853cf7821e2f38af7ddd00294f9f05750"; tshl=; l_cap_id="YTRiOWUxZWM2ZmQ5NGUwNDk0OGU2MDNkMzcwOGNkMTY=|1588349983|58b162e7641eeacee70b443365dbdf02ec4534af"; _gat_gtag_UA_149949619_1=1'
            ]
        ])->getHtml();
        $result = json_decode($result, true);
        if (empty($result)) {
            $this->logger->notice('获取数据失败');
            throw new BusinessException(10001, '获取数据失败');
        }
        if (!$result['data']) {
            $this->logger->notice('data获取失败');
            throw new BusinessException(10001, 'data获取失败');
        }
        foreach ($result['data'] as $key => $value) {
            $res = Db::table('article')->where(['title_id' => $value['target']['id']])->first();
            if (!empty($res)) {
                $this->logger->log('error', $value['target']['id'] . '已经存在');
                continue;
            }
            $insert_data = [
                'title_id' => $value['target']['id'],
                'information' => $value['target']['excerpt'],
                'agree' => $value['target']['follower_count'],
                'content' => $value['target']['comment_count'],
                'title' => $value['target']['title'],
                'user_name' => $value['target']['author']['name'],
                'avatar' => $value['target']['author']['avatar_url'],
                'article_img' => $value['children'][0]['thumbnail'],
            ];
            $response = Db::table('article')->insert($insert_data);
        }
        if (empty($response)) {
            $this->logger->info('插入失败');
            throw new BusinessException(10001, '插入失败');
        }
    }

    public function handleImageCurl()
    {
        set_time_limit(0);
        $this->logger->info('开始获取图片');
        $key = config('pixabay_key');
        //图片地址
        $container = ApplicationContext::getContainer();

        // 通过 DI 容器获取或直接注入 RedisFactory 类
        $redis = $container->get(RedisFactory::class)->get('default');
        $page = $redis->get('page');
        if (empty($page)) {
            $page = 1;
        }
        $this->logger->info("当前页码{$page}");
        $image_url = "https://pixabay.com/api/?key={$key}&pretty=true&per_page=200&page={$page}";
        $this->logger->info($image_url);
        $result = QueryList::get($image_url, null, [
            'headers' => [
                'User-gent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36',
                'Cookie' => '__cfduid=d4a721dc8f15f04976aef4c79d83bd7b01588560172; anonymous_user_id=51cf434e-de65-4f6a-9af0-1fe9a20a3654; _ga=GA1.2.1449119221.1588560189; is_human=1; csrftoken=r68VcYyLAE9pHSEsvbfcP4fvyARvmhRINzguX9L69NE3yvLBSW4bv8W3uK5ACHcU; client_width=1440; lang=zh; sessionid=eyJ0ZXN0Y29va2llIjoid29ya2VkIn0:1jX3hb:Gdc4UaLoFonueBuhPBIWx8SRVIM',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',

            ]
        ])->getHtml();
        $result = json_decode($result, true);
        if (isset($result['hits'])) {
            foreach ($result['hits'] as $value) {
                $res = Db::table('image')->where(['img_id' => $value['id']])->first();
                if (!empty($res)) {
                    $this->logger->info($value['id'] . '已经存在');
                    continue;
                }
                $insert_data[] = [
                    'img_url' => json_encode([
                        'small' => [
                            'url' => $value['previewURL'],
                            'width' => $value['previewWidth'],
                            'height' => $value['previewHeight'],
                        ],
                        'middle' => [
                            'url' => $value['webformatURL'],
                            'width' => $value['webformatWidth'],
                            'height' => $value['webformatHeight'],
                        ],
                        'large' => [
                            'url' => $value['largeImageURL'],
                            'width' => $value['imageWidth'],
                            'height' => $value['imageHeight'],
                        ],
                    ]),
                    'img_id' => $value['id'],
                    'type' => $value['type'],
                    'tags' => $value['tags'],
                    'views' => $value['views'],
                    'downloads' => $value['downloads'],
                    'favorites' => $value['favorites'],
                    'likes' => $value['likes'],
                    'comments' => $value['comments'],
                ];
            }
            if (empty($insert_data)) {
                $this->logger->info('没有数据');
            }

            $insert = Db::table('image')->insert($insert_data);
            if ($insert) {
                $this->logger->info('插入成功');
                $redis->set('page', $page + 1);
                return $this->response->json(true);
            } else {
                $this->logger->info('插入失败');
            }
        } else {
            $this->logger->log('error', $result);
        }
    }

    public function handleData($url, $header)
    {
        $response = QueryList::get($url, $header, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:75.0) Gecko/20100101 Firefox/75.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Cookie' => '_zap=6e92fa25-75ac-48c0-91e2-252dc09e09cc; _xsrf=vR7DYmO3DlnNXWmIus7ifHK4gVv6cjyH; d_c0="ABChuyCNXBCPTgBRQinTEOHnThGxqQSujsY=|1573868918"; Hm_lvt_98beee57fd2ef70ccdd5ca52b9740c49=1588263783,1588348967,1588554083,1588561827; q_c1=3abc8a498a844630af2dc5e3bcbc3d54|1588261529000|1574697976000; _ga=GA1.2.1451345859.1584367024; Hm_lpvt_98beee57fd2ef70ccdd5ca52b9740c49=1589070888; tst=r; capsion_ticket="2|1:0|10:1588352067|14:capsion_ticket|44:ZDY0YTkxZWY2NTVkNDJmOTk5NjE2ZTg0ODA4ZTc4ZjE=|349f0a88da02c5f3ca6c54ff9832dde5b4eb100673bf535a07addfd91f28b9a7"; tshl=; KLBRSID=af132c66e9ed2b57686ff5c489976b91|1588433106|1588433106; l_cap_id="YTRiOWUxZWM2ZmQ5NGUwNDk0OGU2MDNkMzcwOGNkMTY=|1588349983|58b162e7641eeacee70b443365dbdf02ec4534af"; z_c0="2|1:0|10:1588352083|4:z_c0|92:Mi4xaE5RRUJRQUFBQUFBRUtHN0lJMWNFQ1lBQUFCZ0FsVk5VNktaWHdCR0VvbDFIU1NRRzJQdl9hYjhWWWY0NkRkYjVB|e8cdd6fd889b69f4d4c7459f65330a9b3c78cb89d6084fc2dff4e02eae82e038"; _gid=GA1.2.524389020.1589070890',
                'Host' => 'api.zhihu.com',
            ]
        ])->getHtml();
        $result = json_decode($response, true);
        if (empty($result)) {
            $this->logger->notice('获取数据失败');
            throw new BusinessException(10001, '获取数据失败');
        }
        if (!$result['data']) {
            $this->logger->notice('data获取失败');
            throw new BusinessException(10001, 'data获取失败');
        }
        foreach ($result['data'] as $key => $value) {
            $res = Db::table('article')->where(['title_id' => $value['target']['id']])->first();
            if (!empty($res)) {
                $this->logger->log('error', $value['target']['title'] . '已经存在');
                continue;
            }
            if (!isset($value['target']['excerpt_new'])) {
                $this->logger->log('error', 'excerpt没有数据跳过');
            }
            $insert_data = [
                'title_id' => $value['target']['id'],
                'information' => $value['target']['excerpt_new'] ?? $value['target']['excerpt'],
                'agree' => $value['target']['thanks_count'] ?? 0,
                'content' => $value['target']['comment_count'],
                'title' => $value['target']['title'] ?? $value['target']['question']['title'],
                'user_name' => $value['target']['author']['name'],
                'avatar' => $value['target']['author']['avatar_url'],
                'article_img' => $value['target']['image_url'] ?? $value['children'][0]['thumbnail'],
                'detail' => $value['target']['content'],
                'type' => $value['target']['type']
            ];
            $response = Db::table('article')->insert($insert_data);
        }
        if (empty($response)) {
            $this->logger->info('插入失败');
            throw new BusinessException(10001, '插入失败');
        }
        if (isset($result['paging']['next'])) {
            return $result['paging']['next'];
        } else {
            $this->logger->error('没有下一页了');
            return false;
        }
    }

    public function test($url = '', $header = [])
    {
        if ($url == '') {
            $url = 'https://www.zhihu.com/api/v3/feed/topstory/recommend';
        }
        if (empty($header)) {
            $header = [
                'session_token' => '86fbb57e5670de4428762f40c7d08f5f',
                'desktop' => true,
                'page_number' => 1,
                'limit' => 10,
                'action' => 'down',
                'after_id' => 5,
                'ad_interval' => -1
            ];
        }
        $result = $this->handleData($url, $header);
        if ($result) {
            $this->logger->info($result);
            return $this->test($url, $header);
        }
    }

    public function parse_url_param($str)
    {
        $data = array();
        $ex_str = explode('?', $str);
        $end_str = end($ex_str);
        $parameter = explode('&', $end_str);
        foreach ($parameter as $val) {
            $tmp = explode('=', $val);
            $data[$tmp[0]] = $tmp[1];
        }
        return $data;
    }

    public function handleArticleBlogCurl()
    {
        // $options 等同于 GuzzleHttp\Client 构造函数的 $config 参数
//        $options = [
//            'base_uri' => 'https://oauth.cnblogs.com',
//            'timeout' => 5,
//            'headers' => [
//                'Content-Type' => 'application/x-www-form-urlencoded'
//            ],
//        ];
//        // $client 为协程化的 GuzzleHttp\Client 对象
//        $client = $this->clientFactory->create($options);
//        $response = $client->post('/connect/token', [
//            'client_id' => env('CLIENT_ID'),
//            'client_secret' => env('CLIENT_SECRET'),
//            'grant_type' => 'client_credentials'
//        ]);
        $container = ApplicationContext::getContainer();
        // 通过 DI 容器获取或直接注入 RedisFactory 类
        $redis = $container->get(RedisFactory::class)->get('default');
        $page = $redis->get('blog_page');
        if (empty($page)) {
            $page = 1;
        }
        $access_token = $redis->get('access_token');
        if (empty($access_token)) {
            $access_token = $this->getBlogAccessToken();
        }
        $headers = [
            'Authorization' => "Bearer {$access_token}"
        ];
        $response = QueryList::get("https://api.cnblogs.com/api/NewsItems?pageIndex={$page}&pageSize=100", null, [
            'headers' => $headers
        ])->getHtml();
        $this->logger->debug($response);
        $response = json_decode($response, true);
        if (!empty($response)) {
            foreach ($response as $key => $value) {
                //获取文章详细内容
                $article_detail = QueryList::get("https://api.cnblogs.com/api/newsitems/{$value['Id']}/body", null, [
                    'headers' => $headers
                ])->getHtml();
                $this->logger->error($article_detail);
                $redis->set('blog_page', $page + 1);
                $redis->set('access_token', $access_token, 60 * 60 * 12);
                $insert_data = [
                    'title_id' => $value['Id'],
                    'information' => $value['Summary'],
                    'agree' => $value['ViewCount'],
                    'content' => $value['CommentCount'],
                    'title' => $value['Title'],
                    'user_name' => '枫忆-镜花水月',
                    'avatar' => self::$avatorUrl,
                    'article_img' => $value['TopicIcon'],
//                    'detail' => @iconv('GBK', 'UTF-8', $article_detail),
                    'detail' => $article_detail,
                    'type' => 'news',
                    'create_dt' => date('Y-m-d H:i:s', strtotime($value['DateAdded'])),
                    'update_dt' => date('Y-m-d H:i:s')
                ];
                $db_res = Db::table('article')->where(['title_id' => $value['Id']])->first();
                if (empty($db_res)) {
                    $response = Db::table('article')->insert($insert_data);
                    if ($response) {
                        $this->logger->error("{$value['Id']}文章插入成功");
                    } else {
                        $this->logger->error("{$value['Id']}文章插入失败");
                    }
                } else {
                    $this->logger->error("{$value['Id']}文章已经存在");
                }
            }
            if (date('H') == 12) {
                $redis->set('access_token', $this->getBlogAccessToken());
            }
        } else {
            $this->logger->info('没有文章数据');
        }
        return $this->response->json(true);
    }

    public function getBlogAccessToken()
    {
        $response = QueryList::post('https://oauth.cnblogs.com/connect/token', [
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'grant_type' => 'client_credentials'
        ], [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ])->getHtml();
        $this->logger->info(json_decode($response, true)['access_token']);
        return json_decode($response, true)['access_token'];
    }
}
