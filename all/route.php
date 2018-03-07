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
    $pages = [];
    if (is_dir(ROOT_PATH . 'template' . DS . $mipInfo['template'] . DS . 'view')) {
        $templateFile = opendir(ROOT_PATH . 'template' . DS . $mipInfo['template'] . DS . 'view');
        if ($templateFile) {
            while (false !== ($file = readdir($templateFile))) {
                if (substr($file, 0, 1) != '.' AND is_file(ROOT_PATH . 'template' . DS . $mipInfo['template'] . DS . 'view' . DS . $file)) {
                    $pages[] = $file;
                }
            }
            closedir($templateFile);
        }
    }
    if ($pages) {
        foreach ($pages as $key => $val) {
            $pages[$key] = preg_replace("/.html/","",$val);
            Route::rule([$pages[$key].'/:params' => ['view/View/index?name=' . $pages[$key] . '&params=:params',[],[]]]);
            Route::rule([$pages[$key] => ['view/View/index?name=' . $pages[$key],[],[]]]);
        }
    }
    
    if (!strpos($request->url(),'Api')) {
        
        Route::rule([$mipInfo['productModelUrl'].'/index'.$mipInfo['urlPageBreak'].'<page>' => ['product/Product/index',['ext'=>'html'],['page'=>'\d+']]]); // /product/index_1.html
        
        Route::rule([$mipInfo['productModelUrl'].'/[:category]/index'.$mipInfo['urlPageBreak'].'<page>'  => ['product/Product/index',['ext'=>'html'],[]]]); // /product/seo/index_1.html
            
        Route::rule([$mipInfo['productModelUrl'].'/:id'  => ['product/productDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]); // /product/11.html
        
        Route::rule([$mipInfo['productModelUrl'].'/[:category]'  => ['product/Product/index',[],['category'=>'[a-zA-Z0-9_-]+']]]); // /product/seo/
        
        Route::rule($mipInfo['productModelUrl'],'product/Product/index'); //  /product/
        
        Route::rule([$mipInfo['productModelUrl'].'/[:category]/[:sub]' => ['product/Product/index?sub=:sub',[],['category'=>'[a-zA-Z0-9_-]+','sub'=>'[a-zA-Z0-9_-]+']]]); // /seo/sem/
        
        Route::rule([$mipInfo['productModelUrl'].'/[:category]/[:sub]/index'.$mipInfo['urlPageBreak'].'<page>' => ['product/Product/index?sub=:sub',['ext'=>'html'],['page'=>'\d+']]]);  // /seo/sem/index_12.html
    }
    
    foreach (fetch_file_lists(APP_PATH) as $key => $file) {
        if (strstr($file,'route.php')) {
            require_once $file;
        }
    }
    
}