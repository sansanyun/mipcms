<?php
namespace app\route;
use think\Route;
use think\DB;

Route::get('/m',$mipInfo['httpType'] .$mipInfo["mipDomain"]);

Route::domain($mipInfo['mipDomain'], function() use($mipInfo) {

    Route::rule([$mipInfo['tagModelUrl'].'/[:id]/index'.$mipInfo['urlPageBreak'].'<page>'=>['m/Tag/tagDetail',['ext'=>'html'],['id'=>'^[a-zA-Z0-9]+'],['page'=>'\d+']]]);
    Route::rule([$mipInfo['tagModelUrl'].'/:id'=>['m/Tag/tagDetail',[],['id'=>'^[a-zA-Z0-9]+']]]);

    Route::rule('/','m/Index/index');
    Route::rule('/m','/');
    Route::rule('/sitemap.xml','m/Index/sitemap');
    Route::rule(['/xml/:id' => ['m/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
    Route::rule('/baiduSitemapMobile.xml','m/Index/baiduSitemapMobile');
    Route::rule(['/mobileXml/:id' => ['m/Index/mobileXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
    if ($mipInfo['aritcleLevelRemove']) {
         if ($mipInfo['urlCategory']) {
            Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['m/Article/articleDetail?sub=:sub',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/:id'=>['m/Article/articleDetail?sub=:sub',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['m/Article/articleDetail?category=:category',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule(['[:category]/:id'=>['m/Article/articleDetail?category=:category',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['m/Article/index?sub=:sub',[],['category'=>'[a-z]+','sub'=>'[a-z]+']]]);
            Route::rule(['[:category]' => ['m/Article/index',[],['category'=>'[a-z]+']]]);
            Route::rule($mipInfo['articleModelUrl'],'m/Article/index');
        } else {
            Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['/index' => ['m/Article/index',['ext'=>'html'],['id'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'\w+'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['m/Article/index?sub=:sub',[],[]]]);
            Route::rule(['[:category]' => ['m/Article/index',[],[]]]);
            Route::rule(['[:category]/[:keys]' => ['m/Article/index',['ext'=>'html'],['keys'=>'index']]]);
            Route::rule($mipInfo['articleModelUrl'],'m/Article/index');
        }
    } else {
        Route::rule([$mipInfo['articleModelUrl'].'/[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule([$mipInfo['articleModelUrl'].'/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'\w+'],['id'=>'[a-zA-Z0-9_-]+']]]);
        Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
        Route::rule([$mipInfo['articleModelUrl'].'/[:category]' => ['m/Article/index',[],[]]]);
    }
},['ext'=>'html'],[]);

