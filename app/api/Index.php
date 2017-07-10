<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use think\Loader;
use app\api\Users;
use mip\AuthBase;
class Index extends AuthBase
{
    public function index(){
        if (Request::instance()->isPost()) {
        	
        } else {
        	echo '欢迎使用MIPCMS内容管理系统api接口';
        }
        
    }
}