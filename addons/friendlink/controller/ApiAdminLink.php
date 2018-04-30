<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\friendlink\controller;


use mip\AdminBase;
class ApiAdminLink extends AdminBase
{
    public function index() {

    }

    public function friendlinkAdd()
    {
        $name = input('post.name');
        $url = input('post.url');
        $description = input('post.description');

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

        $createInfo = db('friendlink')->insert(array(
           'name' => $name,
           'url' => $url,
           'description' => $description,
           'type' => $type,
           'add_time' => time(),
           'status' => 1,
            ));
        
        return jsonSuccess('添加成功');
    }

    public function friendlinkDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }

        $friendlinkInfo = db('friendlink')->where('id',$id)->find();
        if ($friendlinkInfo) {
            db('friendlink')->where('id',$id)->delete();
            return jsonSuccess('删除成功');
        } else {
            return jsonError('友情链接不存在');
        }
    }
    
    public function friendlinkSave()
    {
        $linkList = input('post.linkList/a');
        foreach ($linkList as $key => $val) {
            if ($linkListInfo = db('friendlink')->where('id',$val['id'])->find()) {
                db('friendlink')->where('id',$val['id'])->update(array('sort' => $val['sort']));
            }
        }
        return jsonSuccess('保存成功');

    }

    public function friendlinkSelect()
    {
		$orderBy = input('post.orderBy');
		$order = input('post.order');

		if(!$orderBy){
           $orderBy = 'sort';
		}
		if(!$order){
            $order = 'desc';
		}
	    $friendlinkList = db('friendlink')->order($orderBy, $order)->select();
	    return jsonSuccess('',['friendlinkList' => $friendlinkList]);

    }

    public function friendlinkEdit()
    {
            $id = input('post.id');
            $name = input('post.name');
            $url = input('post.url');
            $description = input('post.description');

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

            if(!$friendlinkInfo = db('friendlink')->where('id',$id)->find()) {
                return jsonError('友情链接不存在');
            }
            if(db('friendlink')->where('id',$id)->update(['name' => $name, 'url' => $url, 'description' => $description, 'type' => $type])){
                return  jsonSuccess('修改成功');
            }

    }

}