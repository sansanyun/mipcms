<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\xiongzhang\controller;
use think\Request;
use mip\AdminBase;
class ApiAdminXiongZhangAutoReply extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'XiongzhangAutoReply';
    }
    public function itemAdd()
    {
        $userContent = input('post.userContent');
        $replyContent = input('post.replyContent');
        $add_time = input('post.add_time') ? input('post.add_time') : time();
        if (!$userContent || !$replyContent) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('user_content',$userContent)->find();
        if ($itemInfo) {
          return jsonError('已存在，请重新输入');
        }
        db($this->item)->insert(array(
            'user_content' => $userContent,
            'reply_content' => $replyContent,
            'add_time' => $add_time,
        ));
        return jsonSuccess('成功');
    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $userContent = input('post.userContent');
        $replyContent = input('post.replyContent');
        if (!$userContent || !$replyContent) {
          return jsonError('缺少参数');
        }
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('修改项不存在');
        }
        db($this->item)->where('id',$id)->update(array(
            'user_content' => $userContent,
            'reply_content' => $replyContent,
        ));
        return jsonSuccess('成功');
    }
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db($this->item)->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    
    public function itemList()
    {
        $itemList = db($this->item)->select();
        $total = db($this->item)->count();
        return jsonSuccess('',['itemList' => $itemList,'count' => $total]);
    }
    
}