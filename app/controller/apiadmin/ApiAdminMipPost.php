<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use think\Request;
use app\model\RolesNode;
use app\model\RolesAccess;

use mip\AdminBase;
class ApiAdminMipPost extends AdminBase
{
    public function index() {

    }

    public function mipPost(Request $request) {
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
    public function urlPost(Request $request) {
        if (Request::instance()->isPost()) {

            $urlApiAddress = input('post.urlApiAddress');
            if (!$urlApiAddress) {
                return jsonError('请先去设置百度推送的接口');
            }
            $urls = input('post.urls');
            if (!$urls) {
                return jsonError('没有检测到你推送的页面地址');
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
    public function getUrlName(Request $request)
    {
        if (Request::instance()->isPost()) {

            $title = input('post.title');
            if (!$title) {
                return jsonError('请先输入标题');
            }
            $api = 'http://fanyi.baidu.com/v2transapi';
            $ch = curl_init();
            $c_url = $api;
            $c_url_data = "from=zh&to=en&transtype=trans&query=".$title;
            curl_setopt($ch, CURLOPT_URL,$c_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $c_url_data);
            $result = curl_exec($ch);
            curl_close ($ch);
            $result = json_decode($result,TRUE);
            $result = $result['trans_result']['data'][0]['dst'];
            $result = strtolower($result);
            if (strpos($result,',')) {
                $result = explode(',',$result);
                $result = implode('',$result);
            }
            if (strpos($result,"'")) {
                $result = explode("'",$result);
                $result = implode('',$result);
            }
            if (strpos($result,".")) {
                $result = explode(".",$result);
                $result = implode('',$result);
            }
            if (strpos($result,"?")) {
                $result = explode("?",$result);
                $result = implode('',$result);
            }
            $result = explode(' ',$result);
            $result = implode('-',$result);
            return jsonSuccess('ok',$result);
        }
    }
}