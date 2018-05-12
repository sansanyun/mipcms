<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\https\controller;
use think\Request;
use think\Controller;
class GlobalAction extends Controller
{
    public function https()
    {
        $addonsName = 'https'; //配置当前插件名称
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            return false;
        }
        
        if ($this->mipInfo['httpType'] == 'https://') {
          if (Request::instance()->server()['REQUEST_SCHEME'] != "https") {
              header('HTTP/1.1 301 Moved Permanently');
              header('Location: ' . $this->domain . Request::instance()->url());
              exit();
          }
        }

    }
}
