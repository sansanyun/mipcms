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
        $category = input('param.category');
        $sub = input('param.sub');
        $page = $page ? $page : 1;
        if ($sub) {
            $categoryInfo = db($this->articlesCategory)->where('url_name',$sub)->find();
            if (!$categoryInfo) {
                $this->error('分类不存在','');
            }
            $currentCid = $categoryInfo['id'];
        } else {
            if ($category) {
                $categoryInfo = db($this->articlesCategory)->where('url_name',$category)->find();
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
        }
        
        //自定义参数
        $categoryInfo['cid'] = $categoryInfo['id'] ? $categoryInfo['id'] : '';
        $categoryInfo['page'] = $page ? $page : '';
        $categoryInfo['category'] = $category ? $category : '';
        $categoryInfo['sub'] = $sub ? $sub : '';
        
        //分页数量
        $pageText = $page == 1 ? "" : $this->mipInfo['titleSeparator'] . "第" . $page . "页";
        
        //标题关键词描述
        $mipTitle = $categoryInfo['seo_title'] ? $categoryInfo['seo_title'] : $categoryInfo['name'] . $pageText . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        $this->assign('mipKeywords',$categoryInfo['keywords']);
        $this->assign('mipDescription',$categoryInfo['description']);
        
        //面包屑导航
        $currentCategoryInfo = model('app\article\model\Articles')->getCategoryParentInfoByCid($currentCid);
        $this->assign('crumbCategoryName',$currentCategoryInfo['name']);
        $this->assign('crumbCategoryUrl',$currentCategoryInfo['url']);
        if ($currentCid == 0) {
            $this->assign('crumbCategoryName',$categoryInfo['name']);
            $this->assign('crumbCategoryUrl',$categoryInfo['url']);
        }
        $currentCategorySubInfo = model('app\article\model\Articles')->getCategorySubInfoByCid($currentCid);
        $this->assign('crumbCategorySub',$currentCategorySubInfo);
        $this->assign('crumbCategorySubName',$currentCategorySubInfo['name']);
        $this->assign('crumbCategorySubUrl',$currentCategorySubInfo['url']);
        
        //当前分类别名
        $this->assign('categoryUrlName',$categoryInfo['url_name']);
        
        $this->assign('categoryInfo',$categoryInfo);
          
        $templateName = $categoryInfo['template'] ? $categoryInfo['template'] : 'article';
        return $this->mipView('article/'.$templateName);
    }

}