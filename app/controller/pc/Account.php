<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\pc;
use mip\Mip;

class Account extends Mip {
    public function index() {
        return $this->mipView('account/index','pc');
    }
    public function login() {
        if ($this->userId) {
            $this->redirect($this->domain,302);
        }
        $return_url = @htmlspecialchars($_SERVER['HTTP_REFERER']);
        $this->assign('return_url', $return_url);
        return $this->mipView('account/login','pc');
    }
    public function register() {
        
        if (!$this->mipInfo['registerStatus']) {
            $this->error('本站已关闭注册功能');
        }
        if ($this->userId) {
            $this->redirect($this->domain,302);
        }
        return $this->mipView('account/register','pc');
    }
}