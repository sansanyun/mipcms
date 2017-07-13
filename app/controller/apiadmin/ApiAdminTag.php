<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use think\Request;
use think\Loader;
use app\model\Tags\Tags;
use app\model\Tags\ItemTags;

use mip\AdminBase;
class ApiAdminTag extends AdminBase
{
    public function index(){

    }
    public function tagAdd(Request $request){
		if (Request::instance()->isPost()) {
	      	$item_type = input('post.item_type');
	      	$name = input('post.name');
	      	if(!$item_type){
	      	  return jsonError('类型错误');
	      	}
	      	if(!$name){
	      	  return jsonError('请输入标签名');
	      	}
	      	if($tagInfo=Tags::where('name',$name)->find()){
	      		return jsonError('标签已存在');
	      	}else{
	      		if(Tags::create(array('name'=>$name,'item_type' => $item_type))){
	      			return jsonSuccess('添加成功');
		        }else{
		        	return  jsonError('添加失败');
		        }
	      	}

        }
    }
    public function TagsAdd(Request $request){
		if (Request::instance()->isPost()) {
	      	$item_type = input('post.item_type');
            $tags = input('post.tags');
	      	if(!$tags){
	      	  return jsonError('缺少参数');
	      	}
            $tags = explode(',',$tags);
	      	if(!$item_type){
	      	  return jsonError('类型错误');
	      	}
	      	if(is_array($tags)){
                foreach ($tags as $name){
                    if(!$tagInfo=Tags::where('name',$name)->find()){
                        $tagInfo =  Tags::create(array(
                            'name'=>$name,
                            'item_type' => $item_type,
                        ));
                    }
                }
                return jsonSuccess('添加成功');
	        }else{
	        	return  jsonError('标签格式错误');
	        }

        }
    }
    public function TagsDel(Request $request){
		if (Request::instance()->isPost()) {
            $tagIds = input('post.tagIds');
            if(!$tagIds){
	      	  return jsonError('缺少参数');
	      	}
            $tagIds = explode(',',$tagIds);
	      	if(is_array($tagIds)){
                foreach ($tagIds as $id){
                    if($tagInfo=Tags::where('id',$id)->find()){
                       $tagInfo->delete();
                    }
                }
                return jsonSuccess('删除成功');
	        }else{
	        	return  jsonError('参数错误');
	        }

        }
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

    public function tagsEdit(Request $request){
		if (Request::instance()->isPost()) {
			$TagId = input('post.id');
			$name = input('post.name');
			$item_type = input('post.item_type');
			if(!$TagId){
				return jsonError('缺少ID');
			}
			if(!$name){
				return jsonError('请输入名称');
			}
			if(!$TagsInfo = Tags::getById($TagId)){
	          	return jsonError('标签不存在');
	        }
	        if($TagsInfo->where('id',$TagId)->update(['name' => $name,'item_type' => $item_type])){
	        	return  jsonSuccess('修改成功');
	        }
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