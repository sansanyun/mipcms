<?php
//MipSNS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MipSNS.Com All rights reserved.
//Author: 记忆、del <380822670@qq.com>
namespace app\model\Asks;
use think\Model;

class AsksAnswers extends Model
{
   	public function users()
    {
    	return $this->hasOne('app\model\Users\Users','uid','uid')->field('uid,username,nickname');
    }
    
    public function comments() {
        return $this->hasMany('AsksAnswersComments','item_id','id')->order('create_time asc');
    }
    
}