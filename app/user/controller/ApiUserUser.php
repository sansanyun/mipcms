<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\user\controller;
use app\user\model\Users;
use app\user\model\UsersGroup;
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
            if($userInfo = Loader::model('app\user\model\Users')->regUser($username,$password,$this->terminal,$email,$mobile)){
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
            $userInfo = Loader::model('app\user\model\Users')->loginUser($username,$password,$this->terminal);
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
                if ($this->terminal == 'clouds') {
                    if (!db('accessKey')->where('id',999)->find()) {
                        db('accessKey')->insert(array('id' => 999 ,'name' => 'clouds','key' => uuid(),'type' => 'clouds'));
                    } else {
                        db('accessKey')->where('id',999)->update(array('name' => 'clouds','key' => uuid(),'type' => 'clouds'));
                    }
                    $accessKeyInfo = db('accessKey')->where('id',999)->find();
                    return jsonSuccess('绑定成功',array('key' => $accessKeyInfo['key'] , 'mipInfo' => $this->mipInfo));
                } else {
                    return jsonSuccess('登录成功',$userInfo);
                }
            } else {
                return jsonError('账号或密码不正确');
            }
        }
    }
    public function userFind(Request $request){
        if (Request::instance()->isPost()) {
            $uid = $this->userId;
            if (!$uid) {
                return jsonError('参数错误');
            }
            if(!Users::where('uid',$uid)->find()){
                return jsonError('用户不存在');
            }
            $usersInfo = Users::where('uid',$uid)->field(['password'], true)->find();
            $usersInfo['avatar'] = getAvatarUrl($uid);
            return jsonSuccess('',['usersInfo' => $usersInfo]);
        }
    }
    public function userEdit(Request $request){
        if (Request::instance()->isPost()) {

            $nickname = Htmlp::htmlp(input('post.nickname'));
            $email = Htmlp::htmlp(input('post.email'));
            $mobile = Htmlp::htmlp(input('post.mobile'));
            $qq = Htmlp::htmlp(input('post.qq'));
            $sex = Htmlp::htmlp(input('post.sex'));
            $signature = Htmlp::htmlp(input('post.signature'));

            if ($uid = $this->userId) {
                $data['uid'] = $this->userId;
            }
            if (!$uid) {
                return jsonError('缺少用户UID');
            }
            if(!$userInfo = Users::where('uid',$uid)->find()){
                return jsonError('用户不存在');
            }

            if (!$sex) {
                $sex = 1;
            }
            $data['qq'] = $qq;
            $data['sex'] = $sex;
            $data['signature'] = $signature;
            $data['nickname'] = $nickname;

            if ($mobile) {
                if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
                    return jsonError('手机号码输入有误');
                }
                if (Users::getByMobile($mobile)) {
                    return jsonError('手机已存在');
                }
            }
            $data['mobile'] = $mobile;

            $rules = [
                'qq' => 'number',
                'email' => 'email',
            ];
            $msg = [
                'qq' => 'QQ号格式错误',
                'email' => '邮箱格式错误',
            ];
            $result = $this->validate($request->param(), $rules, $msg);
            if (true !== $result) {
                return $result;
            }
            if($email){
                if ($email != $userInfo['email'] AND Users::getByEmail($email)) {
                    return jsonError('邮箱已存在');
                }
            }
            $data['email'] = $email;

            if ($data) {
                $usersInfo = Users::update($data,['uid'=>$uid]);
            }
            return jsonSuccess('修改成功');

        }
    }

    public function userPasswordEdit(Request $request) {
        if (Request::instance()->isPost()) {

            $uid = $this->userId;
            $oldPassword = Htmlp::htmlp(input('post.oldPassword'));
            $newPassword = Htmlp::htmlp(input('post.newPassword'));
            $rpassword = Htmlp::htmlp(input('post.rpassword'));

            if(!$oldPassword){
                return jsonError('请输入原来密码');
            }
            if(!$newPassword){
                 return jsonError('请输入新密码');
            }
            if (strlen($oldPassword) != 32){
                 return jsonError('密码长度不符合规则');
            }
            if (strlen($newPassword) != 32){
                 return jsonError('密码长度不符合规则');
            }
            if (strlen($rpassword) != 32){
                 return jsonError('密码长度不符合规则');
            }
            if ($oldPassword == $newPassword) {
                return jsonError('新密码不能与旧密码重复');
            }
            if($rpassword != $newPassword){
                return jsonError('两次输入的密码不一样');
            }
            $userPasswordInfo = Users::where('uid',$this->userId)->find();

            $oldpassword = create_md5($oldPassword,$userPasswordInfo['salt']);

            if($oldpassword != $userPasswordInfo['password']) {
                 return jsonError('旧密码错误，请重新输入');
            }
            $data['salt'] = create_salt(8);
            $data['password'] = create_md5($newPassword,$data['salt']);
            $usersInfo = Users::update($data,['uid'=>$uid]);
            @session::delete('userInfo');
            @Cache::set('accessToken_' . $this->terminal . $this->accessToken, NULL);
            @Cache::set('accessTokenAndClient_' . $this->terminal .  $this->accessTokenId, NULL);
            return  jsonSuccess('修改成功','','/login/');
        }
    }

    public function userAvatarUpload(Request $request) {
        if (Request::instance()->isPost()) {
            $file = request()->file('file');

            if(empty($file)){
                return jsonError('请选择图片');
            }
            $uid = $this->userId;
            if(!$uid){
                return jsonError('缺少参数');
            }
            if (MIP_HOST) {
                $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . DS . $this->mipInfo['uploadUrl'] . DS .'avatar'. DS , $uid . '.jpg');
            } else {
                $info = $file->validate(['ext'=>'jpg,png,gif'])->move(PUBLIC_PATH . DS . $this->mipInfo['uploadUrl'] . DS .'avatar'. DS , $uid . '.jpg');
            }
            if ($info) {
                return jsonSuccess('上传成功');
            } else {
                return jsonError($file->getError());
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