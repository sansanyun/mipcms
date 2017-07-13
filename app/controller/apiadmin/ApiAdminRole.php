<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use think\Request;
use app\model\Roles\RolesNode;
use app\model\Roles\RolesAccess;

use mip\AdminBase;
class ApiAdminRole extends AdminBase
{
    public function index() {

    }

    public function rolesNodeAdd(Request $request){
		if (Request::instance()->isPost()) {

			$title = input('post.title');
            $name = input('post.name');
            $pid = input('post.pid');
            $status = input('post.status');
            $level = input('post.level');

            if (!$title) {
              return jsonError('请输入节点名称');
            }
            if (!$name) {
              return jsonError('请输入节点路径');
            }
            if (!$pid) {
                if ($pid != 0) {
                    return jsonError('缺少pid参数');
                }
            }

            if (RolesNode::create(array('name' => $name, 'title' => $title, 'pid' => $pid, 'status' => $status, 'level' => $level))) {
                return jsonSuccess('添加成功');
            } else {
                return  jsonError('添加失败');
            }

		}
    }

    public function rolesNodeDel(Request $request){
        if (Request::instance()->isPost()) {

            $id = input('post.id');

            if (!$id) {
              return jsonError('缺少参数');
            }

            $rolesNodeInfo=RolesNode::where('id',$id)->find();
            if ($rolesNodeInfo) {
                $rolesNodeInfo->delete();
                return jsonSuccess('删除成功');
            } else {
                return  jsonError('参数错误');
            }

        }
    }

    public function rolesNodeSelect(Request $request){
        if (Request::instance()->isPost()) {

            $rolesNodes = RolesNode::order('level', 'desc')->select();
            $rolesNodes = list_to_tree($rolesNodes);
            return jsonSuccess('OK',$rolesNodes);

        }
    }

    public function rolesNodeEdit(Request $request){
        if (Request::instance()->isPost()) {

            $id = input('post.id');
            $title = input('post.title');
            $name = input('post.name');
            $pid = input('post.pid');
            $status = input('post.status');
            $level = input('post.level');

            if (!$title) {
              return jsonError('请输入节点名称');
            }
            if (!$name) {
              return jsonError('请输入节点路径');
            }
            if (empty($pid)) {
              return jsonError('缺少pid参数');
            }
            if (empty($id)) {
              return jsonError('缺少id参数');
            }

            $rolesNodeInfo = RolesNode::where('id',$id)->find();
            if (!$rolesNodeInfo) {
                return jsonError('节点不存在');
            } else {
                if (RolesNode::where('id',$id)->update(array('name' => $name, 'title' => $title, 'pid' => $pid, 'status' => $status, 'level' => $level))) {
                    return jsonSuccess('修改成功');
                } else {
                    return  jsonError('修改失败');
                }
            }

        }
    }


    public function rolesAccessAdd(Request $request){
        if (Request::instance()->isPost()) {

            $nodes = input('nodes/a');
            $group_id = input('post.group_id');

            if (!$group_id) {
              return jsonError('缺少分组ID');
            }
            RolesAccess::where('group_id',$group_id)->delete();
            foreach ($nodes as $key => $val) {
                if (!RolesAccess::where('node_id',$val['id'])->where('group_id',$group_id)->find()) {
                    RolesAccess::create(array('node_id' => $val['id'], 'group_id' => $group_id,'level' => $val['level'], 'pid' => $val['pid']));
                }
            }
            return jsonSuccess('授权成功');

        }
    }

    public function rolesAccessSelect(Request $request) {
        if (Request::instance()->isPost()) {

            $group_id = input('post.group_id');

            if (!$group_id) {
              return jsonError('缺少分组ID');
            }

            $rolesAccess = RolesAccess::where('group_id',$group_id)->select();
            return jsonSuccess('OK',$rolesAccess);

        }
    }

}