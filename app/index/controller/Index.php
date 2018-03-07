<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\index\controller;
use think\Request;
use think\Response;
use mip\Mip;
class Index extends Mip
{
    public function index()
    {
        
        if ($this->domainSettingsInfo && $this->mipInfo['topDomain'] && $this->domainSettingsInfo['diySiteName']) {
            $this->assign('mipTitle',$this->domainSettingsInfo['diySiteName']);
        } else {
            $this->assign('mipTitle',$this->mipInfo['siteName'].$this->mipInfo['indexTitle']);
        }
      
        return $this->mipView('index/index');
    }

   function sitemap() {
        $count = model('app\article\model\Articles')->getCount(0);
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
        $page = input('param.id');
        $page = $page ? $page : 1;
        $itemList = model('app\article\model\Articles')->getItemListNoContent(0, $page, 1000, 'publish_time', 'desc');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<urlset>';
        if ($page == 1) {
            $xml .= '<url>';
            $xml .= '<loc>' . $this->domain . '/' . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>1.0</priority>';
            $xml .= '</url>';
            if ($this->itemCategoryList = model('app\article\model\Articles')->getCategory()) {
                foreach($this->itemCategoryList as $k => $v) {
                    $xml .= '<url>';
                    $xml .= '<loc>' . $v["url"] . '</loc>';
                    $xml .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
                    $xml .= '<changefreq>daily</changefreq>';
                    $xml .= '<priority>0.9</priority>';
                    $xml .= '</url>';
                }
            }
            $tagsList = db($this->tags)->select();
            if ($tagsList) {
                foreach ($tagsList as $key => $val) {
                    if ($val['url_name']) {
                        $tagsList[$key]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] .'/' . $val['url_name'] . '/';
                    } else {
                        $tagsList[$key]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] .'/' . $val['id'] . '/';
                    }
                    $tagsList[$key]['time'] = $val['add_time'] ? date("Y-m-d", $val["add_time"]) : date("Y-m-d");
                }
                foreach ($tagsList as $key => $val) {
                    $xml .= '<url>';
                    $xml .= '<loc>' . $val["url"] . '</loc>';
                    $xml .= '<lastmod>' . $tagsList[$key]['time'] . '</lastmod>';
                    $xml .= '<changefreq>daily</changefreq>';
                    $xml .= '<priority>0.9</priority>';
                    $xml .= '</url>';
                }
            }
        }
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
        $count = model('app\article\model\Articles')->getCount(0);
        $pageNum = ceil($count / 1000)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->domain . '/pcXml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        $sitemap .= '</sitemapindex>';
        return Response::create($sitemap)->contentType('text/xml');;
    }
    function pcXml() {
        $page = input('param.id');
        $page = $page ? $page : 1;
        $itemList = model('app\article\model\Articles')->getItemListNoContent(0, $page, 1000, 'publish_time', 'desc');

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
