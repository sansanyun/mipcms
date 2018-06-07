<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;
if (!is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
    Route::rule('bch-install.php','install/BchInstall/index');
    Route::rule('/','install/Install/index');
    Route::rule('/install','install/Install/index');
    Route::rule('/install/installPost','install/Install/installPost');
    Route::rule('/install/installPostOne','install/Install/installPostOne');

} else {
    $request = Request::instance();
    $settings = db('Settings')->select();
    foreach ($settings as $k => $v) {
        $mipInfo[$v['key']] = $v['val'];
    }
    $tplName = $mipInfo['template'];
    $rewrite = $mipInfo['rewrite'] ? '' : '/index.php?s=';
    $domain = $request->domain() . $rewrite;
    $domainStatic = str_replace('/index.php?s=', '', $domain);
    if ($mipInfo['superSites']) {
        $domainSitesList = db('domainSites')->select();
        $domainSettingsInfo = null;
        if ($domainSitesList) {
            $siteInfo = db('domainSites')->where('domain',$request->server()['HTTP_HOST'])->find();
            if ($siteInfo) {
                $domain = $siteInfo['http_type'] . $siteInfo['domain'] . $rewrite;
                $domainStatic = $siteInfo['http_type'] . $siteInfo['domain'];
                $tplName = $siteInfo['template'];
                config('dataId',$siteInfo['id']);
                $domainSettingsInfo = db('domainSettings')->where('id',$siteInfo['id'])->find();
                config('domainSettingsInfo',$domainSettingsInfo);
            } else {
                $domain = $request->domain() . $rewrite;
                $domainStatic = str_replace('/index.php?s=', '', $domain);
            }
        }
    } else {
        $domain = $request->domain() . $rewrite;
        $domainStatic = str_replace('/index.php?s=', '', $domain);
        if ($mipInfo['domain']) {
            $domain = $mipInfo['httpType'] . $mipInfo['domain'] . $rewrite;
            $domainStatic = $mipInfo['httpType'] . $mipInfo['domain'];
        }
        if ($mipInfo['articleDomain']) {    		$rewrite = $mipInfo['rewrite'] ? '' : '/index.php?s=';			
            $domain =  $mipInfo['articleDomain'];			if (strpos($domain{(strlen(trim($domain))-1)},'/') !== false) {               $domain = substr($domain,0,strlen($domain)-1);             }            $domain = $domain . $rewrite;          	$domainStatic = str_replace('/index.php?s=', '', $domain);
        }
    }
    config('domain',$domain);
    config('domainStatic',$domainStatic);
    config('mipauthorization',false);
    config('mipInfo',$mipInfo);
    if (Request::instance()->isMobile()) {
        if ($mipInfo['superTpl']) {
            $tplName = 'm';
        }
    }
    $tplName = $tplName ? $tplName : 'default';
    config('view_name',$tplName);
    config('template.view_path',config('template.view_path') . $tplName . '/');
    
    config('admin','admin'); //如果修改系统管理地址，请修改后一个admin即可
    
    config('routeStatus',true);
    foreach (fetch_file_lists(ROOT_PATH . 'addons' . DS) as $key => $file) {
        if (strstr($file,'route.php')) {
            require $file;
        }
    }
    
if (!strpos($request->url(),'Api')) {
    
    if (config('routeStatus')) {
        Route::rule('/sitemap.xml','index/Index/sitemap');
        Route::rule(['xml/:id' => ['index/Index/xml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
        Route::rule(['tagXml/:id' => ['index/Index/tagXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
        Route::rule('/baiduSitemapPc.xml','index/Index/baiduSitemapPc');
        Route::rule(['pcXml/:id' => ['index/Index/pcXml?id=:id',['ext'=>'xml'],['id'=>'\d+']]]);
        Route::rule('/','index/Index/index');
        Route::group($mipInfo['tagModelUrl'], [
            '[:id]/index'.$mipInfo['urlPageBreak'].'<page>'=>  ['tag/Tag/tagDetail',['ext'=>'html'],['id'=>'^[a-zA-Z0-9_-]+'],['page'=>'\d+']],
            ':id'=>  ['tag/Tag/tagDetail',[],['id'=>'^[a-zA-Z0-9_-]+']],
        ],[],[]);
    }
    Route::group(Config::get('admin'), [
        ':model/:action/:addonsCtr/:addonsAct/[:params]' => 'admin/Admin/index?model=:model&action=:action&params=:params&addonsAct=:addonsAct&addonsCtr=:addonsCtr',
        ':model/:action/:params' => 'admin/Admin/index?model=:model&action=:action&params=:params',
        ':model/:action/' => 'admin/Admin/index?model=:model&?action=:action',
        ':model' => 'admin/Admin/index?model=:model',
        '/' => 'admin/Admin/index',
    ],[],[]);
    
} else {
     Route::rule(Config::get('admin').'/:ctr/:act','admin/:ctr/:act');
}
    if (config('routeStatus')) {
        foreach (fetch_file_lists(APP_PATH) as $key => $file) {
            if (strstr($file,'route.php')) {
                require_once $file;
            }
        }
    }
    
}