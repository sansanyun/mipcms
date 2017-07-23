<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\pc;
use app\model\Articles\Articles;
use app\model\Articles\ArticlesCategory;
use app\model\Asks\Asks;
use think\Request;
use think\Response;
use think\Loader;
use mip\Pagination;
use mip\Mip;
class Index extends Mip
{
    public function index()
    {
        $this->assign('mipTitle',$this->mipInfo['siteName'].$this->mipInfo['indexTitle']);
        $category = input('param.category');
        if ($this->mipInfo['articleStatus']) {
            if ($this->mipInfo['superSites'] && $category) {
                $categoryInfo = ArticlesCategory::where('url_name',$category)->find();
                $currentCid = $categoryInfo['id'];
                $page = 1;

                $categoryList = ArticlesCategory::where('pid',$currentCid)->order('sort desc')->select();
                if ($categoryList) {
                    foreach ($categoryList as $key => $val) {
                        $val['articles'] = $itemList = model('app\model\Articles\Articles')->getItemList($val['id'], $page, 10, 'publish_time', 'desc');
                    }
                }
                $this->assign('categoryList',$categoryList);

                $itemList = model('app\model\Articles\Articles')->getItemList($currentCid, $page, 10, 'publish_time', 'desc');
                $this->assign('articleList',$itemList);

                $recommendListWhere['is_recommend'] = 1;
                $recommendListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'publish_time', 'desc', $recommendListWhere);
                $this->assign('recommendList',$recommendListByCid);

                $hotListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'views', 'desc');
                $this->assign('hotListByCid',$hotListByCid);

                 //标题关键词描述
            $this->assign('mipTitle', $categoryInfo['seo_title'] ? $categoryInfo['seo_title'] : $categoryInfo['name'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName']);

                return $this->mipView('index/index','pc',$categoryInfo['url_name']);
            } else {
                //当前无分类
                $currentCid = 0;
                $page = 1;

//              $categoryList = ArticlesCategory::where('pid',0)->order('sort desc')->select();
                if ($this->itemCategoryList) {
                    foreach ($this->itemCategoryList as $key => $val) {
                        $this->itemCategoryList[$key]['articles'] = $itemList = model('app\model\Articles\Articles')->getItemList($val['id'], $page, 10, 'publish_time', 'desc');
                    }
                }
                $this->assign('categoryList',$this->itemCategoryList);

                $itemList = model('app\model\Articles\Articles')->getItemList($currentCid, $page, 10, 'publish_time', 'desc');
                $this->assign('articleList',$itemList);

                $recommendListWhere['is_recommend'] = 1;
                $recommendListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'publish_time', 'desc', $recommendListWhere);
                $this->assign('recommendList',$recommendListByCid);

                $hotListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'views', 'desc');
                $this->assign('hotListByCid',$hotListByCid);

                return $this->mipView('index/index','pc');
            }

        } else {
            echo Loader::action('Ask/index');
        }

    }

    function sitemap() {
        $count = Articles::count('id');
        $pageNum = ceil($count / 1000)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->domain . '/xml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        $sitemap .= '</sitemapindex>';
        return Response::create($sitemap)->contentType('text/xml');;
    }

    function xml() {
        $page = input('param.page');
        $page = $page ? $page : 1;
        $itemList = model('app\model\Articles\Articles')->getItemListNoContent(0, $page, 1000, 'publish_time', 'desc');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<urlset>';
        foreach($itemList as $k => $v) {
            $xml .= '<url>';
            $xml .= '<loc>' . $v["url"] . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d", $v["publish_time"]) . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';
        return Response::create($xml)->contentType('text/xml');;
    }
}
