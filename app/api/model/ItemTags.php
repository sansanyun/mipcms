<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.

namespace app\api\model;

use think\Model;

class ItemTags extends Model
{
    public function tags()
    {
        return $this->hasOne('Tags','id','tags_id');
    }
}