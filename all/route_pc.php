<?php
namespace app\route;
use think\Route;
use think\DB;

Route::rule('/sitemap.xml','pc/Index/sitemap');
Route::rule(['xml/:id' => ['pc/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
Route::rule('/','pc/Index/index');
Route::rule('search/[:keywords]','pc/Search/index?keywords=:keywords');
Route::rule('login','pc/Account/login');
Route::rule('register','pc/Account/register');


Route::group($mipInfo['userModelUrl'], [
    ':id'=>  ['pc/User/userDetail',['ext'=>'html'],['id'=>'^[A-z\d]{0,24}$']],
    ':action'  => ['pc/User/:action'],
],[],[]);

Route::group($mipInfo['tagModelUrl'], [
    '[:id]/index_<page>'=>  ['pc/Tag/tagDetail',['ext'=>'html'],['id'=>'^[a-zA-Z0-9]+'],['page'=>'\d+']],
    ':id'=>  ['pc/Tag/tagDetail',[],['id'=>'^[a-zA-Z0-9]+']],
],[],[]);


if ($mipInfo['superSites']) {
    $articlesCategory = db('ArticlesCategory')->where('pid',0)->select();
    foreach ($articlesCategory as $k => $v) {
        $url_name = $v['url_name'];
        Route::domain($url_name, function() use($url_name,$mipInfo) {
            Route::rule('ApiUser/:ctr/:action','ApiUser/:ctr/:action');
            Route::rule('ApiAdmin/:ctr/:action','ApiAdmin/:ctr/:action');
            Route::rule('/','pc/Index/index?category='.$url_name);

            Route::rule(['[:sub]/index_<page>' => ['pc/Article/index?category='.$url_name.'&sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/<id>_<page>'=>['pc/Article/articleDetail',['ext'=>'html'],[['id'=>'[a-zA-Z0-9_-]+']],['page'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:sub]' => ['pc/Article/index?category='.$url_name.'&sub=:sub',[],[]]]);
        });
    }


} else {
    if ($mipInfo['articleDomain']) {
        Route::domain($mipInfo['articleDomain'], function() use($mipInfo) {
            Route::rule('ApiUser/:ctr/:action','ApiUser/:ctr/:action');
            Route::rule('ApiAdmin/:ctr/:action','ApiAdmin/:ctr/:action');

            Route::rule(['[:category]/index_<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['index_<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['/index' => ['pc/Article/index',['ext'=>'html'],['id'=>'\d+']]]);
            Route::rule(['/publish' => ['pc/Article/publish',['ext'=>'html'],[]]]);
            Route::rule([$mipInfo['articleModelUrl'].'/<id>_<page>'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['pc/Article/index?sub=:sub',[],[]]]);
            Route::rule(['[:category]' => ['pc/Article/index',[],['category'=>'[a-zA-Z]+']]]);
            Route::rule(['[:category]/[:keys]' => ['pc/Article/index',['ext'=>'html'],['keys'=>'index']]]);
            Route::rule($mipInfo['articleModelUrl'],'pc/Article/index');


        },[],[]);
    } else {
        if ($mipInfo['aritcleLevelRemove']) {
            Route::rule('ApiUser/:ctr/:action','ApiUser/:ctr/:action');
            Route::rule('ApiAdmin/:ctr/:action','ApiAdmin/:ctr/:action');

            Route::rule(['[:category]/index_<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['[:category]/[:sub]/index_<page>' => ['pc/Article/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['index_<page>' => ['pc/Article/index',['ext'=>'html'],['page'=>'\d+']]]);
            Route::rule(['/publish' => ['pc/Article/publish',['ext'=>'html'],[]]]);
            Route::rule([$mipInfo['articleModelUrl'].'/<id>_<page>'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+'],['page'=>'\d+']]]);
            Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
            Route::rule(['[:category]/[:sub]' => ['pc/Article/index?sub=:sub',[],['category'=>'[a-z]+','sub'=>'[a-z]+']]]);
            Route::rule(['[:category]' => ['pc/Article/index',[],['category'=>'[a-z]+']]]);
            Route::rule($mipInfo['articleModelUrl'],'pc/Article/index');

        } else {
            Route::group($mipInfo['articleModelUrl'], [
                '[:category]/index_<page>'  =>['pc/Article/index',['ext'=>'html'],[]],
                'index_<page>'=>['pc/Article/index',['ext'=>'html'],[]],
                '/publish'  =>['pc/Article/publish',['ext'=>'html'],[]],
                '<id>_<page>'=>  ['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
                ':id'=>  ['pc/Article/articleDetail',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']],
                '[:category]'  => ['pc/Article/index',[],['category'=>'[a-zA-Z]+']],
                ],[],['page'=>'\d+']);
        }
    }
}



Route::group($mipInfo['askModelUrl'], [
    '[:category]/index_<page>'  =>'pc/Ask/index',
    'index_<page>'=>'pc/Ask/index',
    '/index'  =>['pc/Ask/index',['ext'=>'html'],['id'=>'\d+']],
    '/publish/[:id]'  =>['pc/Ask/publish?id=:id',[],['id'=>'^[A-z\d]{0,24}$']],
    '/publish'  =>['pc/Ask/publish',[],[]],
    '[:category]/tab_<type>'  => ['pc/Ask/index?type=:type',[],['id'=>'\d+']],
    '[:category]'  => ['pc/Ask/index',[],['id'=>'\d+']],
    'tab_<type>'  => ['pc/Ask/index?type=:type',[],[]],
    '[:category]/[:keys]'  =>['pc/Ask/index',['ext'=>'html'],['id'=>'\d+']],
    '<id>_<page>'=>  ['pc/Ask/articleDetail',['ext'=>'html'],['id'=>'\w+']],
    ':id'=>  ['pc/Ask/askDetail',[],['id'=>'\w+']],
],[],['category'=>'[a-zA-Z]+','page'=>'\d+','ext'=>'html','keys'=>'index']);

