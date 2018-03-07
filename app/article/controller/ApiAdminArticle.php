<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\controller;
use think\Request;
use mip\Htmlp;
use mip\AdminBase;
class ApiAdminArticle extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->itemModelNameSpace = 'app\article\model\Articles';
        $this->itemName = $this->mipInfo['productModelName'];
        $this->item = $this->articles;
        $this->itemCategory = $this->articlesCategory;
        $this->itemContent = $this->articlesContent;
        $this->itemType = 'article';
    }
   
    public function itemAdd()
    {
        $title = input('post.title');
        $keywords = input('post.keywords');
        $description = input('post.description');
        $link_tags = input('post.link_tags');
        $url_name = input('post.url_name');
        $content = input('post.content');
        $publish_time = input('post.publish_time') ? input('post.publish_time') : time();
        $cid = input('post.cid') ? input('post.cid') : 0;
        $is_recommend = input('post.is_recommend') ? input('post.is_recommend') : 0;
        $fieldList = input('post.fieldList');
        $fieldList = json_decode($fieldList,true);
        $tags = input('post.tags');
        if ($tags) {
            $tags = explode(',',$tags);
        }
        $itemType = $this->itemType;
        if (!$title) {
          return jsonError('请输入标题');
        }
        if (!$content) {
          return jsonError('请输入内容');
        }
        if ($url_name) {
            $itemInfoByUrlName = db($this->item)->where('url_name',$url_name)->find();
            if ($itemInfoByUrlName) {
                return jsonError('自定义的Url已存在');
            }
        }
        $itemInfo = db($this->item)->where('title',$title)->find();
        if ($itemInfo) {
            return jsonError('标题已存在');
        }
        $uuid = uuid();
        $resArray = array (
            'title' => htmlspecialchars($title),
            'keywords' => $keywords,
            'description' => $description,
            'link_tags' => $link_tags,
            'uid' => $this->userId,
            'cid' => $cid,
            'publish_time' => $publish_time,
            'uuid' => $uuid,
            'is_recommend' => $is_recommend,
            'content_id' => $uuid,
            'url_name' => $url_name,
        );
        if (is_array($fieldList)) {
             for ($i=0; $i < count($fieldList); $i++) { 
                $resArray[$fieldList[$i]['key']] = $fieldList[$i]['value'];
             }
        }
        db($this->item)->insert($resArray);
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if ($itemInfo) {
            db($this->itemContent)->insert(array(
               'id' => $uuid,
               'content' => htmlspecialchars($content),
            ));
            if ($tags) {
                model('app\tag\model\ItemTags')->innerTags($tags, $itemType, $itemInfo);
            }
            model($this->itemModelNameSpace)->itemPushUrl($itemInfo);
        }
        return jsonSuccess('发布成功');

    }

    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('id',$id)->find();
        if ($itemInfo) {
            db($this->itemContent)->where('id',$itemInfo['content_id'])->delete();
            db($this->item)->where('id',$id)->delete();
            db($this->itemTags)->where('item_id',$itemInfo['uuid'])->delete();
            return jsonSuccess('操作成功');
            } else {
            return  jsonError('不存在');
        }
            
    }

    public function itemsDel()
    {
        $ids = input('post.ids');
        if (!$ids) {
          return jsonError('缺少参数');
        }
        $ids = explode(',',$ids);
        foreach ($ids as $id) {
            $itemInfo = db($this->item)->where('id',$id)->find();
            if ($itemInfo) {
                db($this->item)->where('id',$id)->delete();
                $itemContentInfo = db($this->itemContent)->where('id',$itemInfo['content_id'])->find();
                if ($itemContentInfo) {
                    db($this->itemContent)->where('id',$itemInfo['content_id'])->delete();
                }
                db($this->itemTags)->where('item_id',$itemInfo['uuid'])->delete();
            }
        }
        return jsonSuccess('操作成功');

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
    
    public function itemFind()
    {
        $uuid = input('post.uuid');
        if (!$uuid) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if (!$itemInfo) {
          return jsonError('不存在');
        }
        
        $itemContentInfo = model($this->itemModelNameSpace)->getContentByItemId($itemInfo['id'],$itemInfo['content_id']);
        
        $itemInfo['content'] = htmlspecialchars_decode($itemContentInfo['content']);
        
        return jsonSuccess('',$itemInfo);
    }

    public function itemList()
    {
      	$page = input('post.page');
		$limit = input('post.limit');
		$orderBy = input('post.orderBy');
		$order = input('post.order');
        $cid = input('post.cid');
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
        $itemList = model($this->itemModelNameSpace)->getItemList($cid,$page,$limit,$orderBy,$order,null,$keywords);
        $itemCount = model($this->itemModelNameSpace)->getCount($cid,'', $keywords);
        if ($domain) {
            if ($itemList) {
                foreach ($itemList as $key => $val) {
                    $itemList[$key]['url'] = model($this->itemModelNameSpace)->getUrlByItemInfo($val,$domain);
                }
            }
        }
	    return jsonSuccess('',['itemList' => $itemList,'total' => $itemCount,'page' => $page]);
    }
    
    public function itemEdit()
    {
		$uuid = input('post.uuid');
        $title = input('post.title');
        $keywords = input('post.keywords');
        $description = input('post.description');
        $link_tags = input('post.link_tags');
        $url_name = input('post.url_name');
        $content = input('post.content');
        $cid = input('post.cid');
        $publish_time = input('post.publish_time');
        $itemType = $this->itemType;
        $is_recommend = input('post.is_recommend');
        $tags = input('post.tags');
        $fieldList = input('post.fieldList');
        $fieldList = json_decode($fieldList,true);
        if (!$is_recommend) {
            $is_recommend = 0;
        }
        if (!$title) {
          return jsonError('请输入标题');
        }
        if (!$cid) {
            $cid = 0;
        }
        if (!$uuid) {
            return jsonError('缺少ID');
        }
        if (!$title) {
            return jsonError('请输入标题');
        }
        if (!$content) {
            return jsonError('请输入内容');
        }
        if ($tags) {
            $tags = explode(',',$tags);
        }
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if (!$itemInfo) {
            return jsonError('不存在');
        }
        if ($url_name) {
            $itemInfoByUrlName = db($this->item)->where('uuid','<>',$uuid)->where('url_name',$url_name)->find();
            if ($itemInfoByUrlName) {
                return jsonError('自定义Url已存在');
            }
        }
        $resArray = array(
            'title' => htmlspecialchars($title),
            'keywords' => $keywords,
            'description' => $description,
            'link_tags' => $link_tags,
            'cid' => $cid,
            'edit_time'=>time(),
            'publish_time' => $publish_time,
            'is_recommend' => $is_recommend,
            'url_name' => $url_name
        );
        if (is_array($fieldList)) {
            for ($i=0; $i < count($fieldList); $i++) { 
                @$resArray[$fieldList[$i]['key']] = $fieldList[$i]['value'];
            }
        }
        
        $itemInfo = db($this->item)->where('uuid',$uuid)->update($resArray);
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if ($itemInfo) {
            db($this->itemContent)->where('id',$itemInfo['content_id'])->update(array(
               'content' => htmlspecialchars($content),
            ));
            if ($tags) {
                model('app\tag\model\ItemTags')->innerTags($tags, $itemType, $itemInfo);
            } else {
                db($this->itemTags)->where('item_id',$uuid)->where('item_type',$itemType)->delete();
            }
        }
        return jsonSuccess('修改成功');
    }

    
    public function itemRecomment()
    {
        $id = input('post.id');
        $itemInfo = db($this->item)->where('id',$id)->find();
        if (!$itemInfo) {
            return jsonError('不存在');
        }
        if ($itemInfo['is_recommend'] == 1) {
            $type = 0;
        } else {
            $type = 1;
        }
        db($this->item)->where('id',$id)->update([
            'is_recommend' => $type,
        ]);
        return  jsonSuccess('操作成功');
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
        if (is_dir(ROOT_PATH . 'template' . DS . $this->mipInfo['template'] . DS . 'article')) {
            $templateFile = opendir(ROOT_PATH . 'template' . DS . $this->mipInfo['template'] . DS . 'article');
            if ($templateFile) {
                while (false !== ($file = readdir($templateFile))) {
                    if (substr($file, 0, 1) != '.' AND is_file(ROOT_PATH . 'template' . DS . $this->mipInfo['template'] . DS . 'article' . DS . $file)) {
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