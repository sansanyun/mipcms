<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use think\Loader;
use think\Session;
use think\Cache;
use app\api\model\Users;
use app\api\model\UsersGroup;
use app\api\model\AccessKey;
use mip\Htmlp;
use mip\AuthBase;
class ApiFrontUser extends AuthBase
{
    public function index(){
        
    }
    
    public function frontUserFind(Request $request){
        if (Request::instance()->isPost()) {
            $uid = $this->userId;
            if (!$uid) {
                return jsonError('参数错误');
            }
            if(!Users::getByUid($uid)){
                return jsonError('用户不存在');
            }
            $usersInfo = Users::where('uid',$uid)->field(['password'], true)->find();
            $usersInfo['avatar'] = getAvatarUrl($uid);
            return jsonSuccess('',['usersInfo' => $usersInfo]); 
        }
    }
    public function frontUserEdit(Request $request){
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
            if(!$userInfo = Users::getByUid($uid)){
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
    
    public function frontUserPasswordEdit(Request $request) {
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
            $data['salt'] = create_salt(12);
            $data['password'] = create_md5($newPassword,$data['salt']);
            $usersInfo = Users::update($data,['uid'=>$uid]);
            @session::delete('userInfo');
            @Cache::set('accessToken_' . $this->terminal . $this->accessToken, NULL);
            @Cache::set('accessTokenAndClient_' . $this->terminal .  $this->accessTokenId, NULL);
            return  jsonSuccess('修改成功','','/login/');
        }
    }
    
    public function frontUserAvatarUpload(Request $request) {
        if (Request::instance()->isPost()) {
            $file = request()->file('file');

            if(empty($file)){
                return jsonError('请选择图片');
            }
            $uid = $this->userId;
            if(!$uid){
                return jsonError('缺少参数');
            }
            $uid = sprintf("%09d", $uid);
            $dir1 = substr($uid, 0, 3);
            $dir2 = substr($uid, 3, 2);
            $dir3 = substr($uid, 5, 2);
            
            if (!$this->mipInfo['uploadPath']) {
                $tempUploadPath = ROOT_PATH . DS . $this->mipInfo['uploadUrl'];
            } else {
                $tempUploadPath = $this->mipInfo['uploadPath'];
            }

            $info = $file->validate(['ext'=>'jpg,png,gif'])->move($tempUploadPath . DS .'avatar'. DS . $dir1 . DS . $dir2 . DS . $dir3 .DS ,substr($uid, - 2) . '_avatar_max.jpg');
            if ($info) {
                return jsonSuccess('上传成功');
            } else {
                return jsonError($file->getError());
            }
        }
    }
    
}