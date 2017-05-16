<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use mip\Mip;

class Account extends Mip {
    public function index() {
        return $this->mipView('pc/account/index');
    }
    public function login() {
        if ($this->userId) {
            $this->redirect('/');
        }
        $return_url = @htmlspecialchars($_SERVER['HTTP_REFERER']);
        $this->assign('return_url', $return_url);
        return $this->mipView('pc/account/login');
    }
    public function register() {
        
        if (!$this->mipInfo['registerStatus']) {
            $this->error('本站已关闭注册功能');
        }
        if ($this->userId) {
            $this->redirect('/');
        }
        return $this->mipView('pc/account/register');
    }
}