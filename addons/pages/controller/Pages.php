<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\pages\controller;
use think\Request;
use mip\Mip;
class Pages extends Mip
{
    protected $beforeActionList = ['start'];
    protected $addonsName = '';
    public function start()
    {
        $addonsName = 'pages'; //配置当前插件名称
        $this->addonsName = $addonsName;
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            $this->error('当前插件未启用','');
        }
    }
    
    public function index()
    {
        $urlName = $this->request->dispatch()['var']['url_name'];
        $itemInfo = db('Pages')->where('url_name',$urlName)->find();
        if (!$itemInfo) {
            return $this->error('页面不存在');
        }
        
        $itemInfo['mipContent'] = model('app\common\model\Common')->getContentFilterByContent(htmlspecialchars_decode($itemInfo['content']));
        $itemInfo['content'] = htmlspecialchars_decode($itemInfo['content']);
        //面包屑导航
        $this->assign('crumbCategoryName',$categoryInfo['name']);
        $this->assign('crumbCategoryUrl',$this->domain . '/' . $categoryInfo['url_name'] . '/');
        
        $itemInfo['cid'] = 0;
        $itemInfo['url'] = $this->domain . '/' . $itemInfo['url_name'] . '.html';
        $itemInfo['publish_time'] = time();
        $itemInfo['views'] = 0;
        $itemInfo['img_url'] = '';
        
        
        $mipTitle = $itemInfo['title'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName'];
        $this->assign('mipTitle',$mipTitle);
        $this->assign('mipKeywords',$itemInfo['keywords']);
        $this->assign('mipDescription',$itemInfo['description']);
        
        $this->assign('itemInfo',$itemInfo);
        
        return $this->addonsFetch('pages','pages');
    }
 
    
}
