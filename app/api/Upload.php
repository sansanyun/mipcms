<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;

use mip\AuthBase;
class Upload extends AuthBase
{
    public function index(){
		 
    }
    public function imgUpload(Request $request){
        if (Request::instance()->isPost()) {
            
            $type = input('post.type');
            $ymd = input('post.ymd');
            $file = $request->file('fileDataFileName');
            $ymd = 1;
            if (empty($type)) {
                $type = 'temp';
            }
            if (empty($file)) {
                return jsonError('图片不存在');
            }
            if ($ymd) {
                $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
            } else {
                $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
            }
            if ($info) {
                if ($ymd) {
                    return jsonSuccess('','/'.$this->mipInfo['uploadUrl'] .'/'.$type.'/' . date('Y',time()) . '/' . date('m',time()) . '/' . date('d',time()) . '/' . $info->getFilename());
                } else {
                    return jsonSuccess('','/'.$this->mipInfo['uploadUrl'] .'/'.$type.'/'.$info->getFilename());
                }
            } else {
                return jsonError($file->getError());
            }
            
        }
    }

}