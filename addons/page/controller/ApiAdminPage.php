<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\page\controller;
use think\Request;

use mip\AdminBase;
class ApiAdminPage extends AdminBase
{
    public function index()
    {
        
    }
    
    public function itemAdd()
    {
        $title = input('post.title');
        $url_name = input('post.url_name');
        $template = input('post.template');
        $content = input('post.content');
        $keywords = input('post.keywords');
        $description = input('post.description');
        if (!$title) {
          return jsonError('请输入名称');
        }
        if (!$url_name) {
          return jsonError('请输入别名');
        }
        $itemInfo = db('page')->where('title',$title)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        $itemInfo = db('page')->where('url_name',$url_name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        db('page')->insert(array(
                'id' => uuid(),
                'title' => $title,
                'url_name' => $url_name,
                'template' => $template,
                'keywords' => $keywords,
                'description' => $description,
                'content' => htmlspecialchars($content),
        ));
        return jsonSuccess('成功');
    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $title = input('post.title');
        $url_name = input('post.url_name');
        $template = input('post.template');
        $content = input('post.content');
        $keywords = input('post.keywords');
        $description = input('post.description');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if (!$title) {
          return jsonError('请输入名称');
        }
        if (!$url_name) {
          return jsonError('请输入别名');
        }
        $itemInfo = db('page')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('修改项不存在');
        }
        $itemInfo = db('page')->where('id','<>',$id)->where('title',$title)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        $itemInfo = db('page')->where('id','<>',$id)->where('url_name',$url_name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        
        db('page')->where('id',$id)->update(array(
                'title' => $title,
                'url_name' => $url_name,
                'keywords' => $keywords,
                'template' => $template,
                'description' => $description,
                'content' => htmlspecialchars($content),
        ));
        return jsonSuccess('成功');
    }
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('page')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db('page')->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    
    public function itemSelect()
    {
        $itemList = db('page')->select();
        foreach ($itemList as $k => $v) {
            $itemList[$k]['content'] = htmlspecialchars_decode($itemList[$k]['content']);
        }
        $total = db('page')->count();
        return jsonSuccess('',['itemList' => $itemList]);
    }
  
    
}