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
            $file = $request->file('fileDataFileName');
            if (empty($type)) {
                $type = 'temp';
            }
            if (empty($file)) {
                return jsonError('图片不存在');
            }
            
            $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
            if ($info) {
                return jsonSuccess('','/'.$this->mipInfo['uploadUrl'] .'/'.$type.'/'.$info->getFilename());
            } else {
                return jsonError($file->getError());
            }
            
        }
    }

}