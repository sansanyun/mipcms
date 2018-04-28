<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;

$mipInfo = Config::get('mipInfo');
$request = Request::instance();

if (!strpos($request->url(),'Api')) {
    $categoryList = model('app\\article\\model\\Articles')->getAllCategory();
    if ($categoryList) {
        foreach ($categoryList as $key => $value) {
            Route::rule([$value['pageRule'] => ['article/Article/index?id=' . $value["id"] . '&cid=' . $value["id"],[],[]]]);
            
            Route::rule([str_replace('.html','',$value['detailRule']).$mipInfo['urlPageBreak'].'<page>' => ['article/ArticleDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);

            Route::rule([str_replace('.html','',$value['detailRule']) => ['article/ArticleDetail/index',['ext'=>'html'],['__url__' => $value['detail__url__']],[]]]);
            Route::rule([$value['rule'] => ['article/Article/index?id=' . $value["id"] . '&cid=' . $value["id"],[],[]]]);
        }
    } else {
        Route::rule(['/article/:id' => ['article/ArticleDetail/index',['ext'=>'html'],[],[]]]);
    }
    Route::rule('article','article/Article/index');
}