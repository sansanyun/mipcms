<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use think\Loader;
use app\api\model\Friendlink;
use mip\Htmlp;

use mip\AuthBase;
class Link extends AuthBase
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
 
}