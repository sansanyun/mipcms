<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\m;
use app\model\Articles\Articles;
use app\model\Articles\ArticlesCategory;
use think\Request;
use think\Validate;
use mip\Paginationm;
use think\Response;
use mip\Mip;
class Index extends Mip
{
    public function index()
    {

       $this->assign('mipTitle',$this->mipInfo['siteName'].$this->mipInfo['indexTitle']);
    
        //当前无分类
        $currentCid = 0;
        $page = 1;

        // $categoryList = ArticlesCategory::where('pid',0)->order('sort desc')->select();
        if ($this->itemCategoryList) {
            foreach ($this->itemCategoryList as $key => $val) {
                $this->itemCategoryList[$key]['articles'] = model('app\model\Articles\Articles')->getItemList($val['id'], $page, 10, 'publish_time', 'desc');
            }
        }
        $this->assign('categoryList',$this->itemCategoryList);

        $itemList = model('app\model\Articles\Articles')->getItemList($currentCid, $page, 10, 'publish_time', 'desc');
        $this->assign('articleList',$itemList);

        $recommendListWhere['is_recommend'] = 1;
        $recommendListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'publish_time', 'desc', $recommendListWhere);
        $this->assign('recommendList',$recommendListByCid);

        $hotListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 4, 'views', 'desc');
        $this->assign('hotListByCid',$hotListByCid);
        return $this->mipView('index/index','m');
    }



    function sitemap() {
        $count = Articles::count('id');
        $pageNum = ceil($count / 1000)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->mipInfo['httpType']. $this->mipInfo['mipDomain'] . $this->rewrite . '/xml/' . $i . '.xml' . '</loc>';
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
            $xml .= '<loc>' . $v["mipUrl"] . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d", $v["publish_time"]) . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';
        return Response::create($xml)->contentType('text/xml');;
    }


    function baiduSitemapMobile() {
        $count = Articles::count('id');
        $pageNum = ceil($count / 1000)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->mipInfo['httpType']. $this->mipInfo['mipDomain'] . $this->rewrite . '/mobileXml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        $sitemap .= '</sitemapindex>';
        return Response::create($sitemap)->contentType('text/xml');;
    }

    function mobileXml() {
        $page = input('param.page');
        $page = $page ? $page : 1;
        $itemList = model('app\model\Articles\Articles')->getItemListNoContent(0, $page, 1000, 'publish_time', 'desc');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<urlset>';
        foreach($itemList as $k => $v) {
            $xml .= '<url>';
            $xml .= '<loc>' . $v["mipUrl"] . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d", $v["publish_time"]) . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '<data>';
            $xml .= '<display>';
            $xml .= '<title>' . $v['title'] . '</title>';
            $xml .= '</display>';
            $xml .= '</data>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';
        return Response::create($xml)->contentType('text/xml');;
    }

}
