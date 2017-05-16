<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api\model;
use think\Model;

class ArticlesComments extends Model
{
    public function users() {
        return $this->hasOne('app\api\model\Users','uid','uid');
    }
}