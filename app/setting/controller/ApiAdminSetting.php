<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\setting\controller;
use think\Request;
use mip\AdminBase;
class ApiAdminSetting extends AdminBase
{
    public function settingSelect(){

        $settings = db($this->settings)->select();
        foreach ($settings as $k => $v){
            $this->mipInfo[$v['key']] = $v['val'];
        }
        return jsonSuccess('',$this->mipInfo);
    }
    

    public function settingEdit(){

        $settingInfo = json_decode(input('post.setting'));

        foreach ($settingInfo as $key => $val) {
            db($this->settings)->where( "`key` = '" . $key . "'")->update(array('val' => $val));
        }
        return jsonSuccess('保存成功');
    }
    
    public function settingSave() {
        $domain = input('post.domain');
        $siteName = input('post.siteName');
        $httpType = input('post.httpType');
        
        db($this->settings)->where('key','domain')->update(array('val' => $domain));
        db($this->settings)->where('key','siteName')->update(array('val' => $siteName));
        db($this->settings)->where('key','httpType')->update(array('val' => $httpType));
        
        if (input('post.setting')) {
            $settingInfo = json_decode(input('post.setting'));
            foreach ($settingInfo as $key => $val) {
                db($this->settings)->where( "`key` = '" . $key . "'")->update(array('val' => $val));
            }
        }
        return jsonSuccess('保存成功');
    }


}