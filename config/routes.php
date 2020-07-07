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

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/index', 'App\Controller\IndexController@index');
Router::addRoute(['GET', 'POST', 'HEAD'], '/handle-login', 'App\Controller\IndexController@handleLogin');
Router::addRoute(['GET', 'POST', 'HEAD'], '/handle-curl', 'App\Controller\IndexController@handleCurl');
Router::addRoute(['GET', 'POST', 'HEAD'], '/upload-file', 'App\Controller\IndexController@uploadFile');
Router::addRoute(['GET', 'POST', 'HEAD'], '/handle-image-curl', 'App\Controller\CrontabController@handleImageCurl');
Router::addRoute(['GET', 'POST', 'HEAD'], '/handle-article-curl', 'App\Controller\CrontabController@handleArticleCurl');
Router::addRoute(['GET', 'POST', 'HEAD'], '/handle-article-blog-curl', 'App\Controller\CrontabController@handleArticleBlogCurl');
Router::addRoute(['GET', 'POST', 'HEAD'], '/test', 'App\Controller\CrontabController@test');
Router::addRoute(['GET', 'POST', 'HEAD'], '/handle-img-curl', 'App\Controller\CurlController@handleImgCurl');



Router::addGroup('/login/',function (){
    Router::post('login','App\Controller\LoginController@login');
    Router::post('check-code','App\Controller\LoginController@checkCode');
    Router::post('login-out','App\Controller\LoginController@loginOut');
    Router::post('update','App\Controller\LoginController@update');
});

Router::addGroup('/article/',function (){
    Router::post('get-article','App\Controller\ArticleController@getArticle');
    Router::post('get-article-detail','App\Controller\ArticleController@getArticleDetail');
});

Router::addGroup('/image/',function (){
    Router::post('get-image','App\Controller\ImageController@getImage');
});
