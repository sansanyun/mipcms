<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\xiongzhang\controller;
use think\Request;
use mip\AdminBase;
class ApiAdminXiongZhangUser extends AdminBase
{
    public function getUserId()
    {
        
    }
    public function getUserList()
    {
        $accessToken = model('app\xiongzhang\controller\ApiAdminXiongZhang')->getAccessToken();
        $url = 'https://openapi.baidu.com/rest/2.0/cambrian/user/get?start_index=0&access_token='. $accessToken;
        $result = getData($url);
        if ($result) {
            $result = json_decode($result,true);
            if ($result['count'] != 0) {
                $openidArray = [];
                foreach ($result['data'] as $key => $val) {
                    $openidArray[]['openid'] = $val;
                }
                $subUrl = 'https://openapi.baidu.com/rest/2.0/cambrian/user/info?access_token=' . $accessToken;
                $postArray = array(
                    "user_list" => $openidArray
                );
                $subResult = getData($subUrl,json_encode($postArray));
                if ($subResult) {
                    $subResult = json_decode($subResult,true);
                }
                $result['itemList'] = $subResult;
            }
            if (!empty($result['error_msg'])) {
                return jsonError($result['error_msg']);
            }
        }
        return jsonSuccess('',$result);
    }
    
}