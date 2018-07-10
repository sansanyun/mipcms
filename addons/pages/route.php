<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;
    

Route::rule(Config::get('admin') . '/ApiAdminPages/:action' ,'\\addons\\pages\\controller\\ApiAdminPages@:action');

try {
    $pages = db('Pages')->select();
    if ($pages) {
        foreach ($pages as $key => $val) {
            Route::rule([$val['url_name'] => ['\\addons\\pages\\controller\\Pages@index?url_name='.$val['url_name'],['ext'=>'html'],[]]]);
        }
    }
} catch (\Exception $e) {
     
}