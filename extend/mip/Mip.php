<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;
use think\Response;
use think\Config;
use think\Cache;
use think\Session;
use think\Hook;
use think\Validate;
use think\view\driver\Think;
use mip\Init;
class Mip extends Init
{
    public $mipInfo;
    public $siteUrl;
    public $domain;
    public $userInfo;
    public $rewrite;
    public $userId;
    public $tplName;
    public $dataId;
    

    public function _initialize()
    {
        parent::_initialize();
        
        $this->siteInfoInit();
        $this->headerInfo();
    }
    
    public function siteInfoInit()
    {
        $this->domainSettingsInfo = config('domainSettingsInfo');
        if ($this->domainSettingsInfo) {
            if ($this->domainSettingsInfo['siteName']) {
                $this->mipInfo['siteName'] = $this->domainSettingsInfo['siteName'];
            }
            if ($this->domainSettingsInfo['indexTitle']) {
                $this->mipInfo['indexTitle'] = $this->domainSettingsInfo['indexTitle'];
            }
            if ($this->domainSettingsInfo['keywords']) {
                $this->mipInfo['keywords'] = $this->domainSettingsInfo['keywords'];
            }
            if ($this->domainSettingsInfo['description']) {
                $this->mipInfo['description'] = $this->domainSettingsInfo['description'];
            }
            if ($this->domainSettingsInfo['icp']) {
                $this->mipInfo['icp'] = $this->domainSettingsInfo['icp'];
            }
            if ($this->domainSettingsInfo['statistical']) {
                $this->mipInfo['statistical'] = $this->domainSettingsInfo['statistical'];
            }
            if ($this->domainSettingsInfo['diySiteName']) {
                $this->mipInfo['diySiteName'] = $this->domainSettingsInfo['diySiteName'];
            }
            if ($this->domainSettingsInfo['mipApi']) {
                $this->mipInfo['mipApiAddress'] = $this->domainSettingsInfo['mipApi'];
            }
            if ($this->domainSettingsInfo['mipAutoStatus']) {
                $this->mipInfo['mipPostStatus'] = $this->domainSettingsInfo['mipAutoStatus'];
            }
            if ($this->domainSettingsInfo['ampApi']) {
                $this->mipInfo['ampApi'] = $this->domainSettingsInfo['ampApi'];
            }
            if ($this->domainSettingsInfo['ampAutoStatus']) {
                $this->mipInfo['ampAutoStatus'] = $this->domainSettingsInfo['ampAutoStatus'];
            }
            if ($this->domainSettingsInfo['xiongZhangStatus']) {
                $this->mipInfo['guanfanghaoStatus'] = $this->domainSettingsInfo['xiongZhangStatus'];
            }
            if ($this->domainSettingsInfo['xiongZhangId']) {
                $this->mipInfo['guanfanghaoCambrian'] = $this->domainSettingsInfo['xiongZhangId'];
            }
            if ($this->domainSettingsInfo['xiongZhangNewApi']) {
                $this->mipInfo['guanfanghaoRealtimeUrl'] = $this->domainSettingsInfo['xiongZhangNewApi'];
            }
            if ($this->domainSettingsInfo['xiongZhangNewAutoStatus']) {
                $this->mipInfo['guanfanghaoStatusPost'] = $this->domainSettingsInfo['xiongZhangNewAutoStatus'];
            }
            if ($this->domainSettingsInfo['xiongZhangOldApi']) {
                $this->mipInfo['guanfanghaoUrl'] = $this->domainSettingsInfo['xiongZhangOldApi'];
            }
            if ($this->domainSettingsInfo['yuanChuangApi']) {
                $this->mipInfo['baiduYuanChuangUrl'] = $this->domainSettingsInfo['yuanChuangApi'];
            }
            if ($this->domainSettingsInfo['yuanChuangAutoStatus']) {
                $this->mipInfo['baiduYuanChuangStatus'] = $this->domainSettingsInfo['yuanChuangAutoStatus'];
            }
            if ($this->domainSettingsInfo['linkApi']) {
                $this->mipInfo['baiduTimePcUrl'] = $this->domainSettingsInfo['linkApi'];
            }
            if ($this->domainSettingsInfo['linkAutoStatus']) {
                $this->mipInfo['baiduTimePcStatus'] = $this->domainSettingsInfo['linkAutoStatus'];
            }
            if ($this->domainSettingsInfo['baiduSearchKey']) {
                $this->mipInfo['biaduZn'] = $this->domainSettingsInfo['baiduSearchKey'];
            }
            if ($this->domainSettingsInfo['baiduSearchSiteMap']) {
                $this->mipInfo['baiduSearchPcUrl'] = $this->domainSettingsInfo['baiduSearchSiteMap'];
            }
            config('mipInfo',$this->mipInfo);
        }
    }

    /**
     * 标题关键词描述
     * 不同的页面根据不同的控制器控制
     * */
    public function headerInfo()
    {
        $this->assign('mipTitle',config('mipInfo')['siteName']);
        $this->assign('mipKeywords',config('mipInfo')['keywords']);
        $this->assign('mipDescription',config('mipInfo')['description']);
    }
   
    /**
     * 模板渲染处理
     * 根据后台是否一键开启模板代码压缩
     * */
    public function mipView($parent,$name = null)
    {
        $this->assign('domain',config('domain'));
        $this->assign('domainStatic',config('domainStatic'));
        $this->assign('mipInfo',config('mipInfo'));
        $tplName = config('view_name');
        $this->assign('tplName',$tplName);
        $this->assign('templateStatic', config('domainStatic') . '/template/' . $tplName . '/templateStatic');   
        $this->assign('themeStatic', config('domainStatic') . '/' . config('assets') . '/' . $tplName);
        $this->assign('siteUrl',config('domain') . $this->request->url());
        $this->assign('currentUrl',config('domain') . $this->request->url());
        
        $this->assign('config',config());
        
        $itemList = db('GlobalAction')->select();
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                $addonsName = $val['name'];
                $addonsNameSpace = "addons" . "\\" . $addonsName . "\\" . "controller" . "\\" . "GlobalAction";
                try {
                    model($addonsNameSpace)->$addonsName();
                } catch (\Exception $e) {}
            }
        }
        if ($this->mipInfo['codeCompression']) {
            return compress_html($this->fetch('/' .$parent));
        } else {
           return $this->fetch('/' .$parent);
        }
    }


}
