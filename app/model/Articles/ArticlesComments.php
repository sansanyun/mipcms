<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\model\Articles;
use think\Model;

class ArticlesComments extends Model
{
    public function users() {
        return $this->hasOne('app\model\Users\Users','uid','uid')->field('uid,username,nickname');;
    }
}