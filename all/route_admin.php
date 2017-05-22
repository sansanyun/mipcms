<?php
namespace app\route;
use think\Route;
use think\Config;

Config::set('admin','admin'); //如果修改系统管理地址，请修改后一个admin即可

Route::group(Config::get('admin'), [
    'user' => 'pc/Admin/user',
    'article' => 'pc/Admin/article',
    'ask' => 'pc/Admin/ask',
    'setting' => 'pc/Admin/setting',
    'role' => 'pc/Admin/role',
    'role_authorization' => 'pc/Admin/role_authorization',
    'spider' => 'pc/Admin/spider',
    'update' => 'pc/Admin/update',
    'friendlink' => 'pc/Admin/friendlink',
    '/' => 'pc/Admin/index',
],[],[]);  
    