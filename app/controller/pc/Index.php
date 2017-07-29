<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\pc;
use app\model\Articles\Articles;
use app\model\Articles\ArticlesCategory;
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
        $currentCid = 0;
        $page = 1;

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
    
     function baiduSitemapPc() {
        $count = Articles::count('id');
        $pageNum = ceil($count / 1000)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->mipInfo['httpType'].$this->mipInfo['domain'] . '/pcXml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        $sitemap .= '</sitemapindex>';
        return Response::create($sitemap)->contentType('text/xml');;
    }
    function pcXml() {
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
