<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\controller;
use mip\Cutpagem;
use mip\Mip;
class ArticleDetail extends Mip
{
    public function index()
    {
        $id = input('param.id');
        $page = input('param.page');
        $whereId = $this->mipInfo['idStatus'] ? 'uuid' : 'id';
        $itemInfo = db($this->articles)->where($whereId,$id)->find();
        if (!$itemInfo) {
            if ($this->mipInfo['diyUrlStatus']) {
                $itemInfo = db($this->articles)->where('url_name',$id)->find();
                if (!$itemInfo) {
                    return $this->error($this->mipInfo['articleModelName'].'不存在','');
                }
            }
        }
        if (!$itemInfo) {
            return $this->error($this->mipInfo['articleModelName'].'不存在','');
        }
        $itemInfo = model('app\article\model\Articles')->getItemInfo($itemInfo['id']);
        //当前所属分类别名
        $this->assign('categoryUrlName',$itemInfo['categoryInfo']['url_name']);
        
        //更新当前页面浏览次数
        model('app\article\model\Articles')->updateViews($itemInfo['id'], $itemInfo['uid']);
        
        //详情页面ID
        $this->assign('itemDetailId',$itemInfo['uuid']);
        
        $this->assign('page',$page ? $page : 1);
        
        $this->assign('cid',$itemInfo['categoryInfo']['id']);
        
        //标签列表
        $itemTagsList = model('app\common\model\Tags')->getTagsListByItemType('article',$itemInfo['uuid']);
        $this->assign('tags',$itemTagsList);
        
        //标签列表字符串
        $itemInfo['tagsListString'] = '';
        if ($itemTagsList) {
             foreach ($itemTagsList as $k => $v) {
                $tempTagsName[] = $v['tags']['name'];
            }
            $tagsListString = implode(',',$tempTagsName);
            $itemInfo['tagsListString'] = $tagsListString;
        }
        
        //标题
        $mipTitle = $itemInfo['title'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
     
        //关键词
        if (@$itemInfo['keywords']) {
            $mipKeywords = $itemInfo['keywords'];
        } else {
            $mipKeywords = $itemInfo['tagsListString'];
        }
        $this->assign('mipKeywords',$mipKeywords);
        
        //文本描述
        if (@$itemInfo['description']) {
            $mipDescription = $itemInfo['description'];
        } else {
            $itemInfo['description'] = preg_replace("/(\s|\r|\n|\t|\&nbsp\;|　| |\xc2\xa0)/","",trim(strip_tags($itemInfo['content'])));
            $mipDescription = mb_substr($itemInfo['description'],0,88,'utf-8');
        }
        $this->assign('mipDescription',$mipDescription);
        
        $this->assign('itemInfo',$itemInfo);
        
        $templateName = $itemInfo['categoryInfo']['detail_template'] ? $itemInfo['categoryInfo']['detail_template'] : 'articleDetail';
        $templateName = str_replace('.html', '', $templateName);
        
        return $this->mipView('article/'.$templateName);
    }


}