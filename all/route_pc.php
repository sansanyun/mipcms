<?php
namespace app\route;
use think\Route;
use think\DB;

Route::rule('/sitemap.xml','pc/Index/sitemap');
Route::rule(['xml/:id' => ['pc/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
Route::rule('/baiduSitemapPc.xml','pc/Index/baiduSitemapPc');
Route::rule(['pcXml/:id' => ['pc/Index/pcXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
Route::rule('/','pc/Index/index');
Route::rule('search/[:keywords]','pc/Search/index?keywords=:keywords');
Route::rule('login','pc/Account/login');
Route::rule('register','pc/Account/register');


Route::group($mipInfo['userModelUrl'], [
    ':id'=>  ['pc/User/userDetail',['ext'=>'html'],['id'=>'^[A-z\d]{0,24}$']],
    ':action'  => ['pc/User/:action'],
],[],[]);

Route::group($mipInfo['tagModelUrl'], [
    '[:id]/index'.$mipInfo['urlPageBreak'].'<page>'=>  ['pc/Tag/tagDetail',['ext'=>'html'],['id'=>'^[a-zA-Z0-9]+'],['page'=>'\d+']],
    ':id'=>  ['pc/Tag/tagDetail',[],['id'=>'^[a-zA-Z0-9]+']],
],[],[]);

 
if ($mipInfo['aritcleLevelRemove']) {
    Route::rule('ApiUser/:ctr/:action','ApiUser/:ctr/:action');
    Route::rule('ApiAdmin/:ctr/:action','ApiAdmin/:ctr/:action');

    if ($mipInfo['urlCategory']) {
        Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule(['[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule(['[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['pc/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule(['[:category]/[:sub]/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['pc/Article/articleDetail?sub=:sub',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
        Route::rule(['[:category]/[:sub]/:id'=>['pc/Article/articleDetail?sub=:sub',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
        Route::rule(['[:category]/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['pc/Article/articleDetail?category=:category',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
        Route::rule(['[:category]/:id'=>['pc/Article/articleDetail?category=:category',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
        Route::rule(['[:category]/[:sub]' => ['pc/Article/index?sub=:sub',[],['category'=>'[a-z]+','sub'=>'[a-z]+']]]);
        Route::rule(['[:category]' => ['pc/Article/index',[],['category'=>'[a-z]+']]]);
      
        Route::rule($mipInfo['articleModelUrl'],'pc/Article/index');
    } else {
        Route::rule([$mipInfo['articleModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule(['[:category]/index'.$mipInfo['urlPageBreak'].'<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule(['[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['pc/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
        Route::rule([$mipInfo['articleModelUrl'].'/<id>'.$mipInfo['urlPageBreak'].'<page>'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
        Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
        Route::rule(['[:category]/[:sub]' => ['pc/Article/index?sub=:sub',[],['category'=>'[a-z]+','sub'=>'[a-z]+']]]);
        Route::rule(['[:category]' => ['pc/Article/index',[],['category'=>'[a-z]+']]]);
        Route::rule($mipInfo['articleModelUrl'],'pc/Article/index');
    }

} else {
    Route::group($mipInfo['articleModelUrl'], [
        '[:category]/index'.$mipInfo['urlPageBreak'].'<page>'  =>['pc/Article/index',['ext'=>'html'],[]],
        'index'.$mipInfo['urlPageBreak'].'<page>'=>['pc/Article/index',['ext'=>'html'],[]],
        '/publish'  =>['pc/Article/publish',['ext'=>'html'],[]],
        '<id>'.$mipInfo['urlPageBreak'].'<page>'=>  ['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
        ':id'=>  ['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
        '[:category]'  => ['pc/Article/index',[],['category'=>'[a-zA-Z]+']],
        ],[],['page'=>'\d+']);
}
 