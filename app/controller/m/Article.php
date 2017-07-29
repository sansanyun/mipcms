<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\m;
use app\model\Articles\Articles;
use app\model\Articles\ArticlesCategory;
use app\model\Articles\ArticlesComments;
use app\model\Tags\ItemTags;
use think\Validate;
use think\Cache;
use think\Db;
use think\Request;
use think\Loader;
use mip\Cutpage;
use mip\Mip;
use mip\Paginationm;
class Article extends Mip
{
    protected $beforeActionList = ['start'];

    public function start()
    {
        if (!$this->mipInfo['articleStatus']) {
            $this->error('文章模块未开通','');
        }
    }

    public function index()
    {
        $page = input('param.page');
        $category = input('param.category');
        $page = $page ? $page : 1;
        $currentDomain = $this->mipInfo['httpType'].$this->mipInfo['mipDomain'].$this->rewrite;
        if ($category) {
            $categoryInfo = ArticlesCategory::where('pid',0)->where('url_name',$category)->find();
            if (!$categoryInfo) {
                $this->error('分类不存在','');
            }
            $categoryUrlName = $categoryInfo['url_name'];
            $currentCid = $categoryInfo['id'];
            $count = Articles::where('cid',$currentCid)->count('id');
            
            //标题关键词描述
            $this->assign('mipTitle', $categoryInfo['seo_title'] ? $categoryInfo['seo_title'] : $categoryInfo['name'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName']);
            //面包屑导航
            if ($this->mipInfo['aritcleLevelRemove']) {
                $tempCategory = ArticlesCategory::where('pid',$categoryInfo['id'])->select();
                $tempCategoryIds = array();
                if ($tempCategory) {
                    foreach ($tempCategory as $key => $value) {
                        $tempCategoryIds[] = $value['id'];
                    }
                    $count = Articles::where('cid','in',$tempCategoryIds)->count('id');
                }
                $this->assign('crumbCategoryName',$categoryInfo['name']);
                $this->assign('crumbCategoryUrl',$currentDomain . '/' . $categoryInfo['url_name'] . '/');
            } else {
                $this->assign('crumbCategoryName',$categoryInfo['name']);
                $this->assign('crumbCategoryUrl',$currentDomain . '/' . $this->mipInfo['articleModelUrl'] . '/' . $categoryInfo['url_name'] . '/');
            }
            
            $sub = input('param.sub');
            if ($sub) {
                $subInfo = ArticlesCategory::where('url_name',$sub)->find();
                if (!$subInfo) {
                    $this->error('分类不存在','');
                }
                $tempCategory = ArticlesCategory::where('id',$subInfo['pid'])->find();
                if ($tempCategory['url_name'] != $categoryInfo['url_name']) {
                    $this->error('分类不存在','');
                }
                $currentCid = $subInfo['id'];
                $count = Articles::where('cid',$currentCid)->count('id');
                if ($this->mipInfo['superSites']) {
                    $this->assign('crumb',true);
                    $this->assign('crumbDomain',false);
                    $this->assign('crumbCategorySub',true);
                    $this->assign('crumbCategorySubName',$subInfo['name']);
                    $this->assign('crumbCategorySubUrl',$this->mipInfo['httpType'] . $tempCategory['url_name'] . '.' . $this->mipInfo['topDomain'] . $this->rewrite .'/' . $subInfo['url_name'] . '/');
                    //标题关键词描述
                    $this->assign('mipTitle', $subInfo['seo_title'] ? $subInfo['seo_title'] : $subInfo['name'] . $this->mipInfo['titleSeparator'] . $categoryInfo['name']);
                } else {
                    if ($this->mipInfo['aritcleLevelRemove']) {
                        $this->assign('crumbCategorySub',true);
                        $this->assign('crumbCategorySubName',$subInfo['name']);
                        $this->assign('crumbCategorySubUrl', $currentDomain . '/' . $categoryInfo['url_name'] . '/' . $subInfo['url_name'] . '/');
                        //标题关键词描述
                        $this->assign('mipTitle', $subInfo['seo_title'] ? $subInfo['seo_title'] : $subInfo['name'] . $this->mipInfo['titleSeparator'] . $categoryInfo['name']);

                    }
                }
                $categoryInfo = $subInfo;
            }
            //标题关键词描述
            $this->assign('mipKeywords',$categoryInfo['keywords']);
            $this->assign('mipDescription',$categoryInfo['description']);
            //面包屑导航结束
        } else {
            $categoryUrlName = null;
            $currentCid = 0;
            $categoryInfo = null;
            $count = Articles::count('id');
            //标题关键词描述
            $this->assign('mipTitle',  $this->mipInfo['articleModelName'] . $this->mipInfo['titleSeparator'] .$this->mipInfo['siteName']);
        }
        $this->assign('categoryUrlName',$categoryUrlName);
        $this->assign('categoryInfo',$categoryInfo);

        $itemList = model('app\model\Articles\Articles')->getItemList($currentCid, $page, 10, 'publish_time', 'desc');
        $this->assign('articleList',$itemList);

        $recommendListWhere['is_recommend'] = 1;
        $recommendListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'publish_time', 'desc', $recommendListWhere);
        $this->assign('recommendListByCid',$recommendListByCid);

        $hotListByCid = model('app\model\Articles\Articles')->getItemList($currentCid, 1, 5, 'views', 'desc');
        $this->assign('hotListByCid',$hotListByCid);
         
        if ($category) {
            $baseUrl = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite  . '/' ;
            $baseUrl .= $this->mipInfo['aritcleLevelRemove'] ? $categoryUrlName : $this->articleModelUrl . '/' . $categoryUrlName;
            if ($sub) {
                $baseUrl .= '/'.$sub;
            }
        } else {
            $baseUrl =  $this->mipInfo['httpType'].$this->mipInfo['mipDomain'] . $this->rewrite . '/' . $this->articleModelUrl;
        }
        $pagination_array = array(
            'base_url' =>  $baseUrl,
            'total_rows' => $count, //总共条数
            'per_page' => 10, //每页展示数量
            'page_break' => $this->mipInfo['urlPageBreak'] //分页符号
        );
        $pagination = new Paginationm($pagination_array);
        $this->assign('pagination',  $pagination->create_links());

        return $this->mipView('article/article','m', $categoryUrlName);
    }

    public function articleDetail() {
        $id = input('param.id');
        $whereId = $this->mipInfo['idStatus'] ? 'uuid' : 'id';
        $itemInfo = Articles::where($whereId,$id)->find();
        if (!$itemInfo) {
            if ($this->mipInfo['diyUrlStatus']) {
                $itemDiyInfo = Articles::where('url_name',$id)->find();
                $itemInfo = $itemDiyInfo;
            }
        }
        if (!$itemInfo) {
            return $this->error($this->articleModelName.'不存在','');
        }
         if ($this->mipInfo['urlCategory']) {
            if ($itemInfo['cid']) {
                $category = input('param.category');
                $sub = input('param.sub');
                if ($category) {
                    $tempArticlesCategory = ArticlesCategory::where('url_name',$category)->find();
                    if ($tempArticlesCategory) {
                        if ($sub) {
                            $tempSubArticlesCategory = ArticlesCategory::where('url_name',$sub)->find();
                            if ($tempSubArticlesCategory) {
                                if ($itemInfo['cid'] != $tempSubArticlesCategory['id']) {
                                    return $this->error($this->articleModelName.'不存在','');
                                }
                                if ($tempArticlesCategory['id'] != $tempSubArticlesCategory['pid']) {
                                    return $this->error($this->articleModelName.'不存在','');
                                }
                            }
                        } else {
                            if ($itemInfo['cid'] != $tempArticlesCategory['id']) {
                                return $this->error($this->articleModelName.'不存在','');
                            }
                        }
                    }
                }
            }
        }
        $itemInfo['categoryInfo'] = ArticlesCategory::get($itemInfo['cid']);
        $currentDomain = $this->mipInfo['httpType'].$this->mipInfo['mipDomain'].$this->rewrite;
        $currentCategoryUrl = $currentDomain . '/' . $this->mipInfo['articleModelUrl'] . '/';
        //面包屑导航开始
        if ($itemInfo['categoryInfo']) {
            $categoryInfo = $itemInfo['categoryInfo'];
                $this->assign('crumbCategoryName',$itemInfo['categoryInfo']['name']);
                if ($this->mipInfo['aritcleLevelRemove']) {
                    $currentCategoryUrl = $currentDomain . '/' . $itemInfo['categoryInfo']['url_name'] . '/';
                    $this->assign('crumbCategoryUrl',$currentCategoryUrl);
                } else {
                    $currentCategoryUrl = $currentDomain . '/' . $this->articleModelUrl .'/' . $itemInfo['categoryInfo']['url_name'] . '/';
                    $this->assign('crumbCategoryUrl',$currentCategoryUrl);
                }
                if ($itemInfo['categoryInfo']['pid'] != 0) {
                    $subInfo = ArticlesCategory::get($itemInfo['cid']);
                    $itemInfo['categoryInfo'] = ArticlesCategory::get($subInfo['pid']);

                    if ($this->mipInfo['aritcleLevelRemove']) {
                        $this->assign('crumbCategoryName',$itemInfo['categoryInfo']['name']);
                        $currentCategoryUrl = $currentDomain . '/' . $itemInfo['categoryInfo']['url_name'] . '/';
                        $this->assign('crumbCategoryUrl',$currentCategoryUrl);
                        $this->assign('crumbCategorySub',true);
                        $this->assign('crumbCategorySubName',$subInfo['name']);
                        $currentCategoryUrl = $currentDomain . '/' . $itemInfo['categoryInfo']['url_name'] . '/' . $subInfo['url_name'] . '/';
                        $this->assign('crumbCategorySubUrl',$currentCategoryUrl);
                        $itemInfo['categoryInfo'] = $subInfo;
                    }
                }
                //标题关键词描述
                $this->assign('mipTitle', $itemInfo['title'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName']);
        } else {
            $itemInfo['categoryInfo'] = null;
            $categoryInfo = null;
            $this->assign('mipTitle', $itemInfo['title'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName']);
        }
        $this->assign('crumbDetail',true);
        $this->assign('crumbDetailName',$itemInfo['title']);
        $this->assign('crumbDetailUrl',$itemInfo->domainUrl($itemInfo));
        //面包屑导航结束

        //更新当前页面浏览次数
        $itemInfo->updateViews($itemInfo['id'], $itemInfo['uid']);
        //查询当前内容正文
        $itemInfo['content'] =  htmlspecialchars_decode($itemInfo->getContentByArticleId($itemInfo['id'],$itemInfo['content_id'])['content']);

        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
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
                $src = $this->mipInfo['httpType'].$this->mipInfo['domain'].'/'.$imagesArray[1][$key];
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
        @preg_match_all('/<iframe.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>*<\/iframe>/', $itemInfo['content'], $iframeArray);
        if ($iframeArray) {
            foreach($iframeArray[0] as $key => $val) {
                $layout = 'layout="responsive"';
                $tempiframe = '<mip-iframe   width="320" height="200" '.$layout.' src="'.$iframeArray[1][$key].'"></mip-iframe>';
                $itemInfo['content'] =  str_replace($val,$tempiframe,$itemInfo['content']);
            }
        }
        @preg_match_all('/<embed.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $embedArray);
        if ($embedArray) {
            foreach($embedArray[0] as $key => $val) {
                $layout = '';
                $tempembed = '<mip-embed type="ad-comm" '.$layout.' src="'.$embedArray[1][$key].'"></mip-embed>';
                $itemInfo['content'] =  str_replace($val,$tempembed,$itemInfo['content']);
            }
        }
        @preg_match_all('/<video.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>*<\/video>/', $itemInfo['content'], $videoArray);
        if ($videoArray) {
            foreach($videoArray[0] as $key => $val) {
                $layout = '';
                $tempvideo = '<mip-video '.$layout.' src="'.$videoArray[1][$key].'"></mip-video>';
                $itemInfo['content'] =  str_replace($val,$tempvideo,$itemInfo['content']);
            }
        }
        //查询发布者
        $itemInfo->users;

        $this->assign('itemDetailId',$itemInfo['uuid']);
        //当前所属分类别名
        $this->assign('categoryUrlName',$categoryInfo['url_name']);
        $this->assign('itemInfo',$itemInfo);
        //内容分页
        if ($this->mipInfo['articlePages']) {
            $content = $itemInfo['content'];
            $currentPageNum = input('param.page') ? intval(input('param.page')) : 1;
            $CP = new Cutpage($content,$currentPageNum,$this->mipInfo['articlePagesNum']);
            $page = $CP->cut_str();
            $itemInfo['content'] = $page[$currentPageNum-1];
            $itemInfo['pageCode'] = $CP->pagenav($currentPageNum,$currentCategoryUrl . $id,$this->mipInfo['urlPageBreak']);
        }
        //标签关键词
        $tags = ItemTags::where('item_id',$itemInfo['uuid'])->where('item_type','article')->select();
        $tempName = null;
        if ($tags) {
            foreach ($tags as $k => $v) {
                $tags[$k]->tags;
                $tempName[] = $tags[$k]['tags']['name'];
            }
        }
        $this->assign('tags',$tags);

        //处理关键词 用于seo
        if ($tempName) {
            $this->assign('mipKeywords',implode(',',$tempName));
        }
        //处理描述description 用于seo
        $itemInfo['description']= trim(preg_replace("/ /","",str_replace("\r\n", ' ', strip_tags($itemInfo['content']))),"\r\n\t");
        $this->assign('mipDescription',mb_substr($itemInfo['description'],0,88,'utf-8'));

        //上下文
        $whereUp['cid'] = $itemInfo['cid'];
        $whereUp['publish_time'] = ['<', $itemInfo['publish_time']];
        $item_up_page = model('app\model\Articles\Articles')->getItemListNoContent(0, 1, 1, 'publish_time', 'desc', $whereUp);
        $whereDown['cid'] = $itemInfo['cid'];
        $whereDown['publish_time'] = ['>', $itemInfo['publish_time']];
        $item_down_page = model('app\model\Articles\Articles')->getItemListNoContent(0, 1, 1, 'publish_time', 'asc', $whereDown);
        $this->assign('item_up_page',$item_up_page);
        $this->assign('item_down_page',$item_down_page);

        //评论
        $comments = ArticlesComments::where('item_id',$itemInfo['id'])->order('create_time desc')->select();
        if ($comments) {
            foreach ($comments as $k => $v){
                $comments[$k]['content']= str_replace("\r\n", ' ', strip_tags($v['content']));
                $comments[$k]->users;
            }
        }
        $this->assign('comments',$comments);

        /**
         * 猜你喜欢
         * 此功能用于原创内容seo机制
         * 如果你的站点是采集内容站，数据量过大时，此功能将拖累页面打开速度，请关闭此功能 $aboutLoveList = null;
         * 或者请联系MIPCMS内容管理系统官方人员进行对相关功能的数据进行固化
         * */
        $aboutLoveList =  model('app\model\Articles\Articles')->getLoveListByTags($tags,8, $itemInfo['uuid']);
        $this->assign('aboutLoveList',$aboutLoveList);

        //查询发布者发布的其余内容
        if ($itemInfo['uid']) {
            $whereUid['uid'] = $itemInfo['uid'];
            $whereUid['uuid'] = ['<>', $itemInfo['uuid']];
            $newsListByUid = model('app\model\Articles\Articles')->getItemList(0, 1, 5, 'publish_time', 'desc', $whereUid);
        } else {
            $newsListByUid = null;
        }
        $this->assign('newsListByUid',$newsListByUid);

        $tags ? $tagId = $tags[0]['tags']['id'] : $tagId = null;
        $aboutListByThis = null;
        if ($tagId) {
            foreach ($tags as $k => $v) {
                $tempTagsIds[] = $v['tags']['id'];
            }
            $itemTagsList = ItemTags::where('tags_id','in',implode(',',$tempTagsIds))->where('item_id', '<>', $itemInfo['uuid'])->order('item_add_time desc')->where('item_type','article')->limit(5)->select();
            if ($itemTagsList) {
                foreach ($itemTagsList as $k => $v) {
                    $itemTagsListIds[] = $v['item_id'];
                }
                $whereByThis['uuid'] = ['in', implode(',', $itemTagsListIds)];
                $aboutListByThis = model('app\model\Articles\Articles')->getItemList(0, 1, 5, 'publish_time', 'desc', $whereByThis);
            }
        }
        $this->assign('aboutListByThis',$aboutListByThis);

        //查询当前模块所有的分类
        $categoryList = ArticlesCategory::order('sort desc')->select();
        if ($categoryList) {
            foreach ($categoryList as $key => $val) {
                if (!Validate::regex($categoryList[$key]['url_name'],'\d+') AND $categoryList[$key]['url_name']) {
                    $categoryList[$key]['url_name'] = $categoryList[$key]['url_name'];
                } else {
                    $categoryList[$key]['url_name'] = 'cid_'.$categoryList[$key]['id'];
                }
            }
        }
        $this->assign('categoryList',$categoryList);

        return $this->mipView('article/articleDetail','m', $categoryInfo['url_name']);
    }


}