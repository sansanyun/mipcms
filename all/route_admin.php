<?php
namespace app\route;
use think\Route;
use think\Config;

Config::set('admin','admin'); //如果修改系统管理地址，请修改后一个admin即可

Route::group(Config::get('admin'), [
    ':action' => 'pc/Admin/index?model=:action',
    '/' => 'pc/Admin/index',
],[],[]);  
    