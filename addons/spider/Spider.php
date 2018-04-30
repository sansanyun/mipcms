<?php
namespace addons\spider;

use think\Addons;
use mip\Init;
class Spider extends Addons
{
    public $info = [
        'name' => 'spider',
        'title' => '蜘蛛统计',
        'description' => '蜘蛛统计（百度）',
        'status' => 0,
        'author' => '团长',
        'version' => '1.1.0',
        'adminUrl' => 'addons/spider/AdminSpider/spider',
        'isGlobalAction' => 1,
        
    ];
    
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
    
    public function spiderHook()
    {
        $addonsName = 'spider'; //配置当前插件名称
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            return false;
        }
        
        return $this->fetch($addonsName);
    }
    
    
}
