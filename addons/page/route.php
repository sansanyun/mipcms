<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;
    

Route::rule(Config::get('admin') . '/ApiAdminPage/:action' ,'\\addons\\page\\controller\\ApiAdminPage@:action');

try {
    $pages = db('Page')->select();
    if ($pages) {
        foreach ($pages as $key => $val) {
            Route::rule([$val['url_name'] => ['\\addons\\page\\controller\\Page@index?url_name='.$val['url_name'],['ext'=>'html'],[]]]);
        }
    }
} catch (\Exception $e) {
     
}