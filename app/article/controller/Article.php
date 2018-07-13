<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\controller;
use mip\Mip;
class Article extends Mip
{
    public function index()
    {
        $page = input('param.page');
        $id = input('param.id');
        $page = $page ? $page : 1;
        if ($id) {
            $categoryInfo = model('app\article\model\Articles')->getCategoryInfo($id);
            if (!$categoryInfo) {
                $this->error('分类不存在','');
            }
            $currentCid = $categoryInfo['id'];
        } else {
            $currentCid = 0;
            $categoryInfo['id'] = 0;
            $categoryInfo['name'] = $this->mipInfo['articleModelName'];
            $categoryInfo['seo_title'] = '';
            $categoryInfo['keywords'] = $this->mipInfo['articleModelName'];
            $categoryInfo['description'] = $this->mipInfo['articleModelName'];
            $categoryInfo['url_name'] = $this->mipInfo['articleModelUrl'];
            $categoryInfo['url'] = $this->domain . '/' . $this->mipInfo['articleModelUrl'] . '/';
        }
        //自定义参数
        $categoryInfo['cid'] = $categoryInfo['id'] ? $categoryInfo['id'] : '';
        $categoryInfo['page'] = $page ? $page : '';
        //当前分类别名
        $this->assign('cid',$categoryInfo['id']);
        $this->assign('page',$page);
        $this->assign('categoryUrlName',$categoryInfo['url_name']);
        
        $categoryInfo['content'] = htmlspecialchars_decode($categoryInfo['content']);
        $categoryInfo['mipContent'] = model('app\common\model\Common')->getContentFilterByContent($categoryInfo['content']);
        //分页数量
        $pageText = $page == 1 ? "" : $this->mipInfo['titleSeparator'] . "第" . $page . "页";
        
        //标题关键词描述
        $mipTitle = $categoryInfo['seo_title'] ? $categoryInfo['seo_title'] : $categoryInfo['name'] . $pageText . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        $this->assign('mipKeywords',$categoryInfo['keywords']);
        $this->assign('mipDescription',$categoryInfo['description']);
      
        $this->assign('categoryInfo',$categoryInfo);
          
        $templateName = $categoryInfo['template'] ? $categoryInfo['template'] : 'article';
        $templateName = str_replace('.html', '', $templateName);
        
        return $this->mipView('article/'.$templateName);
    }

}