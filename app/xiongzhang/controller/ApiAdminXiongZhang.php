<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\xiongzhang\controller;
use think\Request;
use mip\AdminBase;
class ApiAdminXiongZhang extends AdminBase
{
    private $keyInfo;
    
    public function getAccessToken()
    {
        $settings = db('Key')->select();
        foreach ($settings as $k => $v) {
            $this->keyInfo[$v['key']] = $v['val'];
        }
        if (!$this->keyInfo['baiduXZClientId'] || !$this->keyInfo['baiduXZClientSecret']) {
            return jsonError('尚未配置开发者信息，请前去配置');
        }
        $url = 'https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id='. $this->keyInfo['baiduXZClientId'] .'&client_secret=' . $this->keyInfo['baiduXZClientSecret']; 
        $result = getData($url);
        if ($result) {
            $result = json_decode($result,true);
        } else {
            return jsonError('连接百度服务器异常');
        }
        if (empty($result['access_token'])) {
            return jsonError('配置信息错误，请前去检查');
        } else {
            return $result['access_token'];
        }
    }
}