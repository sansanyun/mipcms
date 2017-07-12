<?php
namespace app\route;
use think\Route;
use think\DB;

Route::get('/m',$mipInfo['httpType'] .$mipInfo["mipDomain"]);
Route::domain($mipInfo['mipDomain'], function() use($mipInfo) {
    Route::rule('/','m/Index/index');
    Route::rule('/m','/');
    Route::rule('/sitemap.xml','m/Index/sitemap');
    Route::rule(['/xml/:id' => ['m/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
    Route::rule('login','m/Account/login');
    Route::rule('register','m/Account/register');
    Route::rule([$mipInfo['articleModelUrl'].'/[:category]/index_<page>$' => 'm/Article/index']);
    Route::rule([$mipInfo['articleModelUrl'].'/index_<page>$' => ['m/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
    Route::rule([$mipInfo['articleModelUrl'].'/[:category]'  => ['m/Article/index',[],['category'=>'^[a-zA-Z]+$']]]);
    Route::rule([$mipInfo['articleModelUrl'].'/:id' => ['m/Article/articleDetail',['ext'=>'html'],['id'=>'^[A-z\d]{0,24}$']]]);
    Route::rule([$mipInfo['articleModelUrl'].'/[:diy]'=>  ['pc/Article/articleDetail?diy=:diy',['ext'=>'html'],[]]]);

},['ext'=>'html'],['id'=>'\d+','name'=>'\w+']);
