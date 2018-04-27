<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\setting\controller;
use think\Request;

use mip\AdminBase;
class ApiAdminDomainSites extends AdminBase
{
    public function index(){

    }
    
    public function itemAdd()
    {
        $domain = input('post.domain');
        $httpType = input('post.httpType');
        $type = input('post.type');
        $template = input('post.template');
        $data_id = input('post.data_id');
        $data_id = $data_id ? $data_id : 0;
        if (!$domain) {
          return jsonError('请输入域名');
        }
        if (!$type) {
          return jsonError('请选择类型');
        }
        $itemInfo = db('domainSites')->where('domain',$domain)->find();
        if ($itemInfo) {
          return jsonError('域名已存在，请重新输入');
        }
        $tempId = uuid();
        if ($data_id) {
            db('domainSites')->insert(array(
                    'id' => $tempId,
                    'domain' => $domain,
                    'http_type' => $httpType,
                    'data_id' => $data_id,
                    'type' => $type,
                    'template' => $template,
            ));
        } else {
            db('domainSites')->insert(array(
                'id' => $tempId,
                'domain' => $domain,
                'http_type' => $httpType,
                'type' => $type,
                'template' => $template,
            ));
        }
        
        db('domainSettings')->insert(array(
                'id' => $tempId
        ));
        
        return jsonSuccess('成功');
    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $domain = input('post.domain');
        $httpType = input('post.httpType');
        $type = input('post.type');
        $template = input('post.template');
        $data_id = input('post.data_id');
        $data_id = $data_id ? $data_id : 0;
        if (!$id) {
          return jsonError('缺少参数');
        }
         if (!$domain) {
          return jsonError('请输入域名');
        }
        if (!$type) {
          return jsonError('请选择类型');
        }
        $itemInfo = db('domainSites')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('修改项不存在');
        }
        $itemInfo = db('domainSites')->where('id','<>',$id)->where('domain',$domain)->find();
        if ($itemInfo) {
          return jsonError('域名已存在，请重新输入');
        }
        if ($data_id) {
            db('domainSites')->where('id',$id)->update(array(
                    'domain' => $domain,
                    'http_type' => $httpType,
                    'type' => $type,
                    'data_id' => $data_id,
                    'template' => $template,
            ));
        } else {
             db('domainSites')->where('id',$id)->update(array(
                    'domain' => $domain,
                    'http_type' => $httpType,
                    'type' => $type,
                    'template' => $template,
            ));
        }
        return jsonSuccess('成功');
    }
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('domainSites')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db('domainSites')->where('id',$id)->delete();
        db('domainSettings')->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    public function domainSitesFind()
    {
        $id = input('post.id');
        $itemInfo = db('domainSites')->where('id',$id)->find();
        return jsonSuccess('成功',$itemInfo);
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
        
        $itemList = db('domainSites')->select();
        $total = db('domainSites')->count();
        return jsonSuccess('',['itemList' => $itemList,'total' => $total]);
    }
 
}