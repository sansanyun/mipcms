<?php
namespace addons\ad;

use think\Addons;
use mip\Init;
class Ad extends Addons
{
    public $info = [
        'name' => 'ad',
        'title' => '广告管理',
        'description' => '广告插件',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.0',
        'adminUrl' => 'addons/ad/AdminAd/adList'
    ];
    
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
    
    public function adHook($param)
    {
        $addonsName = 'ad'; //配置当前插件名称
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            return false;
        }
        
        if (!$param) {
            return false;
        }
        $adList = db('ad')->where('name',$param)->find();
        if ($adList) {
            $adList['content'] = htmlspecialchars_decode($adList['content']);
        } else {
            return false;
        }
        $this->assign('itemInfo',$adList);
        return $this->fetch($addonsName);
    }
    
    
}
