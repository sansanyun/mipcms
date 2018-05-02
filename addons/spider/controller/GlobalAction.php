<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\spider\controller;
use think\Request;
use think\Controller;
class GlobalAction extends Controller
{
    public function spider()
    {
        $addonsName = 'spider'; //配置当前插件名称
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            return false;
        }
        $userAgent = @Request::instance()->header()['user-agent'];
        if (strpos($userAgent,"Baiduspider")) {
            
            if (strpos($userAgent,"Mobile")) {
                if (strpos($userAgent,"render")) {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobileRender','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                } else {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobile','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                }
            } else {
                if (strpos($userAgent,"render")) {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pcRender','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                } else {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pc','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                }
            }
            
        }

    }
}
