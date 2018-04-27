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
        if ($tempArticlesCategory) {
            $itemInfo['categoryInfo'] = $tempArticlesCategory;
            $categoryInfo = $tempArticlesCategory;
        }
        
        //当前所属分类别名
        $this->assign('categoryUrlName',$categoryInfo['url_name']);
        
        //更新当前页面浏览次数
        model('app\article\model\Articles')->updateViews($itemInfo['id'], $itemInfo['uid']);
        
        //查询内容中图片列表
        $itemInfo = model('app\article\model\Articles')->getImgList($itemInfo);
        
        //查询当前内容正文
        $itemInfo['mipContent'] = model('app\article\model\Articles')->getContentFilterByArticleId($itemInfo['id'],$itemInfo['content_id']);
        
        //内链锚文本
        if (@$itemInfo['link_tags']) {
            $itemInfo = model('app\article\model\Articles')->getTagsLink($itemInfo);
        }

        $isAllTagsLink = false;
        if ($isAllTagsLink) {
            $itemInfo = model('app\article\model\Articles')->getAllTagsLink($itemInfo);
        }

        //详情页面ID
        $this->assign('itemDetailId',$itemInfo['uuid']);
        
        //内容分页
        if ($this->mipInfo['articlePages']) {
            $currentPageNum = input('param.page') ? intval(input('param.page')) : 1;
            $CP = new Cutpagem($itemInfo['content'],$currentPageNum,$this->mipInfo['articlePagesNum']);
            $page = $CP->cut_str();
            $itemInfo['content'] = $page[$currentPageNum-1];
            $currentUrlNotHtml = str_replace('.html','',$itemInfo['categoryInfo']['detailRule']);
            $itemInfo['pageCode'] = $CP->pagenav($currentPageNum,$currentUrlNotHtml,$this->mipInfo['urlPageBreak'],$this->siteUrl);
        }

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
        
        return $this->mipView('article/articleDetail');
    }


}