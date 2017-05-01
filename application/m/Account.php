<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\m;
use mip\Mip;

class Account extends Mip {
    public function login() {
        if ($this->userId) {
            $this->redirect('/');
        }
        $return_url = @htmlspecialchars($_SERVER['HTTP_REFERER']);
        $this->assign('return_url', $return_url);
        return $this->mipView('m/account/login');
    }
    public function register() {
        return $this->mipView('m/account/register');
    }
}