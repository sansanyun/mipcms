<?php
namespace app\route;
use think\Route;
use think\DB;

if (!is_file(CONF_PATH . 'install' . DS .'install.lock')) {
    Route::rule('/','pc/Install/index');
    Route::rule('/install','pc/Install/index');
    Route::rule('/install/installPost','pc/Install/installPost');
} else {
    Route::rule('/sitemap.xml','pc/Index/sitemap');
    $settings = db('Settings')->select();
    global $mipInfo;
    foreach ($settings as $k => $v){
        if (is_serialized($v['val'])){
            $v['val'] =@unserialize($v['val']);
        }
        $mipInfo[$v['key']] = $v['val'];
    }

    Route::rule('/m',function(){
        $settings = db('Settings')->select();
        foreach ($settings as $k => $v){
            if (is_serialized($v['val'])){
                $v['val'] =@unserialize($v['val']);
            }
            $mipInfo[$v['key']] = $v['val'];
        }
        if ($mipInfo['mipDomain']) {
            header('Location: ' . 'http://'.$mipInfo['mipDomain']);
            exit();
        }
    });
    if ($mipInfo['mipDomain']) {
        Route::domain($mipInfo['mipDomain'], function(){
            
            $settings = db('Settings')->select();
            foreach ($settings as $k => $v){
                if (is_serialized($v['val'])){
                    $v['val'] =@unserialize($v['val']);
                }
                $mipInfo[$v['key']] = $v['val'];
            }
            Route::rule('/','m/Index/index');
            Route::rule('/m',function(){
                $settings = db('Settings')->select();
                foreach ($settings as $k => $v){
                    if (is_serialized($v['val'])){
                        $v['val'] =@unserialize($v['val']);
                    }
                    $mipInfo[$v['key']] = $v['val'];
                }
                if ($mipInfo['mipDomain']) {
                    header('Location: ' . 'http://'.$mipInfo['mipDomain']);
                    exit();
                }
            });
            Route::rule('/login','m/Account/login');
            Route::rule('/register','m/Account/register');
            if ($mipInfo['systemType'] == 'Blog' || $mipInfo['systemType'] == 'CMS' || $mipInfo['systemType'] == 'SNS') {
                Route::rule([$mipInfo['articleModelUrl'].'/[:category]/index_<page>' => 'm/Article/index']);
                Route::rule([$mipInfo['articleModelUrl'].'/index_<page>' => 'm/Article/index']);
                Route::rule([$mipInfo['articleModelUrl'].'/:id'=>['m/Article/articleDetail',['ext'=>'html'],['id'=>'\w+']]]);
                Route::rule([$mipInfo['articleModelUrl'].'/[:category]'  => ['m/Article/index',[],['id'=>'\d+']]]);
                Route::rule([$mipInfo['articleModelUrl'].'/cid_<id>/index_<page>' => ['m/Article/index?category=:id',['ext'=>'html'],['id'=>'\d+']]]);
                Route::rule([$mipInfo['articleModelUrl'].'/cid_<id>' =>['m/Article/index?category=:id',[],['id'=>'\d+']]]);
                Route::rule([$mipInfo['articleModelUrl'].'/' => 'm/Article/index']);
            }
        },['ext'=>'html'],['id'=>'\d+','name'=>'\w+']);
    }
    Route::rule('/','pc/Index/index');
    Route::rule('login','pc/Account/login');
    Route::rule('register','pc/Account/register');
    if ($mipInfo['systemType'] == 'Blog' || $mipInfo['systemType'] == 'CMS' || $mipInfo['systemType'] == 'SNS') {
         Route::group($mipInfo['articleModelUrl'], [
        'cid_<id>/index_<page>'  => ['pc/Article/index?category=:id',['ext'=>'html'],['id'=>'\d+']], 
        '[:category]/index_<page>'  =>'pc/Article/index',
        'index_<page>'=>'pc/Article/index',
        '/index'  =>['pc/Article/index',['ext'=>'html'],['id'=>'\d+']], 
        '[:category]'  => ['pc/Article/index',[],['id'=>'\d+']],
        'cid_<id>'  => ['pc/Article/index?category=:id',[],['id'=>'\d+']],
        '[:category]/[:keys]'  =>['pc/Article/index',['ext'=>'html'],['id'=>'\d+']], 
        ':id'=>  ['pc/Article/articleDetail',['ext'=>'html'],['id'=>'\w+']],
        ],[],['category'=>'[a-zA-Z]+','page'=>'\d+','ext'=>'html','keys'=>'index']);  
    }
    if ($mipInfo['systemType'] == 'ASK' || $mipInfo['systemType'] == 'SNS' ) {
    Route::group($mipInfo['askModelUrl'], [
        'cid_<id>/index_<page>'  => ['pc/Ask/index?category=:id',['ext'=>'html'],['id'=>'\d+']], 
        '[:category]/index_<page>'  =>'pc/Ask/index',
        'index_<page>'=>'pc/Ask/index',
        '/index'  =>['pc/Ask/index',['ext'=>'html'],['id'=>'\d+']], 
        '[:category]'  => ['pc/Ask/index',[],['id'=>'\d+']],
        'cid_<id>'  => ['pc/Ask/index?category=:id',[],['id'=>'\d+']],
        '[:category]/[:keys]'  =>['pc/Ask/index',['ext'=>'html'],['id'=>'\d+']], 
        ':id'=>  ['pc/Ask/askDetail',['ext'=>'html'],['id'=>'\w+']],
        ],[],['category'=>'[a-zA-Z]+','page'=>'\d+','ext'=>'html','keys'=>'index']);  
    }
}

Route::rule('admin','pc/Admin/index');
Route::rule('admin/user','pc/Admin/user');
Route::rule('admin/article','pc/Admin/article');
Route::rule('admin/ask','pc/Admin/ask');
Route::rule('admin/setting','pc/Admin/setting');
Route::rule('admin/role','pc/Admin/role');
Route::rule('admin/role_authorization','pc/Admin/role_authorization');
Route::rule('admin/spider','pc/Admin/spider');
Route::rule('admin/update','pc/Admin/update');
Route::rule('admin/friendlink','pc/Admin/friendlink');
$tpl_path = config('template')['view_path'];
foreach (fetch_file_lists($tpl_path) as $key => $file){
    if(strstr($file,'route.php')){
        require_once $file;
    }
}