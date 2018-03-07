<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;

$mipInfo = Config::get('mipInfo');
$request = Request::instance();

if (!strpos($request->url(),'Api')) {
    if ($mipInfo['aritcleLevelRemove']) {
        if ($mipInfo['urlCategory']) {
            Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['article/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['article/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['article/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['article/ArticleDetail/index?sub=:sub',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/:id'=>['article/ArticleDetail/index?sub=:sub',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['article/ArticleDetail/index?category=:category',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule(['[:category]/:id'=>['article/ArticleDetail/index?category=:category',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['article/Article/index?sub=:sub',[],['category'=>'[a-zA-Z0-9_-]+','sub'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]' => ['article/Article/index',[],['category'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule($mipInfo['articleModelUrl'],'article/Article/index');
        } else {
            Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['article/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['article/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['article/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['article/ArticleDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['article/ArticleDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['article/Article/index?sub=:sub',[],['category'=>'[a-zA-Z0-9_-]+','sub'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]' => ['article/Article/index',[],['category'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule($mipInfo['articleModelUrl'],'article/Article/index');
        }
    } else {
        Route::group($mipInfo['articleModelUrl'], [
            '[:category]/index'.$mipInfo['urlPageBreak'].'<page>'  =>['article/Article/index',['ext'=>'html'],[]],
            'index'.$mipInfo['urlPageBreak'].'<page>'=>['article/Article/index',['ext'=>'html'],[]],
            '/publish'  =>['article/Article/publish',['ext'=>'html'],[]],
            '<id>'.$mipInfo['urlPageBreak'].'<page>'=>  ['article/ArticleDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
            ':id'=>  ['article/ArticleDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
            '[:category]'  => ['article/Article/index',[],['category'=>'[a-zA-Z0-9_-]+']],
            ],[],['page'=>'\d+']);
    }   
}