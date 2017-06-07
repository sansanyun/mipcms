<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\api\model;
use think\Model;
use app\api\model\Users;
use app\api\model\ArticlesCategory;
use app\api\model\ArticlesContent;
use think\Db;
use think\Cache;

class Articles extends Model
{
    public function users() {
    	return $this->hasOne('app\api\model\Users','uid','uid');
    }
    public function articlesCategory() {
        return $this->hasOne('app\api\model\ArticlesCategory','id','cid');
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
        return true;
    }
    public function getContentByArticleId($id,$content_id) {
        if (!$id) {
            return false;
        }
        $countNum = $id/1000000;
        if ($countNum < 1) {
            return ArticlesContent::where('id',$content_id)->find();
        } else {
            if (intval($countNum) > 10) {
                    $countNum = 10;
            }
            $dbName = 'ArticlesContent'.intval($countNum);
            return Db::name($dbName)->where('id',$content_id)->find();
        }
    }
    public function filter($list, $idStatus, $domain, $public) {
        if (!$list) {
            return false;
        }
        $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
            '|'. 
            '([0-9a-z_!~*\'()-]+\.)*'. 
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
            '[a-z]{2,6})'.   
            '(:[0-9]{1,4})?'.  
            '((\/\?)|'.  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
        foreach ($list as $k => $v){
            $this->users($list[$k]);
            $v['content'] = htmlspecialchars_decode($this->getContentByArticleId($v['id'],$v['content_id'])['content']);;
            if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $v['content'], $imgs)) {
                if (@preg_match($patern,$imgs[1][0])) {
                    $list[$k]['firstImg'] = $imgs[1][0];
                } else {
                    $list[$k]['firstImg'] = $domain. '/' . $public. $imgs[1][0];
                }
            } else {
                $list[$k]['firstImg'] = null;
            }
            $v['tempId'] = $idStatus ? $v['uuid']:$v['id'];
            $v['content'] = strip_tags(htmlspecialchars_decode($v['content']));
        }
        return $list;
    }
    
}