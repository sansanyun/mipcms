<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use app\api\model\Articles;
use app\api\model\ArticlesCategory;
use app\api\model\Asks;
use think\Request;
use mip\Pagination;
use mip\Mip;
class Index extends Mip 
{
    public function index() {
        if ($this->mipInfo['systemType'] == 'Blog') {
            $page = input('param.page');
            $category = input('param.category');
            if (!$page) {
                $page=1;
            }
            if ($category) {
                $categoryInfo = ArticlesCategory::get($category);
                if ($categoryInfo) {
                    $whereCategory['cid'] = $categoryInfo->id;
                    $categoryUrlName = 'cid_'.$categoryInfo->id;
                } else if ($categoryInfo = ArticlesCategory::where('url_name',$category)->find()) {
                    $whereCategory['cid'] = $categoryInfo->id;
                    $categoryUrlName = $category;
                } else {
                    $categoryUrlName = null;
                }
                
                $list = Articles::order('publish_time desc')->where('publish_time','<',time())->where($whereCategory)->page($page,10)->select();
                
                $count = Articles::where($whereCategory)->count('id');
                $hot_list_by_cid = Articles::where('cid',$categoryInfo->id)->order('views desc')->limit(5)->select();
                foreach($hot_list_by_cid as $k => $v) {
                        $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                }
                $this->assign('hot_list_by_cid',$hot_list_by_cid);
            } else {
                $categoryUrlName = null;
                $categoryInfo = null;
                
                $list = Articles::page($page,10)->order('publish_time desc')->where('publish_time','<',time())->select();
                
                $count = Articles::count('id');
                $hot_list_by_cid = Articles::order('views desc')->limit(5)->where('publish_time','<',time())->select();
                foreach($hot_list_by_cid as $k => $v) {
                        $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                }
                $this->assign('hot_list_by_cid',$hot_list_by_cid);
            }
            if ($list) {
                 $patern = '/^http[s]?:\/\/'.
                '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
                '|'. 
                '([0-9a-z_!~*\'()-]+\.)*'. 
                '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
                '[a-z]{2,6})'.   
                '(:[0-9]{1,4})?'.  
                '((\/\?)|'.  
                '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
                foreach ($list as $k=>$v){
                    $list[$k]->users;
                    $v['content'] = htmlspecialchars_decode($v['content']);
                    if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                        if (@preg_match($patern,$imgs[1][0])) {
                            $list[$k]['firstImg'] = $imgs[1][0];
                        } else {
                            $list[$k]['firstImg'] = $this->domain.$imgs[1][0];
                        }
                        
                    } else {
                        $list[$k]['firstImg'] = null;
                    }
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid']:$v['id'];
                    $v['content'] = strip_tags(htmlspecialchars_decode($v['content']));
                }
            } else {
                $list=null;
            }
            $this->assign('categoryUrlName',$categoryUrlName); //当前URL名称
            $this->assign('categoryInfo',$categoryInfo); //用于SEO
            $this->assign('list',$list);
            $news_list_by_uid = Articles::where('is_recommend',1)->order('publish_time desc')->where('publish_time','<',time())->limit(5)->select();
            foreach($news_list_by_uid as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('recommendListByCid',$news_list_by_uid);
            
            $pagination_array= array(
                'base_url' => $this->domain.'/'.$this->articleModelUrl.'/'.$categoryUrlName,
                'total_rows' => $count, //总共条数
                'per_page' => 10 //每页展示数量
            );
            $pagination = new Pagination($pagination_array);
            $this->assign('pagination',  $pagination->create_links());
             
            return $this->mipView('pc/article/article');
        }
        if ($this->mipInfo['systemType'] == 'CMS') {
          
            $categoryList = ArticlesCategory::order('sort desc')->select();
            foreach ($categoryList as $key => $val) {
                $val->articles();
            }
            $this->assign('categoryList',$categoryList);
            $hot_list_by_cid = Articles::where('publish_time','<',time())->field('id,uuid,publish_time,title,views')->order('views desc')->limit(5)->select();
            foreach($hot_list_by_cid as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('hot_list_by_cid',$hot_list_by_cid);
            
            $recommendList = Articles::where('publish_time','<',time())->limit(4)->where('is_recommend',1)->order('publish_time desc')->select();
            $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
            '|'. 
            '([0-9a-z_!~*\'()-]+\.)*'. 
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
            '[a-z]{2,6})'.   
            '(:[0-9]{1,4})?'.  
            '((\/\?)|'.  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
            foreach ($recommendList as $k=>$v){
                $v['content'] = htmlspecialchars_decode($v['content']);
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                    if (@preg_match($patern,$imgs[1][0])) {
                        $recommendList[$k]['firstImg'] = $imgs[1][0];
                    } else {
                        $recommendList[$k]['firstImg'] = $this->domain.$imgs[1][0];
                    }
                } else {
                    $recommendList[$k]['firstImg'] = null;
                }
            };
            $this->assign('recommendList',$recommendList);
            
            $articleMaxNum = Articles::count('id');
            $articleMinNum = 1;
            for ($i = 0; $i <5; $i++) {
                $tempNum[] = rand($articleMinNum,$articleMaxNum);
            }
            $rand_list = Articles::where('publish_time','<',time())->where('id','in', implode(',', $tempNum))->select();
                
            foreach($rand_list as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('rand_list',$rand_list);
            
            return $this->mipView('pc/index/index');
        }
        
    }
        
    
    function sitemap() {
        Header('Content-type: text/xml');
        if ($this->mipInfo['systemType'] == 'ASK') {
            $list = Asks::where('publish_time','<',time())->field('id,uuid,publish_time')->order('publish_time desc')->limit(5000)->select();
        } else {
            $list = Articles::where('publish_time','<',time())->field('id,uuid,publish_time')->order('publish_time desc')->limit(5000)->select();
        }
        foreach($list as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }
        $this->assign('list',$list);
        return $this->fetch($this->mipInfo['template'].'/'.'pc/index/sitemap'); 
        //???
//       $sitemap = '<?xml version="1.0" encoding="UTF-8"><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
//          foreach($list as $k=>$v){
//              $sitemap .= "<url> "."<loc>".$this->domain."/".$this->articleModelUrl."/".$v['id'].".html</loc> "."<priority>0.6</priority> <lastmod>".date('Y-m-d',$v['publish_time'])."</lastmod> <changefreq>always</changefreq> </url> ";
//          }
//          $sitemap .= '</urlset>';
//          $file = fopen("sitemap.xml","w");
//          fwrite($file,$sitemap);
//          fclose($file);
    }
    
}
