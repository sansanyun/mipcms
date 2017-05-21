<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\m;
use app\api\model\Articles;
use app\api\model\ArticlesCategory;
use think\Request;
use think\Validate;
use mip\Paginationm;
use mip\Mip;
class Index extends Mip
{
    public function index()
    {
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
                
                $count = Articles::where($whereCategory)->where('publish_time','<',time())->count('id');
                $hot_list_by_cid = Articles::where('cid',$categoryInfo->id)->order('views desc')->limit(5)->select();
                foreach($hot_list_by_cid as $k => $v) {
                        $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                }
                $this->assign('hot_list_by_cid',$hot_list_by_cid);
            } else {
                $categoryUrlName = null;
                $categoryInfo = null;
                
                $list = Articles::page($page,10)->order('publish_time desc')->where('publish_time','<',time())->select();
                
                $count = Articles::where('publish_time','<',time())->count('id');
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
                foreach ($list as $k => $v){
                    $list[$k]->users;
                    $v['content'] = htmlspecialchars_decode($v['content']);
                    if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
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
            $pagination = new Paginationm($pagination_array);
            $this->assign('pagination',  $pagination->create_links());
            
            return $this->mipView('m/article/article');
        }
        if ($this->mipInfo['systemType'] == 'CMS') {
            $categoryList = ArticlesCategory::order('sort desc')->select();
            $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
            '|'. 
            '([0-9a-z_!~*\'()-]+\.)*'. 
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
            '[a-z]{2,6})'.   
            '(:[0-9]{1,4})?'.  
            '((\/\?)|'.  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
            foreach ($categoryList as $key => $val) {
                $val['articles'] = Articles::where('publish_time','<',time())->where('cid',$val['id'])->order('publish_time desc')->limit(10)->select();
                foreach ($val['articles']  as $k => $v) {
                    $v['content'] = htmlspecialchars_decode($v['content']);
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                    if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                        if (@preg_match($patern,$imgs[1][0])) {
                            $v['firstImg'] = $imgs[1][0];
                        } else {
                            $v['firstImg'] = $this->domain.$imgs[1][0];
                        }
                    } else {
                        $v['firstImg'] = null;
                    }
                }
                if(!Validate::regex($categoryList[$key]['url_name'],'\d+') AND $categoryList[$key]['url_name']){
                    $categoryList[$key]['url_name'] = $categoryList[$key]['url_name'];
                }else{
                    $categoryList[$key]['url_name'] = 'cid_'.$categoryList[$key]['id'];
                }
            }
            $this->assign('categoryList',$categoryList);
            
            $recommendList = Articles::where('publish_time','<',time())->limit(4)->where('is_recommend',1)->order('publish_time desc')->select();
            foreach ($recommendList as $k => $v){
                $v['content'] = htmlspecialchars_decode($v['content']);
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                    if (@preg_match($patern,$imgs[1][0])) {
                        $v['firstImg'] = $imgs[1][0];
                    } else {
                        $v['firstImg'] = $this->domain.$imgs[1][0];
                    }
                } else {
                    $v['firstImg'] = null;
                }
            };
            $this->assign('recommendList',$recommendList);
            
            
            $newsArticleList = Articles::order('publish_time desc')->where('publish_time','<',time())->limit(10)->select();
            foreach ($newsArticleList as $k => $v){
                $v['content'] = htmlspecialchars_decode($v['content']);
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                    $v['imgCount'] = count($imgs[1]);
                    $v['imgList'] = $imgs[1];
                    if (@preg_match($patern,$imgs[1][0])) {
                        $v['firstImg'] = $imgs[1][0];
                    } else {
                        $v['firstImg'] = $this->domain.$imgs[1][0];
                    }
                } else {
                    $v['firstImg'] = null;
                    $v['imgCount'] = 0;
                    $v['imgList'] = null;
                }
            };
            
            $this->assign('newsArticleList',$newsArticleList);
            return $this->mipView('m/index/index');
        }
    }
    
     function sitemap() {
       
        
        if ($this->mipInfo['systemType'] == 'ASK') {
            $list = Asks::where('publish_time','<',time())->field('id,uuid,publish_time')->order('publish_time desc')->limit(5000)->select();
        } else {
            $list = Articles::where('publish_time','<',time())->field('id,uuid,publish_time')->order('publish_time desc')->limit(5000)->select();
        }
        foreach($list as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }
        $this->assign('list',$list);
        
       return   $this->display('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
<url>
<loc>{$domain}</loc>
<priority>1.00</priority>
<lastmod><?php echo date("Y-m-d")?></lastmod>
<changefreq>always</changefreq>
</url>
<?php foreach($list as $key => $val){ ?>
<url>
<?php if ($mipInfo["systemType"] == "ASK") {?>
<loc>{$domain}/{$askModelUrl}/{$val["id"]}.html</loc>
<?php } else { ?>
<loc>{$domain}/{$articleModelUrl}/{$val["id"]}.html</loc>
<?php }?>
<priority>0.5</priority>
<lastmod><?php echo date("Y-m-d", $val["publish_time"]); ?></lastmod>
<changefreq>always</changefreq>
</url>
<?php } ?>
</urlset>');
        
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
