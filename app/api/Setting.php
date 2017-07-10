<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use app\api\model\Settings;
use mip\AuthBase;
class Setting extends AuthBase
{
    public function settingSelect(){
        if (Request::instance()->isPost()) {
            
            $settings = Settings::select();
            foreach ($settings as $k => $v){
                $this->mipInfo[$v['key']] = $v['val'];
            }
            return jsonSuccess('',$this->mipInfo);
        }
    }
    
    public function settingEdit(){
        if (Request::instance()->isPost()) {
            
            $settingInfo = json_decode(input('post.setting'));
            
            foreach ($settingInfo as $key => $val) {
                Settings::where( "`key` = '" . $key . "'")->update(array('val' => $val));
            }
            return jsonSuccess('保存成功');
        }
    }
    
    
}