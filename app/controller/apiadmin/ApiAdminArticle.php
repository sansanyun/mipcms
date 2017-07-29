<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use app\model\Articles\Articles;
use app\model\Articles\ArticlesComments;
use app\model\Articles\ArticlesCategory;
use app\model\Articles\ArticlesContent;
use app\model\Users\Users;
use app\model\Tags\Tags;
use app\model\Tags\ItemTags;
use think\Request;
use think\Loader;
use think\Db;
use mip\Htmlp;
use mip\AdminBase;
class ApiAdminArticle extends AdminBase
{
    public function index(){

    }
    public function articleAdd(Request $request)
    {
		if (Request::instance()->isPost()) {

	      	$title = input('post.title');
            $url_name = input('post.url_name');
	      	$content = input('post.content');
            $cid = input('post.cid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time') ? input('post.publish_time') : time();;
            $itemType = 'article';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            if ($tags) {
                $tags = explode(',',$tags);
            }
	      	if (!$title) {
	      	  return jsonError('请输入标题');
	      	}
	      	if (!$content) {
	      	  return jsonError('请输入内容');
	      	}
            if (!$cid) {
                $cid = 0;
            }
            if ($this->mipInfo['mipPostStatus']) {
                if (!$this->mipInfo['mipApiAddress']) {
                    return jsonError('请先去设置百度MIP的接口');
                }
            }
            if ($this->mipInfo['baiduYuanChuangStatus']) {
                if (!$this->mipInfo['baiduYuanChuangUrl']) {
                    return jsonError('请先去设置百度原创提交的接口');
                }
            }
            if ($this->mipInfo['baiduTimePcStatus']) {
                if (!$this->mipInfo['baiduTimePcUrl']) {
                    return jsonError('请先去设置百度PC链接提交的接口');
                }
            }
            if ($this->mipInfo['baiduTimeMStatus']) {
                if (!$this->mipInfo['baiduTimeMUrl']) {
                    return jsonError('请先去设置百度M链接提交的接口');
                }
            }
            if ($this->mipInfo['diyUrlStatus']) {
                if ($url_name) {
                    $articleInfoByUrlName = Articles::where('url_name',$url_name)->find();
                    if ($articleInfoByUrlName) {
                        return jsonError('自定义Url已存在');
                    }
                }
            }
            $articleInfo = Articles::where('title',$title)->find();
	      	if ($articleInfo) {
	      		return jsonError('文章已存在');
	      	} else {
	      	    $createInfo = Articles::create(array(
                   'title'=>htmlspecialchars($title),
                   'uid' => $this->userId,
                   'cid' => $cid,
                   'create_time' => time(),
                   'publish_time' => $publish_time,
                   'uuid' => uuid(),
                   'is_recommend' => $is_recommend,
                   'content_id' => uuid(),
                   'url_name' => $url_name,
                    ));
                if ($createInfo) {
                    ArticlesContent::create(array(
                       'id' => $createInfo['content_id'],
                       'content' => htmlspecialchars($content),
                    ));
                }
	      		if ($createInfo) {
                    if ($tags) {
                        model('app\model\Tags\ItemTags')->innerTags($tags, $itemType, $createInfo);
                    }
                    Users::where('uid',$this->userId)->update([
                        'article_num' => Articles::where('uid',$this->userId)->count(),
                    ]);
                    if ($this->mipInfo['baiduYuanChuangStatus']) {
                        $urls = $createInfo->domainUrl($createInfo);
                        $urls = explode(',',$urls);
                        if (is_array($urls)) {
                            $api = $this->mipInfo['baiduYuanChuangUrl'];
                            $ch = curl_init();
                            $options =  array(
                                CURLOPT_URL => $api,
                                CURLOPT_POST => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POSTFIELDS => implode("\n", $urls),
                                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                            );
                            curl_setopt_array($ch, $options);
                            $result = curl_exec($ch);
                        }
                    }
                    if ($this->mipInfo['baiduTimePcStatus']) {
                        $urls = $createInfo->domainUrl($createInfo);
                        $urls = explode(',',$urls);
                        if (is_array($urls)) {
                            $api = $this->mipInfo['baiduTimePcUrl'];
                            $ch = curl_init();
                            $options =  array(
                                CURLOPT_URL => $api,
                                CURLOPT_POST => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POSTFIELDS => implode("\n", $urls),
                                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                            );
                            curl_setopt_array($ch, $options);
                            $result = curl_exec($ch);
                        }
                    }
                    if ($this->mipInfo['baiduTimeMStatus']) {
                        $urls = $createInfo->domainMipUrl($createInfo);
                        $urls = explode(',',$urls);
                        if (is_array($urls)) {
                            $api = $this->mipInfo['baiduTimeMUrl'];
                            $ch = curl_init();
                            $options =  array(
                                CURLOPT_URL => $api,
                                CURLOPT_POST => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POSTFIELDS => implode("\n", $urls),
                                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                            );
                            curl_setopt_array($ch, $options);
                            $result = curl_exec($ch);
                        }
                    }
                    
                    if ($this->mipInfo['mipPostStatus']) {
                        $urls = $createInfo->domainMipUrl($createInfo);
                        $urls = explode(',',$urls);
                        if (is_array($urls)) {
                            $api = $this->mipInfo['mipApiAddress'];
                            $ch = curl_init();
                            $options =  array(
                                CURLOPT_URL => $api,
                                CURLOPT_POST => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POSTFIELDS => implode("\n", $urls),
                                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
                            );
                            curl_setopt_array($ch, $options);
                            $result = curl_exec($ch);
                            return jsonSuccess('发布成功',$result);
                        } else {
                            return jsonError('数据格式错误');
                        }
                    } else {
                        return jsonSuccess('发布成功');
                    }

		        } else {
		        	return  jsonError('提交失败');
		        }
	      	}

        }
    }

    public function articleDel(Request $request)
    {
		if (Request::instance()->isPost()) {
            $id = input('post.id');
            if(!$id){
	      	  return jsonError('缺少参数');
	      	}
            $articleInfo = Articles::where('id',$id)->find();
	   		if($articleInfo) {
	   		    ArticlesContent::where('id',$articleInfo['content_id'])->delete();;
                ItemTags::where('item_id',$articleInfo['uuid'])->delete();
                $articleInfo->delete();
                return jsonSuccess('删除成功');
            	} else {
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
                        $articlesContentInfo = ArticlesContent::where('id',$articleInfo['content_id'])->find();
                        $articlesContentInfo->delete();
                        $articleInfo->delete();
                    }
                }
                return jsonSuccess('删除成功');
	        }else{
	        	return  jsonError('参数错误');
	        }

        }
    }
    public function articleTransferAll(Request $request) {
        if (Request::instance()->isPost()) {
            $cid = input('post.cid');
            $ids = input('post.ids');
            if (!$ids) {
              return jsonError('缺少参数');
            }
            if (empty($cid)) {
                $cid = 0;
            }
            $ids = explode(',',$ids);
            if(is_array($ids)){
                foreach ($ids as $id){
                    Articles::where('id',$id)->update(['cid' => $cid]);
                }
                return jsonSuccess('操作成功');
            } else {
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
            $cid = input('post.cid');
            $keywords = input('post.keywords');
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
            if ($keywords) {
                if(empty($cid)) {
                    $sq = "%".$keywords."%";
                    $where['title']  = ['like',$sq];
                    $articleList = model('app\model\Articles\Articles')->getItemList(0, $page, $limit, $orderBy, $order, $where);
                    $itemCount = Articles::where($where)->count();
                } else {
                    $sq = "%".$keywords."%";
                    $where['title']  = ['like',$sq];
                    $articleList = model('app\model\Articles\Articles')->getItemList($cid, $page, $limit, $orderBy, $order, $where);
                    $tempCategory = ArticlesCategory::where('pid',$cid)->select();
                    $tempCategoryIds = array();
                    if ($tempCategory) {
                        foreach ($tempCategory as $key => $value) {
                            $tempCategoryIds[] = $value['id'];
                        }
                        $itemCount = Articles::where('cid','in',$tempCategoryIds)->where($where)->count('id');
                    } else {
                        $itemCount = Articles::where('cid',$cid)->where($where)->count('id');
                    }
                }
            } else {
                if(empty($cid)) {
                     $articleList = model('app\model\Articles\Articles')->getItemList(0, $page, $limit, $orderBy, $order);
                    $itemCount = Articles::count();
                } else {
                    $articleList = model('app\model\Articles\Articles')->getItemList($cid, $page, $limit, $orderBy, $order);
                    $tempCategory = ArticlesCategory::where('pid',$cid)->select();
                    $tempCategoryIds = array();
                    if ($tempCategory) {
                        foreach ($tempCategory as $key => $value) {
                            $tempCategoryIds[] = $value['id'];
                        }
                        $itemCount = Articles::where('cid','in',$tempCategoryIds)->count('id');
                    } else {
                        $itemCount = Articles::where('cid',$cid)->count('id');
                    }
                }
            }

		    return jsonSuccess('',['articleList' => $articleList,'total' => $itemCount,'page' => $page]);
        }
    }
    public function articleEdit(Request $request){
		if (Request::instance()->isPost()) {

			$id = input('post.id');
			$title = input('post.title');
            $url_name = input('post.url_name');
			$content = input('post.content');
            $cid = input('post.cid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time');
            $itemType = 'article';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            if ($tags) {
                $tags = explode(',',$tags);
            }
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

            if ($this->mipInfo['diyUrlStatus']) {
                if ($url_name) {
                    $articleInfoByUrlName = Articles::where('id','<>',$id)->where('url_name',$url_name)->find();
                    if ($articleInfoByUrlName) {
                        return jsonError('自定义Url已存在');
                    }
                }
            }
	        $updateArticleInfo = $articleInfo->where('id',$id)->update([
                'title' => htmlspecialchars($title),
                'cid' => $cid,
                'edit_time'=>time(),
                'publish_time' => $publish_time,
                'is_recommend' => $is_recommend,
                'url_name' => $url_name,
               ]);

            if ($articleInfo) {
                ArticlesContent::where('id',$articleInfo['content_id'])->update(array(
                   'content' => htmlspecialchars($content),
                ));
            }
	        if ($articleInfo) {
                if ($tags) {
                    model('app\model\Tags\ItemTags')->innerTags($tags, $itemType, $articleInfo);
                }
                return  jsonSuccess('修改成功');
	        }
  		}
   }

    public function articleRecomment(Request $request){
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $articleInfo = Articles::where('id',$id)->find();
            if (!$articleInfo) {
                return jsonError('文章不存在');
            }
            if ($articleInfo['is_recommend'] == 1) {
                $type = 0;
            } else {
                $type = 1;
            }

            $articleInfo->where('id',$id)->update([
                'is_recommend' => $type,
            ]);
            return  jsonSuccess('操作成功');
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
            $seo_title = input('post.seo_title');
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

	      	$articleInfo = ArticlesCategory::where('name',$name)->find();
	      	if ($articleInfo) {
	      		return jsonError('分类存在');
	      	} else {
	      		if (ArticlesCategory::create(array(
          		    'name' => $name,
                    'url_name' => $url_name,
                    'seo_title' => $seo_title,
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


    public function categorySortSave(Request $request)
    {
        if (Request::instance()->isPost()) {

            $itemList = input('post.itemList/a');
            if ($itemList) {
                foreach ($itemList as $key => $val) {
                    if ($itemListInfo = ArticlesCategory::where('id',$val['id'])->find()) {
                        ArticlesCategory::where('id',$val['id'])->update(array('sort' => $val['sort']));
                    }
                }
                return jsonSuccess('保存成功');
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
			  $limit = 1000;
			}
			if(!$orderBy){
               $orderBy = 'sort';
			}
			if(!$order){
				$order = 'asc';
			}
		    $categoryList = ArticlesCategory::limit($limit)->page($page)->order($orderBy, $order)->select();
            if ($categoryList) {
                foreach ($categoryList as $k => $v) {
                    if ($v['pid'] == 0) {
                        $children = ArticlesCategory::where('pid',$v['id'])->select();
                        $categoryList[$k]['_child'] = $children;
                        if ($children) {
                        $categoryList[$k]['children'] = $children;
                        }
                    } else {
                        $categoryList[$k]['_child'] = array();
                    }
                }
            }
		    return jsonSuccess('',['categoryList' => $categoryList,'total' => ArticlesCategory::count(),'page' => $page]);
        }
    }
    public function categoryEdit(Request $request){
		if (Request::instance()->isPost()) {

			$id = input('post.id');
			$pid = input('post.pid');
			$name = input('post.name');
            $url_name = input('post.url_name');
            $seo_title = input('post.seo_title');
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

			$categoryInfo = ArticlesCategory::getById($id);
			if(!$categoryInfo){
                return jsonError('分类不存在');
	        }
            if ($categoryInfo->where('id',$id)->update([
                'name' => $name,
                'url_name' => $url_name,
                'seo_title' => $seo_title,
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