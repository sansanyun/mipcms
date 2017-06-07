<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api\model;
use think\Model;
use app\api\model\UsersGroup;

class Users extends Model
{
    
    public function usersGroup() {
        return $this->hasOne('app\api\model\UsersGroup','group_id','group_id');
    }
    public function regUser($username,$password,$terminal,$email = null, $mobile = null){
        if(!isset($username)){
            return false;
        }
        if(!isset($password)){
            return false;
        }
        if(!isset($terminal)){
            return false;
        }
        $salt = create_salt(12);
        return  $this->create(array(
                'uuid' => uuid(),
                'username' => $username,
                'password' => create_md5($password,$salt),
                'salt' => $salt,
                'reg_time' => time(),
                'reg_ip' => request()->ip(),
                'email' => $email,
                'group_id' => 2,
                'mobile' => $mobile,
                'rank' => 1,
                'terminal' => $terminal,
            ));
    }
    
    public function loginUser($username,$password,$terminal) {
        if (!isset($username)) {
            return false;
        }
        if (!isset($password)) {
            return false;
        }
        if (!isset($terminal)) {
            return false;
        }
        if (preg_match("/^1[34578]{1}\d{9}$/",$username)) {
            $userInfo = $this->getByMobile($username);
            if (!$userInfo) {
            	$userInfo = $this->getByUsername($username);
            }
        } else {
           	$userInfo = $this->getByUsername($username);
        }
        if ($userInfo && $userInfo['password'] == create_md5($password,$userInfo['salt'])) {
	        $userInfo = $this->getByUid($userInfo['uid']);
	        $userInfo->last_login_time = time();
	        $userInfo->last_login_ip = request()->ip();
	        $userInfo->login_num = $userInfo['login_num']+1;
	        $userInfo->save();
            return $userInfo;
        } else {
        	return false;
        }
    }
    
}