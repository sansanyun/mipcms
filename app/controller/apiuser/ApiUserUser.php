<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiuser;
use app\model\Users\Users;
use app\model\Users\UsersGroup;
use app\model\AccessKey;
use think\Request;
use think\Loader;
use think\Session;
use think\Cache;
use mip\Htmlp;
use mip\AuthBase;
class ApiUserUser extends AuthBase
{
    public function index(){

    }
    public function userAdd(Request $request) {
        if (Request::instance()->isPost()) {
            if (!$this->mipInfo['registerStatus']) {
                return jsonError('该功能已被管理员关闭');
            }
            $username = Htmlp::htmlp(trim(input('post.username')," \t\n\r\0\x0B"));
            $password = Htmlp::htmlp(trim(input('post.password')," \t\n\r\0\x0B"));
            $email = input('post.email');
            $mobile = input('post.mobile');
            $captcha = input('post.captcha');
            $rules = [
                'username'  => 'require|max:25',
                'password'  => 'require|max:33',
                'email' => 'email',
            ];
            $msg = [
                'username.require' => '用户名不能为空',
                'username.max'     => '用户名超过最大长度',
                'password.require' => '密码不能为空',
                'password.max'     => '密码超过最大长度',
                'email'        => '邮箱格式错误',
            ];
            $result = $this->validate($request->param(), $rules, $msg);
            if (true !== $result) {
                return $result;
            }
            if ($mobile) {
                if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
                    return jsonError('手机号码输入有误');
                }
            }
            if ($this->mipInfo['registerCaptcha']) {
                if ($this->terminal == 'pc') {
                    if(!captcha_check($captcha)){
                        return jsonError('验证码错误');
                    }
                }
            }
            if (Users::getByUsername($username)) {
                return jsonError('用户名已存在');
            }
            if($email){
                if (Users::getByEmail($email)) {
                    return jsonError('邮箱已存在');
                }
            }
            if($mobile){
                if (Users::getByMobile($mobile)) {
                    return jsonError('手机已存在');
                }
            }
            if($userInfo = Loader::model('app\model\Users\Users')->regUser($username,$password,$this->terminal,$email,$mobile)){
                return jsonSuccess('注册成功',$username,'/');
            }else{
                return jsonError('注册失败');
            }
        }
    }
    public function login(Request $request) {
        if (Request::instance()->isPost()) {
            $terminal = input('post.terminal');
            $username = input('post.username');
            $password = input('post.password');
            $email = input('post.email');
            $mobile = input('post.mobile');
            $captcha = input('post.captcha');
            $rules = [
                'terminal'  => 'require',
                'username'  => 'require',
                'password'  => 'require',
                'email' => 'email',
                'mobile' => 'mobile',
            ];
            $msg = [
                'username.require' => '用户名不能为空',
                'password.require' => '密码不能为空',
                'email'        => '邮箱格式错误',
                'mobile'        => '手机号码格式错误',
            ];
            $result = $this->validate($request->param(), $rules, $msg);
            if (true !== $result) {
                return $result;
            }
            if ($this->mipInfo['loginCaptcha']) {
                if ($this->terminal == 'pc') {
                    if(!captcha_check($captcha)){
                        return jsonError('验证码错误');
                    }
                }
            }
            $userInfo = Loader::model('app\model\Users\Users')->loginUser($username,$password,$this->terminal);
            if ($userInfo) {
                if (!$this->mipInfo['loginStatus']) {
                    if ($userInfo['group_id'] != 1) {
                        return jsonError('本站已关闭登录');
                    }
                }
                if($userInfo['status']==1){
                    return jsonError('你的账号被禁止登录');
                }
                $session['uid']       = $userInfo['uid'];
                $session['username']  = $userInfo['username'];
                $AuthBase = new AuthBase();
                $userInfo['accessToken'] = $AuthBase->accessToken($userInfo['uid'],$this->terminal);
                $userInfo['password'] = null;
                $userInfo['salt'] = null;
                $this->roleList = [];
                $this->rolesNodeList = [];
                $roleAccessList = db('RolesAccess')->where('group_id',$userInfo['group_id'])->select();
                if ($roleAccessList) {
                    foreach ($roleAccessList as $k => $v) {
                        $modeIds[$k] = $v['node_id'];
                        $rolesAccessPids[$k] = $v['pid'];
                    }
                    $this->rolesNodeList = db('RolesNode')->select();
                    $roleList = db('RolesNode')->where(['id' => ['in', $modeIds]])->whereOr(['id' => ['in', $rolesAccessPids]])->select();
                    $this->roleList = $roleList;
                }
                $userInfo['roleList'] = $this->roleList;
                $userInfo['rolesNodeList'] = $this->rolesNodeList;
                session('userInfo',$session);
                return jsonSuccess('登录成功',$userInfo);
            } else {
                return jsonError('账号或密码不正确');
            }
        }
    }

    public function loginOut(){
        @session::delete('userInfo');
        @Cache::set('accessToken_' . $this->terminal . $this->accessToken, NULL);
        @Cache::set('accessTokenAndClient_' . $this->terminal .  $this->accessTokenId, NULL);
        return jsonSuccess('退出成功');
    }

}