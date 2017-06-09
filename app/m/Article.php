<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\m;
use app\api\model\Articles;
use app\api\model\ArticlesCategory;
use app\api\model\ArticlesComments;
use think\Request;
use app\api\model\ItemTags;
use think\Validate;
use mip\Paginationm;
use mip\Mip;
class Article extends Mip
{
    public function index()
    {
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
            
            $list = Articles::order('publish_time desc')->where($whereCategory)->page($page,10)->select();
            
            $count = Articles::where($whereCategory)->count('id');
            $hot_list_by_cid = Articles::where('cid',$categoryInfo->id)->order('views desc')->limit(5)->select();
            foreach($hot_list_by_cid as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('hot_list_by_cid',$hot_list_by_cid);
        } else {
            $categoryUrlName = null;
            $categoryInfo = null;
            
            $list = Articles::page($page,10)->order('publish_time desc')->select();
            
            $count = Articles::count('id');
            $hot_list_by_cid = Articles::order('views desc')->limit(5)->select();
            foreach($hot_list_by_cid as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('hot_list_by_cid',$hot_list_by_cid);
        }
        if ($list) { 
            
            $list = model('api/Articles')->filterM($list, $this->mipInfo['idStatus'], $this->domain, $this->public);
            foreach($list as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
        } else {
            $list=null;
        }
        $this->assign('categoryUrlName',$categoryUrlName); //当前URL名称
        $this->assign('categoryInfo',$categoryInfo); //用于SEO
        $this->assign('list',$list);
        $news_list_by_uid = Articles::where('is_recommend',1)->order('publish_time desc')->limit(5)->select();
        foreach($news_list_by_uid as $k => $v) {
            $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }
        $this->assign('recommendListByCid',$news_list_by_uid);
        
        require ALL_PATH . 'mip_config.php';
        if ($isModel) {
            $pagination_array= array(
                'base_url' => $this->domain.'/'.$this->articleModelUrl.'/'.$categoryUrlName,
                'total_rows' => $count, //总共条数
                'per_page' => 10 //每页展示数量
            );
        } else {
            $pagination_array= array(
                'base_url' => $this->domain.'/'.$categoryUrlName,
                'total_rows' => $count, //总共条数
                'per_page' => 10 //每页展示数量
            );
        }
        $pagination = new Paginationm($pagination_array);
        $this->assign('pagination',  $pagination->create_links());
        
        return $this->mipView('m/article/article');
    }
    
    public function articleDetail() {
      $id = input('param.id');
        $whereId = $this->mipInfo['idStatus'] ? 'uuid' : 'id';
        $itemInfo = Articles::where($whereId,$id)->find();
        if(!$itemInfo){
            return $this->error($this->articleModelName.'不存在','/');
        }
        $itemInfo->updateViews($itemInfo['id'], $itemInfo['uid']);
        $itemInfo['content'] =  htmlspecialchars_decode($itemInfo->getContentByArticleId($itemInfo['id'],$itemInfo['content_id'])['content']);
        
            preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
            $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
            '|'. 
            '([0-9a-z_!~*\'()-]+\.)*'. 
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
            '[a-z]{2,6})'.   
            '(:[0-9]{1,4})?'.  
            '((\/\?)|'.  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
            foreach($imagesArray[0] as $key => $val) {
                @preg_match('/alt=".+?"/',$val,$tempAlt);
                @$alt = explode('=',$tempAlt[0]);
                @$alt = explode('"',$alt[1]);
                if (count($alt) == 1) {
                    $alt = $alt[0];
                } 
                if (count($alt) == 2) {
                    $alt = $alt[1] ;
                }
                if (count($alt) == 3) {
                    $alt = $alt[1] ;
                }
                if (@preg_match($patern,$imagesArray[1][$key])) {
                    $src = $imagesArray[1][$key];
                } else {
                    $src = $this->domain.'/'.$this->public.$imagesArray[1][$key];
                }
                $layout = 'layout="container"';
                $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                $itemInfo['content'] =  str_replace($val,$tempImg,$itemInfo['content']);
            }
            $itemInfo['content'] =  preg_replace("/style=.+?['|\"]/i",'', bbc2html($itemInfo['content']));
            @preg_match_all('/<a[^>]*>[^>]+a>/',$itemInfo['content'],$tempLink);
            foreach($tempLink[0] as $k => $v) {
                if(strpos($v,"href")) {
                    @preg_match('/href\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^"\'>\s]+))/',$v,$hrefRes);
                    $matches = @preg_match($patern,$hrefRes[1]);
                    if (!$matches) {
                        $itemInfo['content'] = str_replace($v,'',$itemInfo['content']); 
                    }
                } else {
                    $itemInfo['content'] = str_replace($v,'',$itemInfo['content']); 
                }
            }
        
        $itemInfo->users;
        $itemInfo['message_description']= trim(preg_replace("/ /","",str_replace("\r\n", ' ', strip_tags($itemInfo['content']))),"\r\n\t");
        $itemInfo['categoryInfo'] = ArticlesCategory::get($itemInfo['cid']);
        if ($itemInfo['categoryInfo']) {
            if (!Validate::regex($itemInfo['categoryInfo']['url_name'],'\d+') AND $itemInfo['categoryInfo']['url_name']) {
                $itemInfo['categoryInfo']['url_name']=$itemInfo['categoryInfo']['url_name'];
            } else {
                $itemInfo['categoryInfo']['url_name']='cid_'.$itemInfo['categoryInfo']['id'];
            }
        }
        $this->assign('itemInfo',$itemInfo);
        
        $tags= ItemTags::where('item_id',$itemInfo['id'])->where('item_type','article')->select();
        if($tags){
            foreach ($tags as $k=>$v){
                $tags[$k]->tags;
            }
        }
        $this->assign('tags',$tags);

        $item_up_page = Articles::where('publish_time','<',$itemInfo['publish_time'])->order('publish_time desc')->limit(1)->select();
        $item_down_page = Articles::where('publish_time','>',$itemInfo['publish_time'])->limit(1)->order('publish_time asc')->select();
        foreach($item_up_page as $k => $v) {
            $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }     
        foreach($item_down_page as $k => $v) {
            $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }
        $this->assign('item_up_page',$item_up_page);
        $this->assign('item_down_page',$item_down_page);

        //评论区域
        $comments= ArticlesComments::where('item_id',$itemInfo['id'])->order('create_time desc')->select();
        if ($comments) {
            foreach ($comments as $k=>$v){
                $comments[$k]['content']= str_replace("\r\n", ' ', strip_tags(bbc2html($v['content'])));
                $comments[$k]->users;
            }
        }
        $this->assign('comments',$comments);

        if ($tags) {
            $tagName = $tags[0]['tags']['name'];
        } else {
            $tagName = null;
        }
        
        if ($tagName) {
            $sq = "%".$tagName."%";
            $tagWhere['title']  = ['like',$sq];
            $aboutLoveList = Articles::where($tagWhere)->limit(8)->select();
            $aboutLoveList = model('api/Articles')->filterM($aboutLoveList, $this->mipInfo['idStatus'], $this->domain, $this->public);
        } else {
            $aboutLoveList = null;
        }
        if ($aboutLoveList) {
            foreach ($aboutLoveList as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
        }
        $this->assign('aboutLoveList',$aboutLoveList);

        //获取发布者发布的最新数据

         if ($this->mipInfo["systemType"] == 'CMS') {
            //随机推荐
            $articleMaxNum = Articles::count('id');
                $articleMinNum = 1;
                for ($i = 0; $i < 5; $i++) {
                    $tempNum[] = rand($articleMinNum,$articleMaxNum);
                }
            $rand_list = Articles::where('publish_time','<',time())->where('id','in', implode(',', $tempNum))->select();
            foreach ($rand_list as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('rand_list',$rand_list);
            
            $hot_list_by_cid = Articles::order('views desc')->limit(5)->select();
            foreach($hot_list_by_cid as $k => $v) {
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('hot_list_by_cid',$hot_list_by_cid);
        } else {
            //获取发布者发布的最新数据
            $newsListByUid = Articles::where('uid',$itemInfo['uid'])->order('publish_time desc')->limit(5)->select();
            foreach($newsListByUid as $k => $v) {
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
            }
            $this->assign('news_list_by_uid',$newsListByUid);
        }
        
        $categoryList = ArticlesCategory::order('sort desc')->select();
        if ($categoryList) {
            foreach ($categoryList as $key => $val) {
                if (!Validate::regex($categoryList[$key]['url_name'],'\d+') AND $categoryList[$key]['url_name']) {
                    $categoryList[$key]['url_name']=$categoryList[$key]['url_name'];
                } else {
                    $categoryList[$key]['url_name']='cid_'.$categoryList[$key]['id'];
                }
            }
        }
        $this->assign('categoryList',$categoryList);

        return $this->mipView('m/article/articleDetail');
    }

}
