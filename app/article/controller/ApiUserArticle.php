<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\controller;
use think\Request;
use think\Session;
use think\Cache;
use mip\Htmlp;
use think\Controller;
class ApiUserArticle extends Controller
{
    public function defaultImgUpload(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range,access-token, secret-key,access-key,dataId,uid,sid,terminal,X-File-Name,Content-Disposition, Content-Description');
        if (Request::instance()->isPost()) {
            $accessKeyInfo = db('accessKey')->where('id',999)->find();
            if ($accessKeyInfo) {
                if ($accessKeyInfo['key'] == input('post.key')) {
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
                            $info = $file->rule('uniqid')->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . DS . 'uploads' . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                        } else {
                            $info = $file->rule('uniqid')->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . DS . 'uploads' . DS . $type);
                        }
                    } else {
                        if ($ymd) {
                            $info = $file->rule('uniqid')->validate(['ext'=>'jpg,png,gif'])->move(PUBLIC_PATH . DS . 'uploads' . DS . $type . DS . date('Y',time()) . DS . date('m',time()) . DS . date('d',time()));
                        } else {
                            $info = $file->rule('uniqid')->validate(['ext'=>'jpg,png,gif'])->move(PUBLIC_PATH . DS . 'uploads' . DS . $type);
                        }
                    }
                    if ($info) {
                        if ($ymd) {
                            return jsonSuccess('','/uploads/'.$type.'/' . date('Y',time()) . '/' . date('m',time()) . '/' . date('d',time()) . '/' . $info->getFilename());
                        } else {
                            return jsonSuccess('','/uploads/'.$type.'/'.$info->getFilename());
                        }
                    } else {
                        return jsonError($file->getError());
                    }
                }

            }
        }
    }
 
}