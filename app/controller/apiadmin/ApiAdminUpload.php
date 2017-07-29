<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use think\Request;

use mip\AdminBase;
class ApiAdminUpload extends AdminBase
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
            if (MIP_HOST) {
                if ($ymd) {
                    $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                } else {
                    $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
                }
            } else {
                if ($ymd) {
                    $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                } else {
                    $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
                }
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

    public function wangImgUpload(Request $request)
    {
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
            if (MIP_HOST) {
                if ($ymd) {
                    $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                } else {
                    $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
                }
            } else {
                if ($ymd) {
                    $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                } else {
                    $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
                }
            }
            if ($info) {
                $return['errno'] = 0;
                if ($ymd) {
                    $return['data'] = array('/'.$this->mipInfo['uploadUrl'] .'/'.$type.'/' . date('Y',time()) . '/' . date('m',time()) . '/' . date('d',time()) . '/' . $info->getFilename());
                    return json($return);
                } else {
                    $return['data'] = array('/'.$this->mipInfo['uploadUrl'] .'/'.$type.'/'.$info->getFilename());
                    return json($return);
                }
            } else {
                return jsonError($file->getError());
            }

        }
    }


 public function wysiwygImgUpload(Request $request)
    {
        if (Request::instance()->isPost()) {
          $type = input('post.type');
            $ymd = input('post.ymd');
            $file = $request->file('file');
            $ymd = 1;
            if (empty($type)) {
                $type = 'temp';
            }
            if (empty($file)) {
                return jsonError('图片不存在');
            }
            if (MIP_HOST) {
                if ($ymd) {
                    $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                } else {
                    $info = $file->rule('uniqid')->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
                }
            } else {
                if ($ymd) {
                    $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                } else {
                    $info = $file->rule('uniqid')->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS . $type);
                }
            }
            if ($info) {
                    $response = new \StdClass;
                 $response->link = $this->mipInfo['uploadUrl'] .'/'.$type.'/' . date('Y',time()) . '/' . date('m',time()) . '/' . date('d',time()) . '/' . $info->getFilename();
                // Send response.
                echo stripslashes(json_encode($response));
            } else {
                return jsonError($file->getError());
            }


        }
    }

}