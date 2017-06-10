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
    public function index() {
        
        $categoryList = ArticlesCategory::order('sort desc')->select();
         
        foreach ($categoryList as $key => $val) {
            $val['articles'] = Articles::where('cid',$val['id'])->order('publish_time desc')->limit(10)->select();
            $val['articles'] = model('api/Articles')->filterM( $val['articles'], $this->mipInfo['idStatus'], $this->domain, $this->public);
            foreach( $val['articles'] as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            if(!Validate::regex($categoryList[$key]['url_name'],'\d+') AND $categoryList[$key]['url_name']){
                $categoryList[$key]['url_name'] = $categoryList[$key]['url_name'];
            }else{
                $categoryList[$key]['url_name'] = 'cid_'.$categoryList[$key]['id'];
            }
        }
        $this->assign('categoryList',$categoryList);
        
        $recommendList = Articles::limit(4)->where('is_recommend',1)->order('publish_time desc')->select();
        $recommendList = model('api/Articles')->filterM($recommendList, $this->mipInfo['idStatus'], $this->domain, $this->public);
        if ($recommendList) {
            foreach($recommendList as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
        }
        $this->assign('recommendList',$recommendList);
        
        
        $newsArticleList = Articles::order('publish_time desc')->limit(10)->select();
        $newsArticleList = model('api/Articles')->filterM($newsArticleList, $this->mipInfo['idStatus'], $this->domain, $this->public);
        if ($newsArticleList) {
            foreach($newsArticleList as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
        }
        $this->assign('newsArticleList',$newsArticleList);
        return $this->mipView('m/index/index');
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
