<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\admin\controller;
use think\Request;

use mip\AdminBase;
class ApiAdminDomainSettings extends AdminBase
{
    public function index() {

    }
    
    public function urlPost(Request $request) {
        $postAddress = input('post.postAddress');
        if (!$postAddress) {
            return jsonError('请先去设置推送的接口');
        }
        $pushData = json_decode(input('post.pushData'),true);
        if (!$pushData) {
            return jsonError('没有检测到你推送的页面地址');
        }
        if (is_array($pushData)) {
            $urls = array();
            foreach ($pushData as $key => $val) {
                $urls[] = $val['url'];
            }
            $api = $postAddress;
            $ch = curl_init();
            $options =  array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urls),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);
            return jsonSuccess($result);
        } else {
            return jsonError('数据格式错误');
        }
    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $settingInfo = json_decode(input('post.setting'));
        foreach ($settingInfo as $key => $val) {
            db('domainSettings')->where('id',$id)->update(array($key => $val));
        }
        return jsonSuccess('成功');
    }
  
    public function itemFind()
    {
        $id = input('post.id');
        $itemInfo = db('domainSettings')->where('id',$id)->find();
        return jsonSuccess('成功',$itemInfo);
    }
      
}