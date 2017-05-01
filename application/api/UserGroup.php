<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use think\Loader;
use app\api\model\Users;
use app\api\model\UsersGroup;
use app\api\model\AccessKey;
use mip\Htmlp;
use mip\AuthBase;
class UserGroup extends AuthBase
{
    public function index(){
        
    } 
   
    public function userGroupAdd(Request $request){
		if (Request::instance()->isPost()) {
		    
			$name = input('post.name');
			
			if(!$name){
				return jsonError('请输入用户组名称');
			}
			if(UsersGroup::getByName($name)){
                return jsonError('用户组已存在');
            }
            
            UsersGroup::create(array(
                'name' => $name
            ));
            return  jsonSuccess('添加成功');
            
		}
    }
    public function userGroupDel(Request $request){
		if (Request::instance()->isPost()) {
		    
			$groupId = input('post.group_id');
			
			if (!$groupId) {
				return jsonError('缺少用户组ID');
			}
			if ($groupId == 1 || $groupId == 2) {
				return jsonError('系统自带用户组禁止删除');
			}
			
			$usersGroupInfo = UsersGroup::getByGroupId($groupId);
			if (!$usersGroupInfo) {
                return jsonError('用户组不存在');
            }
            if ($usersGroupInfo->delete()) {
            	return  jsonSuccess('删除成功');
            }
            
		}
    }
    public function userGroupSelect(Request $request){
		if (Request::instance()->isPost()) {
		    
			return jsonSuccess(UsersGroup::all());
			
		}
    }
    public function userGroupEdit(Request $request){
		if (Request::instance()->isPost()) {
		    
			$groupId = input('post.group_id');
			$name = input('post.name');
			
			if (!$groupId) {
				return jsonError('缺少用户组ID');
			}
			if (!$name) {
				return jsonError('请输入用户组名称');
			}
			
			$usersGroupInfo = UsersGroup::getByGroupId($groupId);
			if (!$usersGroupInfo) {
	          	return jsonError('用户组不存在');
	        }
	        if ($usersGroupInfo->where('group_id',$groupId)->update(['name' => $name])) {
	        	return  jsonSuccess('修改成功');
	        }
	        
  		}
   }
}