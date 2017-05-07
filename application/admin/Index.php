<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\admin;
use mip\AdminBase;

class Index extends AdminBase
{
	protected $beforeActionList = ['start'];

    public function start() { 
   
    }
    public function setting() {
        return $this->mipView('admin/setting');
    }
    public function index() {
         
//       今日
        $this->assign('todayUserCount',db('Users')->where('reg_time','>',strtotime(date('Y-m-d')))->count());
//       昨日
        $this->assign('yesterdayUserCount',db('Users')->where('reg_time>'.strtotime(date('Y-m-'.(date('d')-1))).' AND reg_time<'.strtotime(date('Y-m-d')))->count());//写完后 自己都看不懂了。。。
//       总人数
        $this->assign('allUserCount',db('Users')->count());
        return $this->mipView('admin/index');
    }
    
    public function user() {
        return $this->mipView('admin/user');
    }
    public function article() {
        return $this->mipView('admin/article');
    }
    
    public function role() {
        return $this->mipView('admin/role');
    }
    
    public function role_authorization() {
        return $this->mipView('admin/role_authorization');
    }
    
    public function spider() {
        return $this->mipView('admin/spider');
    }
    
    public function update() {
        return $this->mipView('admin/update');
    }
}