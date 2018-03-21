<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;
if (!is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
    Route::rule('/','install/Install/index');
    Route::rule('/install','install/Install/index');
    Route::rule('/install/installPost','install/Install/installPost');
    Route::rule('/install/installPostOne','install/Install/installPostOne');
    Route::rule('/install/installPostTwo','install/Install/installPostTwo');
} else {
    
    $settings = db('Settings')->select();
    foreach ($settings as $k => $v) {
        $mipInfo[$v['key']] = $v['val'];
    }
    Config::set('mipInfo',$mipInfo);
    Config::set('admin','admin'); //如果修改系统管理地址，请修改后一个admin即可
    
    foreach (fetch_file_lists(ROOT_PATH . 'addons' . DS) as $key => $file) {
        if (strstr($file,'route.php')) {
            require $file;
        }
    }
    
if (!strpos($request->url(),'Api')) {
    Route::rule('/sitemap.xml','index/Index/sitemap');
    Route::rule(['xml/:id' => ['index/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
    Route::rule('/baiduSitemapPc.xml','index/Index/baiduSitemapPc');
    Route::rule(['pcXml/:id' => ['index/Index/pcXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
    Route::rule('/','index/Index/index');
    Route::group($mipInfo['tagModelUrl'], [
        '[:id]/index'.$mipInfo['urlPageBreak'].'<page>'=>  ['tag/Tag/tagDetail',['ext'=>'html'],['id'=>'^[a-zA-Z0-9_-]+'],['page'=>'\d+']],
        ':id'=>  ['tag/Tag/tagDetail',[],['id'=>'^[a-zA-Z0-9_-]+']],
    ],[],[]);

    Route::group(Config::get('admin'), [
        ':model/:action/:addonsCtr/:addonsAct/[:params]' => 'admin/Admin/index?model=:model&action=:action&params=:params&addonsAct=:addonsAct&addonsCtr=:addonsCtr',
        ':model/:action/:params' => 'admin/Admin/index?model=:model&action=:action&params=:params',
        ':model/:action/' => 'admin/Admin/index?model=:model&?action=:action',
        ':model' => 'admin/Admin/index?model=:model',
        '/' => 'admin/Admin/index',
    ],[],[]);  
    
}
    
   
    
    foreach (fetch_file_lists(APP_PATH) as $key => $file) {
        if (strstr($file,'route.php')) {
            require_once $file;
        }
    }
    
}