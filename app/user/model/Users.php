<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\user\model;
use think\Model;
use app\user\model\Users;
use think\Cache;

class Users extends Model
{
    
    public function usersGroup() {
        return $this->hasOne('app\user\model\UsersGroup','group_id','group_id');
    }
    public function updateViews($id) {
        $tempCache = Cache::get('updateViewsUser' . md5(session_id()) . intval($id));
        if ($tempCache) {
            return false;
        }
        Cache::set('updateViewsUser' . md5(session_id()) . intval($id), time(), 60);
        $this->where('uid',$id)->update([
            'home_page_views_num' => ['exp','home_page_views_num+1'],
        ]);
        return true;
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
        $salt = create_salt(8);
        return  $this->create(array(
                'uid' => uuid(),
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