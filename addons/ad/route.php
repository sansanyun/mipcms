<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;
    
Route::rule('/ad/ApiAdminAd/:action' ,'\\addons\\ad\\controller\\ApiAdminAd@:action');
Route::rule(Config::get('admin') . '/ApiAdminAd/:action' ,'\\addons\\ad\\ApiAdminAd@:action');