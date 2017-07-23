<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\model\Asks;
use think\Model;

class AsksAnswersComments extends Model
{
    
    public function users()
    {
        return $this->hasOne('app\model\Users\Users','uid','uid')->field('uid,username,nickname');;
    }
    public function replyUsers()
    {
        return $this->hasOne('app\model\Users\Users','uid','reply_uid')->field('uid,username,nickname');;
    }
    
}