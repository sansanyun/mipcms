<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\ApiUser;
use app\model\Articles\Articles;
use app\model\Articles\ArticlesComments;
use app\model\Articles\ArticlesCategory;
use app\model\Articles\ArticlesContent;
use app\model\Users\Users;
use app\model\Tags\Tags;
use app\model\Tags\ItemTags;
use think\Request;
use think\Loader;
use mip\Htmlp;
use mip\AuthBase;
class ApiUserArticle extends AuthBase
{
    public function index() {
         
    }
    public function articleAdd(Request $request) {
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
                    ));
                if ($createInfo) {
                    ArticlesContent::create(array(
                       'id' => $createInfo['content_id'],
                       'content' => htmlspecialchars($content),
                    ));
                    if ($tags) {
                        Loader::model('app\model\Tags\ItemTags')->innerTags($tags, $itemType, $createInfo);
                    }
                    Users::where('uid',$this->userId)->update([
                        'article_num' => Articles::where('uid',$this->userId)->count(),
                    ]);
                    return jsonSuccess('提交成功');
                } else {
                    return  jsonError('提交失败');
                }
            }
            
        }
    }
    public function articlesSelectByUserInfo(Request $request) {
        if (Request::instance()->isPost()) {
            $page = input('post.page');
            $limit = input('post.limit');
            $orderBy = input('post.orderBy');
            $order = input('post.order');
            $uid = $this->userId;
            if (!$uid) {
                return jsonError('缺少用户UID');
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
                $order = 'desc';
            }
            $where['uid'] = $uid;
            $itemList = Articles::where($where)->limit($limit)->page($page)->order($orderBy, $order)->select();
            $total = Articles::where($where)->count();
            foreach ($itemList as $key => $val) {
                $val['content'] = htmlspecialchars_decode($itemList[$key]->getContentByArticleId($val['id'],$val['content_id'])['content']);
                $itemList[$key]->users;
                $itemList[$key]->articlesCategory;
                $val['tempId'] = $this->mipInfo['idStatus'] ? $val['uuid'] : $val['id'];
            }
            return jsonSuccess('',['itemList' => $itemList,'total' => $total,'page' => $page]); 
        }
    }
    
    
    public function commentsAdd(Request $request) {
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
                if(ArticlesComments::create(array('id' => uuid(),'item_id'=>$articleId,'uid' => $this->userId,'content' => htmlspecialchars($content),'create_time'=>time()))){
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
    
    public function commentDel(Request $request) {
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
    public function commentsDel(Request $request) {
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
    public function commentsSelect(Request $request) {
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
    public function commentsEdit(Request $request) {
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
}