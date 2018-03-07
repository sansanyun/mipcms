<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\tag\controller;
use mip\Mip;
class Tag extends Mip 
{

    protected $beforeActionList = ['start'];
    public function start() {

    }
    public function index()
    {

        return $this->mipView('tag/tag');
    }
    public function tagDetail()
    {
        $id = input('param.id');
        $category = input('param.category');
        $sub = input('param.sub');
        $page = input('param.page');
        $page = $page ? $page : 1;
        
        $tagInfo = db($this->tags)->where('id',$id)->find();
        if ($tagInfo) {
            if ($tagInfo['url_name']) {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $this->domain .'/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['url_name'] .  '/');
                exit();
            }
        } else {
            $tagInfo =  db($this->tags)->where('url_name',$id)->find();
        }
        if (!$tagInfo) {
            $this->error('标签不存在','/');
        }
        
        //自定义变量
        $tagInfo['category'] = $category ? $category : '';
        $tagInfo['sub'] = $sub ? $sub : '';
        $tagInfo['page'] = $page ? $page : '';
        
        //分页数量
        $pageText = $page == 1 ? "" : $this->mipInfo['titleSeparator'] . "第" . $page . "页";
        
        //标题
        $mipTitle = $tagInfo['name'] . $pageText . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName'];
        $this->assign('mipTitle',$mipTitle);
        
        //关键词
        $tagsAboutList = db($this->tags)->where('name','like','%'.$tagInfo['name'].'%')->select();
        $tempTagsAboutList = [];
        $tempTagsAboutInfo = $tagInfo['name'];
        if ($tagsAboutList) {
            foreach ($tagsAboutList as $key => $val) {
                if ($key < 5) {
                    $tempTagsAboutList[] = $val['name'];
                }
            }
            $tempTagsAboutInfo = implode(',', $tempTagsAboutList);
        }
        $this->assign('mipKeywords',$tempTagsAboutInfo);
        
        //描述
        $this->assign('mipDescription',$tagInfo['description']);
        
        $this->assign('tagInfo',$tagInfo);
        
        return $this->mipView('tag/tagDetail');
    }


}