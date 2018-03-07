<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\xiongzhang\controller;
use think\Request;
use mip\AdminBase;
class ApiAdminXiongZhangDiy extends AdminBase
{
    public function itemList()
    {
        $accessToken = model('app\xiongzhang\model\xiongZhang')->getAccessToken();
        $url = 'https://openapi.baidu.com/rest/2.0/cambrian/menu/get?access_token='. $accessToken;
        $result = getData($url);
        if ($result) {
            $result = json_decode($result,true);
            
        }
        return jsonSuccess('',$result);
    }
    public function itemEdit()
    {
        $button = input('post.button');
        if (!$button) {
          return jsonError('缺少参数');
        }
        $accessToken = model('app\xiongzhang\model\xiongZhang')->getAccessToken();
        $subUrl = 'https://openapi.baidu.com/rest/2.0/cambrian/menu/create?access_token=' . $accessToken;
        $postArray = array(
            "menues" => json_encode(array(
                'button' => json_decode($button)
            )),
        );
        $result = getData($subUrl,$postArray);
        if ($result) {
            $result = json_decode($result,true);
        }
        if (!empty($result['error_msg'])) {
            return jsonSuccess('',$result['error_msg']);
        }
        return jsonSuccess('',$result);
    }
    
}