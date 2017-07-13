<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiuser;
use app\model\Tags\Tags;
use app\model\Tags\ItemTags;
use think\Request;
use think\Loader;

use mip\AuthBase;
class ApiUserTag extends AuthBase
{
    public function index(){

    }
    public function tagsSelect(Request $request){
		if (Request::instance()->isPost()) {
	      	$page = input('post.page');
			$limit = input('post.limit');
			$orderBy = input('post.orderBy');
			$order = input('post.order');
			if(!$page){
			  $page = 1;
			}
			if(!$limit){
			  $limit = 10;
			}
			if(!$orderBy){
			 $orderBy = 'id';
			}
			if(!$order){
				$order = 'desc';
			}
		    $tagsList = Tags::limit($limit)->page($page)->order($orderBy, $order)->select();
		    return jsonSuccess('',['tagsList' => $tagsList,'total' => Tags::count(),'page' => $page]);
        }
    }

    public function itemTagsSelectByItem(Request $request){
        if (Request::instance()->isPost()) {
            $itemType = input('post.itemType');
            $itemId = input('post.itemId');

            if(!$itemType){
                return jsonError('缺少类型');
            }
            if(!$itemId){
                return jsonError('缺少类型Id');
            }

            $tagsList = ItemTags::where('item_type',$itemType)->where('item_id',$itemId)->select();

            if ($tagsList) {
                foreach ($tagsList as $k => $v){
                    $tagsList[$k]->tags;
                }
            }
            return jsonSuccess('',['tagsList' => $tagsList]);
        }
    }

}