<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;
use think\Request;

use mip\Init;
class AdminBase extends Init {
 
    public function _initialize() {
        parent::_initialize();
        
        if (!Request::instance()->isPost()) {
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['code'=>1000, 'msg'=>'违法操作']));
        }
        $header = Request::instance()->header();
         
        $this->passStatus = false;
        $passAuthInfo = true;
        $passAuthList = array(
            '0' => 'imgUpload',
        );
        foreach ($passAuthList as $key => $val) {
            if (strtoupper($passAuthList[$key]) == strtoupper($this->request->action())) {
                $passAuthInfo = false;
            }
        }
        if ($passAuthInfo) {
            if (!$this->isAdmin) {
                if (empty($header['secret-key'])) {
                    header('Content-Type:application/json; charset=utf-8');
                    exit(json_encode(['code'=>1008, 'msg'=>'无权限操作']));
                }
                if ($header['secret-key'] == db('AccessKey')->where('id',999)->find()['key']) {
                    $this->passStatus = true;
                } else {
                    header('Content-Type:application/json; charset=utf-8');
                    exit(json_encode(['code'=>1010, 'msg'=>'无权限操作']));
                }
            } else {
                $this->passStatus = true;
            }
            
            if (!$this->passStatus) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['code'=>1009, 'msg'=>'无权限操作']));
            }

        }
         
    }
}
