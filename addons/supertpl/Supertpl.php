<?php
namespace addons\supertpl;

use think\Addons;
use mip\Init;
class Supertpl extends Addons
{
    public $info = [
        'name' => 'supertpl',
        'title' => '超级模板',
        'description' => '单域名模式下，PC端访问默认模板，移动端访问m模板。取消该功能需卸载该插件',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.0',
    ];
    
    public function install()
    {
        db('settings')->where('key','superTpl')->update(array('val' => 1));
        return true;
    }

    public function uninstall()
    {
        db('settings')->where('key','superTpl')->update(array('val' => ''));
        return true;
    }
    
    public function supertplHook()
    {
        
    }
    
    
}
