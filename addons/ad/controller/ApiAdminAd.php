<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\ad\controller;
use think\Request;

use mip\AdminBase;
class ApiAdminAd extends AdminBase
{
     
    public function itemAdd()
    {
        $title = input('post.title');
        $name = input('post.name');
        $content = input('post.content');
        $info = input('post.info');
        if (!$title) {
          return jsonError('请输入名称');
        }
        if (!$name) {
          return jsonError('请输入别名');
        }
        $itemInfo = db('Ad')->where('title',$title)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        $itemInfo = db('Ad')->where('name',$name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        db('Ad')->insert(array(
            'id' => uuid(),
            'title' => $title,
            'name' => $name,
            'content' => htmlspecialchars($content),
            'add_time' => time(),
            'info' => $info,
        ));
        return jsonSuccess('成功');
    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $title = input('post.title');
        $name = input('post.name');
        $content = input('post.content');
        $info = input('post.info');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if (!$title) {
          return jsonError('请输入名称');
        }
        if (!$name) {
          return jsonError('请输入别名');
        }
        $itemInfo = db('Ad')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('修改项不存在');
        }
        $itemInfo = db('Ad')->where('id','<>',$id)->where('title',$title)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        $itemInfo = db('Ad')->where('id','<>',$id)->where('name',$name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        
        db('Ad')->where('id',$id)->update(array(
                'title' => $title,
                'name' => $name,
                'content' => htmlspecialchars($content),
                'info' => $info,
        ));
        return jsonSuccess('成功');
    }
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('Ad')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db('Ad')->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    
    public function itemSelect()
    {
        $status = input('post.status');
        $orderBy = input('post.orderBy');
        $order = input('post.order');
        $page = input('post.page');
        $limit = input('post.limit');
        $limit ? $limit : 10;
        if(!$orderBy) {
           $orderBy = 'add_time';
        }
        if(!$order){
            $order = 'desc';
        }
        
        $itemList = db('Ad')->page($page,$limit)->order($orderBy,$order)->select();
        foreach ($itemList as $k => $v) {
            $itemList[$k]['content'] = htmlspecialchars_decode($itemList[$k]['content']);
        }
        $total = db('Ad')->count();
        return jsonSuccess('',['itemList' => $itemList,'total' => $total]);
    }
 
}