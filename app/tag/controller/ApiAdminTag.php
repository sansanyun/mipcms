<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\tag\controller;

use mip\AdminBase;
class ApiAdminTag extends AdminBase
{
     protected $beforeActionList = ['start'];
    public function start() {
        $this->itemModelNameSpace = 'tagLib\TagsModel';
        $this->itemName = $this->mipInfo['tagModelName'];
        $this->item = $this->tags;
        $this->itemCategory = $this->tagsCategory;
        $this->itemType = 'tag';
    }
    public function index(){

    }
    public function tagAdd()
    {
      	$item_type = input('post.item_type');
        $cid = input('post.cid') ? input('post.cid') : 0;
        $name = input('post.name');
        $url_name = input('post.url_name');
        $description = input('post.description');
      	if(!$item_type){
      	  return jsonError('类型错误');
      	}
      	if(!$name){
      	  return jsonError('请输入标签名');
      	}
        $tagInfo = db($this->tags)->where('name',$name)->find();
      	if($tagInfo){
      		return jsonError('标签已存在');
      	}
        $itemInfo = db($this->tags)->where('url_name',$url_name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        db($this->tags)->insert(array(
            'id' => uuid(),
           'name' => $name,
           'cid' => $cid,
           'item_type' => $item_type,
           'url_name' => $url_name,
            'description' => $description,
            'add_time' => time(),
        ));
        return jsonSuccess('添加成功');
        

    }
    public function TagsAdd()
    {
      	$item_type = input('post.item_type');
        $tags = input('post.tags');
      	if (!$tags) {
      	  return jsonError('缺少参数');
      	}
        $tags = explode(',',$tags);
      	if (!$item_type) {
      	  return jsonError('类型错误');
      	}
      	if (is_array($tags)) {
            foreach ($tags as $name) {
                if ($name) {
                    if (!$tagInfo=db($this->tags)->where('name',$name)->find()) {
                        $tagInfo =  db($this->tags)->insert(array(
                            'id' => uuid(),
                            'name'=>$name,
                            'item_type' => $item_type,
                            'add_time' => time(),
                        ));
                    }
                }
            }
            return jsonSuccess('添加成功');
        }else{
        	return  jsonError('标签格式错误');
        }

    }
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->tags)->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db($this->tags)->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    
    public function TagsDel(){
        $tagIds = input('post.tagIds');
        if (!$tagIds) {
      	  return jsonError('缺少参数');
      	}
        $tagIds = explode(',',$tagIds);
      	if (is_array($tagIds)) {
            foreach ($tagIds as $id){
                $tagInfo = db($this->tags)->where('id',$id)->find();
                if ($tagInfo) {
                   db($this->tags)->where('id',$id)->delete();
                }
            }
            return jsonSuccess('删除成功');
        } else {
        	   return jsonError('参数错误');
        }

    }
    public function tagsSelect()
    {
      	$page = input('post.page');
		$limit = input('post.limit');
        $cid = input('post.cid');
		$orderBy = input('post.orderBy');
		$order = input('post.order');
        $keywords = input('post.keywords');
        $domain = input('post.domain');
		if (!$page) {
		  $page = 1;
		}
		if (!$limit) {
		  $limit = 10;
		}
		if (!$orderBy) {
		 $orderBy = 'id';
		}
		if (!$order) {
			$order = 'desc';
		}
        $itemList = model($this->itemModelNameSpace)->getItemList($cid,$page,$limit,$orderBy,$order,'',$keywords);
        $itemCount = model($this->itemModelNameSpace)->getCount($cid,'', $keywords);
	    return jsonSuccess('',['tagsList' => $itemList,'total' => $itemCount,'page' => $page]);
    }

    public function tagsEdit()
    {
		$id = input('post.id');
		$name = input('post.name');
        $cid = input('post.cid');
        $url_name = input('post.url_name');
		$item_type = input('post.item_type');
        $description = input('post.description');
        
		if(!$id){
			return jsonError('缺少ID');
		}
		if(!$name){
			return jsonError('请输入名称');
		}
		if(!$TagsInfo = db($this->tags)->where('id',$id)->find()){
          	return jsonError('标签不存在');
        }
        $itemInfo = db($this->tags)->where('id','<>',$id)->where('name',$name)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        if ($url_name) {
            $itemInfo = db($this->tags)->where('id','<>',$id)->where('url_name',$url_name)->find();
            if ($itemInfo) {
              return jsonError('别名已存在，请重新输入');
            }
        }
        if(db($this->tags)->where('id',$id)->update([
           'name' => $name,
           'url_name' => $url_name,
           'item_type' => $item_type,
           'description' => $description,
           'cid' => $cid,
           ])){
        }
        return  jsonSuccess('修改成功');
    }


    public function itemTagsSelectByItem()
    {

        $itemType = input('post.itemType');
        $itemId = input('post.itemId');

        if (!$itemType) {
            return jsonError('缺少类型');
        }
        if (!$itemId) {
            return jsonError('缺少类型Id');
        }

        $tagsList = db($this->itemTags)->where('item_type',$itemType)->where('item_id',$itemId)->select();

        if ($tagsList) {
            foreach ($tagsList as $k => $v){
                $tagsList[$k]['tags'] = db($this->tags)->where('id',$v['tags_id'])->find();
            }
        }
        return jsonSuccess('',['tagsList' => $tagsList]);
    }
    
    public function itemTransferAll()
    {
        $cid = input('post.cid');
        $ids = input('post.ids');
        if (!$ids) {
          return jsonError('缺少参数');
        }
        if ($cid == '') {
            $cid = 0;
        }
        $ids = explode(',',$ids);
        if (is_array($ids)) {
            foreach ($ids as $id){
                db($this->item)->where('id',$id)->update(['cid' => $cid]);
            }
            return jsonSuccess('操作成功');
        } else {
            return  jsonError('参数错误');
        }
    }
    /*
    * 分类模块
    */
   public function categoryAdd()
   {
        $pid = input('post.pid');
        $name = input('post.name');
        $url_name = input('post.url_name');
        $seo_title = input('post.seo_title');
        $template =  input('post.template');
        $description = input('post.description');
        $keywords = input('post.keywords');
        if (!$pid) {
            $pid = 0;
        }
        if (!$name) {
          return jsonError('请输入名称');
        }
        if (!$url_name) {
          return jsonError('请输入URL别名');
        }

        $itemCategoryInfo = db($this->itemCategory)->where('name',$name)->find();
        if ($itemCategoryInfo) {
            return jsonError('分类存在');
        }
        if (db($this->itemCategory)->insert(array(
            'name' => $name,
            'url_name' => $url_name,
            'seo_title' => $seo_title,
            'template' => $template,
            'keywords' => $keywords,
            'description' => $description,
            'pid' => $pid
        ))) {
            return jsonSuccess('添加成功');
        } else {
            return  jsonError('添加失败');
        }

    }


    public function categorySortSave()
    {
        $itemList = input('post.itemList/a');
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                if ($itemListInfo = db($this->itemCategory)->where('id',$val['id'])->find()) {
                    db($this->itemCategory)->where('id',$val['id'])->update(array('sort' => $val['sort']));
                }
                if ($itemList[$key]['children']) {
                    foreach ($itemList[$key]['children'] as $k => $v) {
                        if (db($this->itemCategory)->where('id',$v['id'])->find()) {
                            db($this->itemCategory)->where('id',$v['id'])->update(array('sort' => $v['sort']));
                        }
                    }
                }
            }
            return jsonSuccess('保存成功');
        }

    }

    public function categoryDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if (db($this->itemCategory)->where('id',$id)->find()) {
            db($this->itemCategory)->where('id',$id)->delete();
            return jsonSuccess('删除成功');
            } else {
                return  jsonError('不存在');
        }

    }
    
    public function categoryList()
    {
        $pid = input('post.pid');
        $page = input('post.page');
        $limit = input('post.limit');
        $orderBy = input('post.orderBy');
        $order = input('post.order');
        $pid = $pid ? $pid : 0;
        if (!$page) {
          $page = 1;
        }
        if (!$limit){
          $limit = 1000;
        }
        if (!$orderBy) {
           $orderBy = 'sort';
        }
        if (!$order) {
            $order = 'asc';
        }
        $categoryList = model($this->itemModelNameSpace)->getCategory($pid,$orderBy,$order,$limit);
        if ($categoryList) {
            foreach ($categoryList as $key => $val) {
                $categoryList[$key]['value'] = $val['id'];
                $categoryList[$key]['label'] = $val['name'];
                if ($categoryList[$key]['children']) {
                    foreach ($categoryList[$key]['children'] as $k => $v) {
                        $categoryList[$key]['children'][$k]['value'] = $v['id'];
                        $categoryList[$key]['children'][$k]['label'] = $v['name'];
                    }
                }
            }
        } else {
            $categoryList = array();
        }
        return jsonSuccess('',['categoryList' => $categoryList]);
    }
 
    public function categoryEdit()
    {
        $id = input('post.id');
        $pid = input('post.pid');
        $name = input('post.name');
        $url_name = input('post.url_name');
        $seo_title = input('post.seo_title');
        $template =  input('post.template');
        $description = input('post.description');
        $keywords = input('post.keywords');

        if (!$id) {
            return jsonError('缺少ID');
        }
        if (!$url_name) {
            return jsonError('缺少URL别名');
        }
        if (!$pid) {
            $pid = 0;
        }
        if (!$name) {
            return jsonError('请输入名称');
        }

        $categoryInfo = db($this->itemCategory)->where('id',$id)->find();
        if(!$categoryInfo){
            return jsonError('分类不存在');
        }
        
        $itemInfo = db($this->itemCategory)->where('id','<>',$id)->where('name',$name)->find();
        if ($itemInfo) {
          return jsonError('标题已存在，请重新输入');
        }
        $itemInfo = db($this->itemCategory)->where('id','<>',$id)->where('url_name',$url_name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        
        if (db($this->itemCategory)->where('id',$id)->update([
            'name' => $name,
            'url_name' => $url_name,
            'seo_title' => $seo_title,
            'description' => $description,
            'template' => $template,
            'keywords' => $keywords,
            'pid' => $pid
        ])) {
            return  jsonSuccess('修改成功');
        } else {
            return  jsonError('修改失败');
        }
    }

    public function getTemplate()
    {
        $pages = [];
        if (is_dir(ROOT_PATH . 'template' . DS . $this->mipInfo['template'] . DS . 'tag')) {
            $templateFile = opendir(ROOT_PATH . 'template' . DS . $this->mipInfo['template'] . DS . 'tag');
            if ($templateFile) {
                while (false !== ($file = readdir($templateFile))) {
                    if (substr($file, 0, 1) != '.' AND is_file(ROOT_PATH . 'template' . DS . $this->mipInfo['template'] . DS . 'tag' . DS . $file)) {
                        $pages[] = $file;
                    }
                }
                closedir($templateFile);
            }
        }
        if ($pages) {
            foreach ($pages as $key => $val) {
                $pages[$key] = preg_replace("/.html/","",$val);
            }
        }
        return jsonSuccess('',$pages);
    }
}