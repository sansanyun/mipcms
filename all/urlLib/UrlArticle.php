<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace urlLib;
use think\Controller;
use think\Config;
use think\Hook;

class UrlArticle extends Controller
{
    public $addonsNameSpace;
    public function _initialize()
    {
        parent::_initialize();
        $itemList = db('GlobalAction')->select();
        if ($itemList) {
            try {
                foreach ($itemList as $key => $val) {
                    $addonsName = $val['name'];
                    if ((strpos(strtolower($addonsName), 'supertagssite') !== false) || (strpos(strtolower($addonsName), 'urllib') !== false)) {
                        $this->addonsNameSpace = "addons" . "\\" . $addonsName . "\\" . "controller" . "\\" . "GlobalAction";
                    }
                }
            } catch (\Exception $e) {
                
            }
        }
        
        $this->articles = config('articles');
        $this->item = config('articles');
        $this->articlesContent = config('articlesContent');
        $this->itemCategory = config('articlesCategory');
        $this->itemTags = config('itemTags');
        $this->tags = config('tags');
        $this->mipInfo = config('mipInfo');
        $this->domain = config('domain');
        $this->dataId = config('dataId');
    }
    
    public function getUrl($item,$domain = null)
    {
        if ($this->mipInfo['idStatus']) {
            $tempId = $item['uuid'];
        } else {
            $tempId = $item['id'];
        }
        if ($this->mipInfo['diyUrlStatus']) {
            if ($item['url_name']) {
                $tempId = $item['url_name'];
            }
        }
        
        if (!$domain) {
            $domain = $this->domain;
        }
        
        $item['categoryInfo'] = db('articlesCategory')->where('id',$item['cid'])->find();
        
        $url = $domain . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
        
        if ($this->mipInfo['urlCategory']) {
            $item['itemCategory'] = db('articlesCategory')->where('id',$item['cid'])->find();
            if ($item['itemCategory']) {
                $item['articlesLastCategory'] = db('articlesCategory')->where('id',$item['itemCategory']['pid'])->find();
                if ($item['articlesLastCategory']) {
                    $url = $domain . '/' . $item['articlesLastCategory']['url_name'] . '/' . $item['itemCategory']['url_name'] . '/' . $tempId . '.html';
                } else {
                    $url = $domain . '/' . $item['itemCategory']['url_name'] . '/' . $tempId . '.html';
                }
            }
        }
        if ($this->addonsNameSpace) {
            if ($tempUrl = model($this->addonsNameSpace)->getArticleUrl($item)) {
                $url = $tempUrl;
            }
        }
        return $url;
    }
    
    public function getPageUrl($category,$sub,$tagInfo)
    {
        if (!$tagInfo) {
            if ($category) {
                if ($this->mipInfo['aritcleLevelRemove']) {
                    $url = $this->domain . '/' . $category . '/';
                } else {
                    $url = $this->domain . '/' . $this->mipInfo['articleModelUrl'] . '/' . $category . '/';
                }
                if ($sub) {
                    $url = $this->domain . '/' . $category . '/' . $sub . '/';
                }
            } else {
                $url = $this->domain . '/' .  $this->mipInfo['articleModelUrl'] . '/';
            }
        }
        if ($tagInfo) {
            if ($tagInfo['url_name']) {
                $url = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['url_name'] . '/';
            } else {
                $url = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['id'] . '/';
            }
        }
        
        if ($this->addonsNameSpace) {
            if ($tempUrl = model($this->addonsNameSpace)->getTagsPageUrl($category,$sub,$tagInfo)) {
                $url = $tempUrl;
            }
        }
        return $url;
    }

}
