<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace urlLib;
use think\Controller;
use think\Config;
use think\Hook;
class UrlTags extends Controller
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
    
    public function getUrl($itemInfo)
    {
        if (!$itemInfo) {
            return false;
        }
        if ($itemInfo['url_name']) {
            $url = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $itemInfo['url_name'] . '/';
        } else {
            $url = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $itemInfo['id'] . '/';
        }
        if ($this->addonsNameSpace) {
            if ($tempUrl = model($this->addonsNameSpace)->getTagsUrl($itemInfo)) {
                $url = $tempUrl;
            }
        }
        return $url;
    }
    
    public function getPageUrl($category,$sub)
    {
        if ($category) {
            $url = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $category . '/';
            if ($sub) {
                $url = $this->domain . '/' . $category . '/' . $sub . '/';
            }
        } else {
            $url = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/';
        }
        if ($this->addonsNameSpace) {
            if ($tempUrl = model($this->addonsNameSpace)->getTagsPageUrl($category,$sub)) {
                $url = $tempUrl;
            }
        }
        return $url;
    }

}
