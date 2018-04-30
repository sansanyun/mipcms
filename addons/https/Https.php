<?php
namespace addons\https;

use think\Addons;
use mip\Init;
class Https extends Addons
{
    public $info = [
        'name' => 'https',
        'title' => 'https跳转',
        'description' => '强制https跳转',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.0',
        'adminUrl' => '',
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
    
    public function httpsHook()
    {
        $addonsName = 'https'; //配置当前插件名称
        
        return $this->fetch();
    }
    
    
}
