<?php
namespace addons\page;

use think\Addons;
use mip\Init;
class Page extends Addons
{
    public $info = [
        'name' => 'page',
        'title' => '单页面',
        'description' => '单页面',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.0',
        'adminUrl' => 'addons/page/AdminPage/page'
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
        $addonsName = 'page'; //配置当前插件名称
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            return false;
        }
        if (!$param) {
            return false;
        }
        
        return $this->fetch($addonsName);
    }
    
    
}
