<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use app\model\Friendlink;
use think\Request;
use think\Loader;
use mip\Htmlp;

use mip\AdminBase;
class ApiAdminLink extends AdminBase
{
    public function index(){

    }

    public function friendlinkAdd(Request $request){
        if (Request::instance()->isPost()) {

            $name = Htmlp::htmlp(input('post.name'));
            $url = Htmlp::htmlp(input('post.url'));
            $description = Htmlp::htmlp(input('post.description'));

            if (!$name) {
              return jsonError('请输入名称');
            }
            if (!$url) {
              return jsonError('请输入网址');
            }
            if (!$description) {
                $description = $name;
            }
            $type = input('post.type');
            if (!$type) {
                $type = 'all';
            }

            $createInfo = Friendlink::create(array(
               'name' => $name,
               'url' => $url,
               'description' => $description,
               'type' => $type,
               'add_time' => time(),
               'status' => 1,
                ));
            if ($createInfo) {
                return jsonSuccess('添加成功');
            } else {
                return jsonError('添加失败');
            }
        }
    }

    public function friendlinkDel(Request $request){
        if (Request::instance()->isPost()) {

            $id = input('post.id');
            if (!$id) {
              return jsonError('缺少参数');
            }

            $friendlinkInfo = Friendlink::where('id',$id)->find();
            if ($friendlinkInfo) {
                $friendlinkInfo->delete();
                return jsonSuccess('删除成功');
            } else {
                return jsonError('友情链接不存在');
            }

        }
    }
    public function friendlinkSave(Request $request){
        if (Request::instance()->isPost()) {

            $linkList = input('post.linkList/a');
            foreach ($linkList as $key => $val) {
                if ($linkListInfo = Friendlink::where('id',$val['id'])->find()) {
                    Friendlink::where('id',$val['id'])->update(array('sort' => $val['sort']));
                }
            }
            return jsonSuccess('保存成功');

        }
    }

    public function friendlinkSelect(Request $request){
		if (Request::instance()->isPost()) {

			$orderBy = input('post.orderBy');
			$order = input('post.order');

			if(!$orderBy){
	           $orderBy = 'sort';
			}
			if(!$order){
                $order = 'desc';
			}
		    $friendlinkList = Friendlink::order($orderBy, $order)->select();
		    return jsonSuccess('',['friendlinkList' => $friendlinkList]);

        }
    }

    public function friendlinkEdit(Request $request) {
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $name = Htmlp::htmlp(input('post.name'));
            $url = Htmlp::htmlp(input('post.url'));
            $description = Htmlp::htmlp(input('post.description'));

            if (!$name) {
              return jsonError('请输入名称');
            }
            if (!$url) {
              return jsonError('请输入网址');
            }
            if (!$description) {
                $description = $name;
            }
            $type = input('post.type');
            if (!$type) {
                $type = 'all';
            }

            if(!$friendlinkInfo = Friendlink::getById($id)){
                return jsonError('友情链接不存在');
            }
            if($friendlinkInfo->where('id',$id)->update(['name' => $name, 'url' => $url, 'description' => $description, 'type' => $type])){
                return  jsonSuccess('修改成功');
            }

        }
    }

}