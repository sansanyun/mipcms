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
        $pagination = new Paginationm($pagination_array);
        $this->assign('pagination',  $pagination->create_links());
        
        return $this->mipView('m/article/article');
    }
    
    public function articleDetail() {
      $id = input('param.id');
        $whereId = $this->mipInfo['idStatus'] ? 'uuid' : 'id';
        $itemInfo = Articles::where('publish_time','<',time())->where($whereId,$id)->find();
        if(!$itemInfo){
            return $this->error($this->articleModelName.'不存在','/');
        }
        $itemInfo->updateViews($itemInfo['id'], $itemInfo['uid']);
        $itemInfo['content'] = htmlspecialchars_decode($itemInfo['content']);
        
        if (!$this->userId) {
            preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
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
                @preg_match('/width="\d+/',$val,$tempWidth);
                @$width = explode('="',$tempWidth[0]);
                @preg_match('/height="\d+/',$val,$tempHeight);
                @$height = explode('="',$tempHeight[0]);
                @preg_match('/alt=".+?"/',$val,$tempAlt);
                @$alt = explode('=',$tempAlt[0]);
                @$alt = explode('"',$alt[1]);
                if ($this->isMobile) {
                    if (@$width[1]) {
                        $tempRes = $width[1]/$height[1];
                        $width = 'width="290"';
                        $layout = 'layout="fixed"';
                    } else {
                        $width = '';
                        $layout = 'layout="container"';
                    }
                    if (@$height[1]) {
                        $tempHeightRes = 290/$tempRes;
                        $height = 'height="'.$tempHeightRes.'"';
                    } else {
                        $height = '';
                    }
                } else {
                    if (@$width[1]) {
                        $tempRes = $width[1]/$height[1];
                        if ($width[1] < 728) {
                            $width = 'width="'.$width[1].'"';
                        } else {
                             $width = 'width="728"';
                        }
                        $layout = 'layout="fixed"';
                    } else {
                        $width = '450';
                        $layout = 'layout="fixed"';
                    }
                    if (@$height[1]) {
                        if ($width[1] < 728) {
                            $height = 'height="'.$height[1].'"';
                        } else {
                            $tempHeightRes = 728/$tempRes;
                            $height = 'height="'.$tempHeightRes.'"';
                        }
                    } else {
                        $height = 150;
                    }
                }
                
                if (@preg_match($patern,$imagesArray[1][$key])) {
                    $src = $imagesArray[1][$key];
                } else {
                    $src = $this->domain.$imagesArray[1][$key];
                }
                 
                $tempImg = '<mip-img '.$layout.' alt="'.$alt[1].'" '.$width.' '.$height.'  src="'.$src.'" popup></mip-img>';
                $itemInfo['content'] =  str_replace($val,$tempImg,$itemInfo['content']);
            }
            $itemInfo['content'] =  preg_replace("/style=.+?['|\"]/i",'', $itemInfo['content']);
            @preg_match_all('/<a[^>]+>[^>]+a>/',$itemInfo['content'],$tempLink);
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
        }
        
        $itemInfo->users;
        $itemInfo['message_description']= trim(preg_replace("/\[attach\](.*)\[\/attach\]/","",str_replace("\r\n", ' ', strip_tags(bbc2html($itemInfo['content'])))),"\r\n\t");
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

        $item_up_page = Articles::where('publish_time','<',$itemInfo['publish_time'])->where('publish_time','<',time())->order('publish_time desc')->limit(1)->select();
        $item_down_page = Articles::where('publish_time','>',$itemInfo['publish_time'])->where('publish_time','<',time())->limit(1)->order('publish_time asc')->select();
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

        //随机数据
        $articleMaxNum = Articles::count('id');
            $articleMinNum = 1;
            for ($i = 0; $i <8; $i++) {
                $tempNum[] = rand($articleMinNum,$articleMaxNum);
            }
        $rand_list = Articles::where('publish_time','<',time())->where('id','in', implode(',', $tempNum))->select();
        $patern = '/^http[s]?:\/\/'.
        '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
        '|'. 
        '([0-9a-z_!~*\'()-]+\.)*'. 
        '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
        '[a-z]{2,6})'.   
        '(:[0-9]{1,4})?'.  
        '((\/\?)|'.  
        '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
        foreach ($rand_list as $k => $v) {
            $v['content'] = htmlspecialchars_decode($v['content']);
            if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                if (@preg_match($patern,$imgs[1][0])) {
                    $rand_list[$k]['firstImg'] = $imgs[1][0];
                } else {
                    $rand_list[$k]['firstImg'] = $this->domain.$imgs[1][0];
                }
                
            } else {
                $rand_list[$k]['firstImg'] = null;
            }
        }  
        foreach ($rand_list as $k => $v) {
            $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }
        $this->assign('rand_list',$rand_list);


        //获取发布者发布的最新数据
        $news_list_by_uid = Articles::where('uid',$itemInfo['uid'])->order('publish_time desc')->where('publish_time','<',time())->limit(5)->select();
        foreach($news_list_by_uid as $k => $v) {
            $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
        }
        $this->assign('news_list_by_uid',$news_list_by_uid);

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
