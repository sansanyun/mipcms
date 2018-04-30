<?php
namespace addons\friendlink;

use think\Addons;
use think\Request;
use mip\Init;
class Friendlink extends Addons
{
    public $info = [
        'name' => 'friendlink',
        'title' => '友情链接',
        'description' => '模板调用代码 {:hook("friendlinkHook")}',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.1',
        'adminUrl' => 'addons/friendlink/AdminFriendlink/friendlink'
    ];
    
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
    
    public function friendlinkHook()
    {
        $addonsName = 'friendlink'; //配置当前插件名称
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            return false;
        }
        $friendLink = db('Friendlink')->order('sort ASC')->select();
        $friendLink['friendLinkAll'] = false;
        $friendLink['friendLinkIndex'] = false;
        $friendLink['friendLinkNotIndex'] = false;
        
        $request = Request::instance();
        $this->assign('mod',$request->module());
        $this->assign('ctr',$request->controller());
        $this->assign('act',$request->action());
        
        foreach ($friendLink as $key => $val) {
            if ($val['type'] == 'all') {
                $friendLink['friendLinkAll'] = true;
            }
            if ($val['type'] == 'index') {
                $friendLink['friendLinkIndex'] = true;
            }
            if ($val['type'] == 'notIndex') {
                $friendLink['friendLinkNotIndex'] = true;
            }
        }
        $this->assign('friendLink',$friendLink);
        
        return $this->fetch($addonsName);
    }
    
    
}
