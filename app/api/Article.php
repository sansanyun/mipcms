<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api;
use think\Request;
use think\Loader;
use app\api\model\Articles;
use app\api\model\ArticlesComments;
use app\api\model\ArticlesCategory;
use app\api\model\Users;
use app\api\model\Tags;
use app\api\model\ItemTags;
use mip\Htmlp;
use mip\AuthBase;
class Article extends AuthBase
{
    public function index(){
		 
    }
    public function articleAdd(Request $request){
		if (Request::instance()->isPost()) {
		    
	      	$title = Htmlp::htmlp(input('post.title'));
	      	$content = Htmlp::htmlp(input('post.content'));
            $cid = input('post.cid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time') ? input('post.publish_time') : time();;
            $itemType = 'article';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            
            $tags = explode(',',$tags);
	      	if (!$title) {
	      	  return jsonError('请输入标题');
	      	}
	      	if (!$content) {
	      	  return jsonError('请输入内容');
	      	}
            if (!$cid) {
                $cid = 0;
            }
            $articleInfo = Articles::where('title',$title)->find();
	      	if ($articleInfo) {
	      		return jsonError('文章已存在');
	      	} else {
	      	    $createInfo = Articles::create(array(
                   'title'=>htmlspecialchars($title),
                   'content' => htmlspecialchars($content),
                   'uid' => $this->userId,
                   'cid' => $cid,
                   'create_time' => time(),
                   'publish_time' => $publish_time,
                   'uuid' => uuid(),
                   'is_recommend' => $is_recommend,
                    ));
	      		if ($createInfo) {
                    if (is_array($tags)) {
                        ItemTags::where('item_id',$createInfo['id'])->where('item_type',$itemType)->delete();
                        foreach ($tags as $name){
                            if ($name) {
                                $tagInfo = Tags::where('name',$name)->find();
                                if (!$tagInfo) {
                                    $tagInfo =  Tags::create(array(
                                        'name' => $name,
                                        'item_type' => $itemType,
                                    ));
                                }
                                ItemTags::create(array(
                                    'tags_id'=>$tagInfo['id'],
                                    'item_id' => $createInfo['id'],
                                    'item_type' => $itemType,
                                ));
                            }
                        }
                    }
                    Users::where('uid',$this->userId)->update([
                        'article_num' => Articles::where('uid',$this->userId)->count(),
                    ]);
	      			return jsonSuccess('添加成功');
		        } else {
		        	return  jsonError('添加失败');
		        }
	      	}
	      	
        }
    }
    
    public function articleDel(Request $request){
		if (Request::instance()->isPost()) {
            $id = input('post.id');
            if(!$id){
	      	  return jsonError('缺少参数');
	      	}
	   		if($articleInfo=Articles::where('id',$id)->find()){
                $articleInfo->delete();
                return jsonSuccess('删除成功');
        	}else{
	        	return  jsonError('文章不存在');
	        }
	      	
        }
    }
    public function articlesDel(Request $request){
		if (Request::instance()->isPost()) {
            $ids = input('post.ids');
            if(!$ids){
	      	  return jsonError('缺少参数');
	      	}
            $ids = explode(',',$ids);
	      	if(is_array($ids)){
                foreach ($ids as $id){
                    if($articleInfo = Articles::where('id',$id)->find()){
                       $articleInfo->delete();
                    }
                }
                return jsonSuccess('删除成功');
	        }else{
	        	return  jsonError('参数错误');
	        }
	      	
        }
    }
    public function articlesSelect(Request $request){
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
		    $articleList = Articles::limit($limit)->page($page)->order($orderBy, $order)->select();
		    foreach ($articleList as $key => $val) {
		        $val['content'] = htmlspecialchars_decode($val['content']);
                $articleList[$key]->users;
                $articleList[$key]->articlesCategory;
                $val['id'] = $this->mipInfo['idStatus'] ? $val['uuid'] : $val['id'];
		    }
		    return jsonSuccess('',['articleList' => $articleList,'total' => Articles::count(),'page' => $page]); 
        }
    }
    public function articleEdit(Request $request){
		if (Request::instance()->isPost()) {
		    
			$id = input('post.id');
			$title = Htmlp::htmlp(input('post.title'));
			$content = Htmlp::htmlp(input('post.content'));
            $cid = input('post.cid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time');
            $itemType = 'article';   
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            $tags = explode(',',$tags);
            if (!$title) {
              return jsonError('请输入标题');
            }
            if (!$cid) {
                $cid = 0;
            }
			if (!$id) {
				return jsonError('缺少ID');
			}
			if (!$title) {
				return jsonError('请输入标题');
			}
			if (!$content) {
				return jsonError('请输入内容');
			}
			$articleInfo = Articles::getById($id);
			if (!$articleInfo) {
	          	return jsonError('文章不存在');
	        }
	        
	        $articleInfo->where('id',$id)->update([
                'title' => htmlspecialchars($title),
                'content' => htmlspecialchars($content),
                'cid' => $cid,
                'edit_time'=>time(),
                'publish_time' => $publish_time,
                'is_recommend' => $is_recommend,
               ]);
	        if ($articleInfo) {
                if (is_array($tags)) {
                    ItemTags::where('item_id',$articleInfo['id'])->where('item_type',$itemType)->delete();
                    foreach ($tags as $name){
                        if ($name) {
                            $tagInfo = Tags::where('name',$name)->find();
                            if (!$tagInfo) {
                                $tagInfo =  Tags::create(array(
                                    'name' => $name,
                                    'item_type' => $itemType,
                                ));
                            }
                            ItemTags::create(array(
                                'tags_id'=>$tagInfo['id'],
                                'item_id' => $articleInfo['id'],
                                'item_type' => $itemType,
                            ));
                        }
                    }
                }
                return  jsonSuccess('修改成功');
	        }
  		}
   }
   
    public function articleRecomment(Request $request){
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $whereId = $this->mipInfo['idStatus'] ? 'uuid' : 'id';
            $articleInfo = Articles::where($whereId,$id)->find();
            if (!$articleInfo) {
                return jsonError('文章不存在');
            }
            if ($articleInfo['is_recommend'] == 1) {
                $type = 0;
            } else {
                $type = 1;
            }
            
            $whereId = $this->mipInfo['idStatus'] ? 'uuid' : 'id';
            $articleInfo->where($whereId,$id)->update([
                'is_recommend' => $type,
            ]);
            return  jsonSuccess('操作成功');
        }
    }
   /*
    * 回复模块
    */
   public function commentsAdd(Request $request){
		if (Request::instance()->isPost()) {
	      	$articleId = input('post.articleId');
	      	$content = Htmlp::htmlp(input('post.content'));
	      	if(!$articleId){
	      	  return jsonError('参数错误');
	      	}
	      	if(!$content){
	      	  return jsonError('请输入内容');
	      	}
	      	if(!$articleInfo=Articles::where('id',$articleId)->find()){
	      		return jsonError('文章不存在');
	      	}else{
	      		if(ArticlesComments::create(array('item_id'=>$articleId,'uid' => $this->userId,'content' => htmlspecialchars($content),'create_time'=>time()))){
	      		    $articleCommentsCount = ArticlesComments::where('item_id',$articleId)->count();
	      		    Articles::where('id',$articleId)->update(array('comments' => $articleCommentsCount));
                    Users::where('uid',$this->userId)->update([
                        'article_comments_num' => ArticlesComments::where('uid',$this->userId)->count(),
                    ]);
	      			return jsonSuccess('添加成功');
		        }else{
		        	return  jsonError('添加失败');
		        }
	      	}
	      	
        }
    }
    
    public function commentDel(Request $request){
		if (Request::instance()->isPost()) {
            $id = input('post.id');
           
            if (!$id) {
	      	  return jsonError('缺少参数');
	      	}
	      	
	      	$articleCommentsInfo = ArticlesComments::where('id',$id)->find();
	      	
	   		if ($articleCommentsInfo) {
                if ($this->userId == $articleCommentsInfo['uid'] || $this->isAdmin) {
                    $articleCommentsInfo->delete();
                    return jsonSuccess('删除成功');
                } else {
                    return jsonError('无权限操作');
                }
        	} else {
	        	return  jsonError('回复不存在');
	        }
	      	
        }
    }
    public function commentsDel(Request $request){
		if (Request::instance()->isPost()) {
            $ids = input('post.ids');
            if(!$ids){
	      	  return jsonError('缺少参数');
	      	}
            $ids = explode(',',$ids);
	      	if(is_array($ids)){
                foreach ($ids as $id){
                    if($articleCommentsInfo = ArticlesComments::where('id',$id)->find()){
                        if ($this->userId == $articleCommentsInfo['uid'] || $this->isAdmin) {
                            $articleCommentsInfo->delete();
                        } else {
                            return jsonError('无权限操作');
                        }
                    }
                }
                return jsonSuccess('删除成功');
	        }else{
	        	return  jsonError('参数错误');
	        }
	      	
        }
    }
    public function commentsSelect(Request $request){
		if (Request::instance()->isPost()) {
            $itemId = input('post.itemId');
	      	$page = input('post.page');
			$limit = input('post.limit');
			$orderBy = input('post.orderBy');
			$order = input('post.order');
            if (!$itemId) {
              $itemId = 1;
            }
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
				$order = 'asc';
			}
		    $articlesCommentsList = ArticlesComments::where('item_id',$itemId)->limit($limit)->page($page)->order($orderBy, $order)->select();
		    
            if ($articlesCommentsList) {
                foreach ($articlesCommentsList as $k=>$v){
                    $articlesCommentsList[$k]['content']= str_replace("\r\n", ' ', strip_tags($v['content']));
                    $articlesCommentsList[$k]->users;
                }
            }
		    
		    return jsonSuccess('ok',['itemList' => $articlesCommentsList,'total' => ArticlesComments::where('item_id',$itemId)->count(),'page' => $page]); 
        }
    }
    public function commentsEdit(Request $request){
		if (Request::instance()->isPost()) {
			$id = input('post.id');
			$content = Htmlp::htmlp(input('post.content'));
			if(!$id){
				return jsonError('缺少ID');
			}
			if(!$content){
				return jsonError('请输入内容');
			}
			if(!$articleCommentsInfo = ArticlesComments::getById($id)){
	          	return jsonError('回复不存在');
	        }
	        if ($this->userId == $articleCommentsInfo['uid'] || $this->isAdmin) {
                if($articleCommentsInfo->where('id',$id)->update(['content' => htmlspecialchars($content),'edit_time'=>time()])){
                    return  jsonSuccess('修改成功');
                }
	        } else {
    	        return jsonError('无权限操作');
	        }
  		}
   }
   /*
    * 分类模块
    */
   public function categoryAdd(Request $request){
		if (Request::instance()->isPost()) {
		    
	      	$pid = input('post.pid');
	      	$name = input('post.name');
            $url_name = input('post.url_name');
            $description = input('post.description');
            $keywords = input('post.keywords');
	      	if (!$pid) {
	      		$pid = 0;
	      	}
	      	if (!$name) {
	      	  return jsonError('请输入名称');
	      	}
	      	
	      	$articleInfo = ArticlesCategory::where('name',$name)->find();
	      	if ($articleInfo) {
	      		return jsonError('分类存在');
	      	} else {
	      		if (ArticlesCategory::create(array(
          		    'name' => $name,
                    'url_name' => $url_name,
                    'description' => $description,
                    'keywords' => $keywords,
                    'pid'=>$pid
                    ))) {
	      		   return jsonSuccess('添加成功');
		        } else {
		        	return  jsonError('添加失败');
		        }
	      	}
	      	
        }
    }
    
    public function categoryDel(Request $request){
		if (Request::instance()->isPost()) {
            $id = input('post.id');
            if(!$id){
	      	  return jsonError('缺少参数');
	      	}
	   		if($articlesCategoryInfo=ArticlesCategory::where('id',$id)->find()){
                $articlesCategoryInfo->delete();
                return jsonSuccess('删除成功');
        	}else{
	        	return  jsonError('回复不存在');
	        }
	      	
        }
    }
    public function categorySelect(Request $request){
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
		    $categoryList = ArticlesCategory::limit($limit)->page($page)->order($orderBy, $order)->select();
		    return jsonSuccess('',['categoryList' => $categoryList,'total' => ArticlesCategory::count(),'page' => $page]); 
        }
    }
    public function categoryEdit(Request $request){
		if (Request::instance()->isPost()) {
		    
			$id = input('post.id');
			$pid = input('post.pid');
			$name = input('post.name');
            $url_name = input('post.url_name');
            $description = input('post.description');
            $keywords = input('post.keywords');
			
			if (!$id) {
				return jsonError('缺少ID');
			}
			if (!$pid) {
				$pid = 0;
			}
			if (!$name) {
				return jsonError('请输入名称');
			}
			
			$categoryInfo = ArticlesCategory::getById($id);
			if(!$categoryInfo){
                return jsonError('分类不存在');
	        }
            if ($categoryInfo->where('id',$id)->update([
                'name' => $name,
                'url_name' => $url_name,
                'description' => $description,
                'keywords' => $keywords,
                'pid' => $pid])) {
                return  jsonSuccess('修改成功');
            } else {
                return  jsonError('修改失败');
            }
  		}
   }
}