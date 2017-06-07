<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use app\api\model\RolesNode;
use app\api\model\RolesAccess;

use mip\AuthBase;
class MipPost extends AuthBase
{
    public function index() {
        
    }
    
    public function mippost(Request $request){
		if (Request::instance()->isPost()) {
		    
            $mipApiAddress = input('post.mipApiAddress');
            if (!$mipApiAddress) {
                return jsonError('请先去设置百度MIP的接口');
            }
            $urls = input('post.urls');
            if (!$urls) {
                return jsonError('没有检测到你推送的MIP页面地址');
            }
            $urls = explode(',',$urls);
            if (is_array($urls)) {
                $api = $mipApiAddress;
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
                return jsonSuccess($result);
            } else {
                return jsonError('数据格式错误');
            }
		}
    }

}