<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use mip\Mip;
use app\api\model\Articles;
use app\api\model\Users;
use mip\Pagination;

class User extends Mip {
    
    protected $beforeActionList = ['start'];
    public function start() {
        if(!$this->userId){
            $this->error('你尚未登录','/login/');
        }
    }
    public function index() {
        $page = input('param.page');
        if (!$page) {
            $page=1;
        }
        $list= Users::order('reg_time desc')->page($page,12)->select();
        $count = Users::count('uid');
        $this->assign('list',$list);
        $pagination_array= array(
            'base_url' => $this->domain.'/'.$this->userModelUrl,
            'total_rows' => $count,
            'per_page' => 10
        );
        $pagination = new Pagination($pagination_array);
        $this->assign('pagination',  $pagination->create_links());
        return $this->mipView('pc/user/user');
    }
    public function userDetail() {
        $uid = input('param.id');
        if (!$uid) {
            $this->error('你访问的页面不存在','/');
        }
        $itemInfo = Users::where('uid',$uid)->field(['password'], true)->find();
        if(!$itemInfo){
            return $this->error('用户不存在','/');
        }
        $this->assign('itemInfo',$itemInfo);
        
        $articleList = Articles::where('uid',$uid)->order('publish_time desc')->limit(10)->select();
        $articleList = model('api/Articles')->filter($articleList, $this->mipInfo['idStatus'], $this->domain, $this->public);
        $this->assign('articleList',$articleList);
        return $this->mipView('pc/user/userDetail');
    }
    public function setting() {
       
        return $this->mipView('pc/user/userSetting');
    }
    public function userArticle() {
       
        return $this->mipView('pc/user/userArticle');
    }
}