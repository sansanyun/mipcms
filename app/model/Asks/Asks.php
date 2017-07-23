<?php
//MipSNS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MipSNS.Com All rights reserved.
//Author: 记忆、del <380822670@qq.com>
namespace app\model\Asks;
use think\Model;
use app\model\Users\Users;
use app\model\Asks\AsksCategory;
use app\model\Asks\AsksContent;
use app\model\Asks\AsksAnswersComments;
use app\model\Tags\Tags;
use app\model\Tags\ItemTags;
use think\Db;
use think\Cache;
use Mip\ModelBase;

class Asks extends ModelBase
{
  	public function users()
    {
    	return $this->hasOne('app\model\Users\Users','uid','uid')->field('uid,username,nickname');
    }
    
    public function asksCategory() 
    {
        return $this->hasOne('app\model\Asks\AsksCategory','id','cid');
    }
    
    public function domainUrl()
    {
        if ($this->mipInfo['idStatus']) {
            $tempId = $this->uuid;
        } else {
            $tempId = $this->id;
        }
        if ($this->askSetting['askDomain']) {
            return $this->askSetting['askDomain'];
        } else {
            return '/' . $this->mipInfo['askModelUrl'] . '/' . $tempId . '.html';
        }
    }
    
    public function tagsSelect($item_id,$type) {
        if (!$item_id) {
            return false;
        }
        $tags = ItemTags::where('item_id',$item_id)->where('item_type',$type)->select();
        if ($tags) {
            foreach ($tags as $k => $v) {
                $tags[$k] = $tagsInfo = Tags::where('id',$v['tags_id'])->find();
                $tags[$k]['url_name'] = $tagsInfo['url_name'] ? $tagsInfo['url_name'] : $tagsInfo['id'];
            }
        }
        return $tags;
    }
    
    
    public function asksAnswer()
    {
    	return $this->hasMany('app\model\Asks\AsksAnswers','item_id','uuid');
    }
    public function updateViews($id) {
        $tempCache = Cache::get('updateViewsAsk' . md5(session_id()) . intval($id));
        if ($tempCache) {
            return false;
        }
        Cache::set('updateViewsAsk' . md5(session_id()) . intval($id), time(), 60);
        $this->where('id',$id)->update([
            'views' => ['exp','views+1'],
        ]);
        return true;
    }
    
    public function getContentByAskId($id,$content_id) {
        if (!$id) {
            return false;
        }
        return AsksContent::where('id',$content_id)->find();
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
            $v['content'] = htmlspecialchars_decode($this->getContentByAskId($v['id'],$v['content_id'])['content']);;
            if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $v['content'], $imgs)) {
                if (@preg_match($patern,$imgs[1][0])) {
                    $list[$k]['firstImg'] = $imgs[1][0];
                } else {
                    $list[$k]['firstImg'] = $domain. $imgs[1][0];
                }
            } else {
                $list[$k]['firstImg'] = null;
            }
            $v['content'] = strip_tags(htmlspecialchars_decode($v['content']));
        }
        return $list;
    }
}