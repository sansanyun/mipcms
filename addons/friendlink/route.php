<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;


    
Route::rule(Config::get('admin') . '/ApiAdminLink/:action' ,'\\think\\addons\\Route@execute/friendlink/ApiAdminLink/:action');