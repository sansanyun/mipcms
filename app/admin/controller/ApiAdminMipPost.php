<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\admin\controller;
use think\Request;
use mip\ChinesePinyin;
use mip\AdminBase;
class ApiAdminMipPost extends AdminBase
{
    public function index() {

    }

    public function mipPost(Request $request) {
		if (Request::instance()->isPost()) {

            $postAddress = input('post.postAddress');
            if (!$postAddress) {
                return jsonError('请先去设置百度MIP的接口');
            }
            $urls = input('post.urls');
            if (!$urls) {
                return jsonError('没有检测到你推送的MIP页面地址');
            }
            $urls = explode(',',$urls);
            if (is_array($urls)) {
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
    }
    public function urlPost(Request $request) {
        if (Request::instance()->isPost()) {

            $postAddress = input('post.postAddress');
            if (!$postAddress) {
                return jsonError('请先去设置推送的接口');
            }
            $urls = input('post.urls');
            if (!$urls) {
                return jsonError('没有检测到你推送的页面地址');
            }
            $urls = explode(',',$urls);
            if (is_array($urls)) {
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
    }
    public function getPinyin() {
        $words = input('post.words');
        $type = input('post.type');
        if (!$words) {
            return jsonError('请输入内容');
        }
        $Pinyin = new ChinesePinyin();
        $result = $Pinyin->TransformWithoutTone($words,$type);
        return jsonSuccess('',$result);
    }
}