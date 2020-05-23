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
use App\Model\Article;

class ArticleController extends BaseController
{
    public function getArticle(){
        $param = $this->request->all();
        $this->logger->info(json_encode($param));
        $page = $param['page'] ?? env('PAGE');
        $page_size = $param['page_size'] ?? env('PAGE_SIZE');
        $this->logger->info(($page - 1) * $page_size);
        //$offset = ($page - 1) * $page_size;
        $result = Article::getArticle($page, $page_size);
        if(empty($result)){
            throw new BusinessException(ErrorCode::ARTICLE_EMPTY_ERROR);
        }
        return $this->response([
            'data' => $result
        ]);
    }
}
