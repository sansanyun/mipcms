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
class ApiFrontArticle extends AuthBase
{
    public function index(){
         
    }
    public function frontArticlesSelectByUserInfo(Request $request) {
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
            $itemList = Articles::limit($limit)->page($page)->order($orderBy, $order)->select();
            foreach ($itemList as $key => $val) {
                $val['content'] = htmlspecialchars_decode($itemList[$key]->getContentByArticleId($val['id'],$val['content_id'])['content']);
                $itemList[$key]->users;
                $itemList[$key]->articlesCategory;
                $val['tempId'] = $this->mipInfo['idStatus'] ? $val['uuid'] : $val['id'];
            }
            return jsonSuccess('',['itemList' => $itemList,'total' => Articles::count(),'page' => $page]); 
        }
    }
}