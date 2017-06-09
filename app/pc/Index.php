<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use app\api\model\Articles;
use app\api\model\ArticlesCategory;
use app\api\model\Asks;
use think\Request;
use think\Response;
use mip\Pagination;
use mip\Mip;
class Index extends Mip 
{
    public function index() {
        
        if ($this->mipInfo['systemType'] == 'CMS') {
          
            $categoryList = ArticlesCategory::order('sort desc')->select();
            foreach ($categoryList as $key => $val) {
                $val->articles();
            }
            $this->assign('categoryList',$categoryList);
            $hot_list_by_cid = Articles::field('id,uuid,publish_time,title,views')->order('views desc')->limit(5)->select();
            foreach($hot_list_by_cid as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('hot_list_by_cid',$hot_list_by_cid);
            
            $recommendList = Articles::limit(4)->where('is_recommend',1)->order('publish_time desc')->select();
            $recommendList = model('api/Articles')->filter($recommendList, $this->mipInfo['idStatus'], $this->domain, $this->public);
           	foreach($recommendList as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('recommendList',$recommendList);
            
            $articleMaxNum = Articles::count('id');
            $articleMinNum = 1;
            for ($i = 0; $i <5; $i++) {
                $tempNum[] = rand($articleMinNum,$articleMaxNum);
            }
            $rand_list = Articles::where('id','in', implode(',', $tempNum))->select();
                
            foreach($rand_list as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('rand_list',$rand_list);
            
            return $this->mipView('pc/index/index');
            
        } else {
            
            $articleList = Articles::page(1,10)->order('publish_time desc')->select();
            if ($articleList) {
               $articleList = model('api/Articles')->filter($articleList, $this->mipInfo['idStatus'], $this->domain, $this->public);
            } else {
                $articleList = null;
            }
            $this->assign('articleList',$articleList);
            
            $hot_list_by_cid = Articles::order('views desc')->limit(5)->select();
            foreach($hot_list_by_cid as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('hot_list_by_cid',$hot_list_by_cid);
            
            $recommendListByCid = Articles::where('is_recommend',1)->order('publish_time desc')->limit(5)->select();
            foreach($recommendListByCid as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('recommendListByCid',$recommendListByCid);
            
            $recommendList = Articles::limit(4)->where('is_recommend',1)->order('publish_time desc')->select();
            if ($recommendList) {
               $recommendList = model('api/Articles')->filter($recommendList, $this->mipInfo['idStatus'], $this->domain, $this->public);
            } else {
                $recommendList = null;
            }
            $this->assign('recommendList',$recommendList);
            
            return $this->mipView('pc/index/index');
        }
    }
    
    
    function sitemap() {
       
        if ($this->mipInfo['systemType'] == 'ASK') {
            $list = Asks::field('id,uuid,publish_time')->order('publish_time desc')->limit(5000)->select();
        } else {
            $list = Articles::field('id,uuid,publish_time')->order('publish_time desc')->limit(5000)->select();
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
