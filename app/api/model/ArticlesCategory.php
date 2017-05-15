<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api\model;
use think\Model;

class ArticlesCategory extends Model
{
    public function articles()
    {
        return $this->hasMany('articles','cid','id')->order('publish_time desc')->where('publish_time','<',time())->field('id,uuid,publish_time,title')->limit(9);
    }
}