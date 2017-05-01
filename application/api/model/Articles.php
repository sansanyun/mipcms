<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api\model;
use think\Model;
use app\api\model\Users;
use think\Cache;

class Articles extends Model
{
    public function users() {
    	return $this->hasOne('app\api\model\Users','uid','uid');
    }
    
    public function updateViews($id, $uid) {
        $tempCache = Cache::get('updateViewsArticle' . md5(session_id()) . intval($id));
        if ($tempCache) {
            return false;
        }
        Cache::set('updateViewsArticle' . md5(session_id()) . intval($id), time(), 60);
        $this->where('id',$id)->update([
            'views' => ['exp','views+1'],
        ]);
        Users::where('uid',$uid)->update([
            'article_views_num' => $this->where('uid',$uid)->sum('views'),
        ]);
        return true;
    }
    
}