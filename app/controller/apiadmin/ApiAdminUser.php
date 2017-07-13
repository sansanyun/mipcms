<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use app\model\Users\Users;
use app\model\Users\UsersGroup;
use app\model\AccessKey;
use think\Request;
use think\Loader;
use think\Session;
use think\Cache;
use mip\Htmlp;
use mip\AdminBase;
class ApiAdminUser extends AdminBase
{
    public function index(){

    }
    public function userCreate(Request $request) {
        if (Request::instance()->isPost()) {
            $username = Htmlp::htmlp(trim(input('post.username')," \t\n\r\0\x0B"));
            $password = Htmlp::htmlp(trim(input('post.password')," \t\n\r\0\x0B"));
            $email = input('post.email');
            $mobile = input('post.mobile');
            $rules = [
                'terminal'  => 'require',
                'username'  => 'require|max:33',
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
            if($userInfo = Loader::model('Users')->regUser($username,$password,$this->terminal,$email,$mobile)){
                return jsonSuccess('创建成功',$username,'/');
            }else{
                return jsonError('创建失败');
            }
        }
    }

    public function userDel(Request $request){
		if (Request::instance()->isPost()) {
			$uid = input('post.uid');
			if (!$uid) {
				return jsonError('请输入用户ID');
			}
			if (!$uidInfo = Users::getByUid($uid)) {
				return jsonError('用户不存在');
			}
			if ($uidInfo['uid'] == 1) {
                return jsonError('禁止删除超级管理员');
			}
		    if($uid = Users::where('uid',$uid)->delete()){
		   		return jsonSuccess('删除成功',$uid);
		    }
		}
    }
    public function usersDel(Request $request){
		if (Request::instance()->isPost()) {
			$uids = input('post.uids');
            if(!$uids){
	      	  return jsonError('缺少参数');
	      	}
            $uids = explode(',',$uids);
	      	if(is_array($uids)){
                foreach ($uids as $uid){
                   Users::where('uid',$uid)->delete();
                }
                return jsonSuccess('删除成功');
	        }else{
	        	return  jsonError('参数错误');
	        }
		}
    }
    public function usersSelect(Request $request){
		if (Request::instance()->isPost()) {
			$page = input('post.page');
			$limit = input('post.limit');
			$orderBy = input('post.orderBy');
			$order = input('post.order');
			$status = input('post.status');
			if(!$page){
			  $page = 1;
			}
			if(!$limit){
			  $limit = 10;
			}
			if(!$orderBy){
			 $orderBy = 'uid';
			}
			if(!$order){
				$order = 'desc';
			}
			if($status == 'all'){
				$usersList = Users::field(['password'], true)->limit($limit)->page($page)->order($orderBy, $order)->select();
			}else{
				$usersList = Users::where('status','<>',1)->field(['password'], true)->limit($limit)->page($page)->order($orderBy, $order)->select();
			}
			if ($usersList) {
                foreach ($usersList as $key => $val) {
                    $usersList[$key]->usersGroup;
                }
            }
		    return jsonSuccess('',['usersList' => $usersList,'total' => Users::count(),'page' => $page]);
		}
    }
    public function userFind(Request $request){
		if (Request::instance()->isPost()) {
			$uid = input('post.uid');
		    if (!$uid) {
                    return jsonError('参数错误');
            }
			if(!Users::getByUid($uid)){
				return jsonError('用户不存在');
			}
		    $usersInfo = Users::where('uid',$uid)->field(['password'], true)->find();
		    return jsonSuccess('',['usersInfo' => $usersInfo]);
		}
    }
    public function userEdit(Request $request){
		if (Request::instance()->isPost()) {

			$uid = input('post.uid');
            $username = Htmlp::htmlp(input('post.username'));
            $nickname = Htmlp::htmlp(input('post.nickname'));
            $email = Htmlp::htmlp(input('post.email'));
            $mobile = Htmlp::htmlp(input('post.mobile'));
            $qq = Htmlp::htmlp(input('post.qq'));
            $sex = Htmlp::htmlp(input('post.sex'));
            $signature = Htmlp::htmlp(input('post.signature'));
            $status = Htmlp::htmlp(input('post.status'));
			$password = Htmlp::htmlp(input('post.password'));
            $group_id = input('post.group_id');
			$data = '';

            if (!$uid) {
                return jsonError('缺少用户UID');
            }
            if(!$userInfo = Users::getByUid($uid)){
                return jsonError('用户不存在');
            }

            if ($username) {
                if ($username != $userInfo['username'] AND Users::getByUsername($username)) {
                    return jsonError('用户名已存在');
                }
            }
            if (!empty($username)) {
                $data['username'] = $username;
            }

            if (!$sex) {
                $sex = 1;
            }

            $data['qq'] = $qq;
            $data['sex'] = $sex;
            $data['signature'] = $signature;
            $data['nickname'] = $nickname;
            $data['status'] = $status;

            $rules = [
                'email' => 'email',
            ];
            $msg = [
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

            if ($mobile) {
                if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
                    return jsonError('手机号码输入有误');
                }
                if (Users::getByMobile($mobile)) {
                    return jsonError('手机已存在');
                }
            }
            $data['mobile'] = $mobile;

            if ($userInfo['uid'] == 1 && $group_id != 1) {
                return jsonError('超级管理员组禁止变更');
            }
            $data['group_id'] = $group_id;
			if(!empty($password)){
				$data['password'] = create_md5($password,$userInfo['salt']);
			}
			if ($data) {
                $usersInfo = Users::update($data,['uid'=>$uid]);
                if ($usersInfo) {
                    return jsonSuccess('修改成功');
                }
			}
		    return jsonError('修改失败');
		}
    }

}