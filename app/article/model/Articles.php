<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\model;
use think\Cache;
use mip\Paginationm;
use think\Db;
use think\Controller;

class Articles extends Controller
{
    public $item;
    public $itemCategory;
    public function _initialize()
    {
        parent::_initialize();
        $this->articles = 'articles';
        $this->item = 'articles';
        $this->articlesContent = 'articlesContent';
        $this->itemCategory = 'articlesCategory';
        $this->itemTags = 'itemTags';
        $this->tags = 'tags';
        $this->mipInfo = config('mipInfo');
        $this->domain = config('domain');
        $this->dataId = config('dataId');
    }
    
    public function getItemInfo($id = null,$uuid = null)
    {
        if (!$id && !$uuid) {
               return false;
        }
        if ($id) {
            $itemInfo = db($this->item)->where('id',$id)->find();
        }
        if ($uuid) {
            $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        }
        if ($itemInfo) {
            $itemInfo = $this->getImgList($itemInfo);
            $itemInfo['userInfo'] = null;
            $itemInfo['content'] = $this->getContentByItemContentId($itemInfo['content_id']);
            $itemInfo['mipContent'] = $this->getContentFilterByArticleInfo($itemInfo);
            $itemInfo['categoryInfo'] = $this->getCategoryInfo($itemInfo['cid']);
            $itemInfo['url'] = $this->getUrlByItemInfo($itemInfo);
            return $itemInfo;
        } else {
            return false;
        }
    }
    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null, $uuids = null,$notUuids = null,$tagIds = null,$tagNames = null,$ids = null,$itemId = null,$type = null)
    {
        $itemList = null;
        if ($itemId && $type == 'about') {
            $itemTagsList = db($this->itemTags)->where('item_id',$itemId)->order('item_add_time',$order)->select();
            if ($itemTagsList) {
                foreach ($itemTagsList as $k => $v) {
                    $itemTagsListIds[] = $v['tags_id'];
                }
                $tagIds = implode(',', $itemTagsListIds);
                $tempItemTagsList = db($this->itemTags)->where('tags_id','in',$tagIds)->order('item_add_time',$order)->select();
                if ($tempItemTagsList) {
                    foreach ($tempItemTagsList as $key => $val) {
                        $tempItemTagsListIds[] = $val['item_id'];
                    }
                    $uuids = implode(',', $tempItemTagsListIds);
                }
                
            }
        }
        if (empty($tagIds) && empty($tagNames)) {
            if (empty($keywords)) {
                $keywordsWhere = null;
            } else {
                $keywords = explode(',',$keywords);
                foreach ($keywords as $key => $val) {
                    if ($val) {
                        $sq[] = "%".$val."%";
                    }
                }
                $keywordsWhere['title']  = ['like',$sq,'OR'];
            }
            if (empty($uuids)) {
                $uuidsWhere = null;
            } else {
                $uuids = explode(',',$uuids);
                $uuidsWhere['uuid']  = ['in',$uuids];
            }
            if (empty($notUuids)) {
                $notUuidsWhere = null;
            } else {
                $notUuids = explode(',',$notUuids);
                $notUuidsWhere['uuid']  = ['not in',$notUuids];
            }
            if (empty($ids)) {
                $idsWhere = null;
            } else {
                $ids = explode(',',$ids);
                $idsWhere['id']  = ['in',$ids];
            }
            if ($cid == '' || $cid == null) {
                    $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where($idsWhere)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
                if ($itemCategoryList) {
                    foreach ($itemCategoryList as $key => $value) {
                           $cids[] = $value['id'];
                    }
                }
                if ($itemCategoryList) {
                    $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
                } else {
                    $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                }
                
            }
        } else {
            return $this->getItemListbyTags($cid, $page, $perPage, $orderBy, $order, $where,$keywords, $uuids,$notUuids,$tagIds,$tagNames,$ids,$itemId,$type);                
        }
        
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k] = $this->getImgList($v);
                $itemList[$k]['tempId'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['userInfo'] = db('Users')->where('uid',$v['uid'])->find();
                $itemList[$k]['categoryInfo'] = $this->getCategoryInfo($v['cid']);
                $itemList[$k]['url'] = $this->getUrlByItemInfo($v);
            }
        } else {
            $itemList = null;
        }
        return $itemList;
    }

    public function getItemListbyTags($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null, $uuids = null,$notUuids = null,$tagIds = null,$tagNames = null,$ids = null)
    {
        if ($tagNames) {
            $tagNames = explode(',',$tagNames);
            foreach ($tagNames as $val) {
                $tagInfo = db($this->tags)->where('name',$val)->find();
                if ($tagInfo) {
                    $tempTagIds[] = $tagInfo['id'];
                }
            }
            $tagIdsWhere['tags_id']  = ['in',$tempTagIds];
        }
        if ($tagIds) {
            $tagIds = explode(',',$tagIds);
            $tagIdsWhere['tags_id']  = ['in',$tagIds];
        }
        
        if (!empty($keywords) || !empty($uuids) || !empty($ids) || !empty($notUuids)) {
            $itemTagsList = db($this->itemTags)->where($tagIdsWhere)->order('item_add_time',$order)->where('item_type','article')->select();
            if ($itemTagsList) {
                foreach ($itemTagsList as $k => $v) {
                    $itemTagsListIds[] = $v['item_id'];
                }
                $itemTagsListIds = implode(',', $itemTagsListIds);
                return $this->getItemList($cid, $page, $perPage, $orderBy, $order, $where, $keywords, $itemTagsListIds, $notUuids, $ids);
            }
        } else {
            $itemTagsList = db($this->itemTags)->where($tagIdsWhere)->order('item_add_time',$order)->where('item_type','article')->page($page,$perPage)->select();
            if ($itemTagsList) {
                foreach ($itemTagsList as $k => $v) {
                    $itemTagsListIds[] = $v['item_id'];
                }
                $itemTagsListIds = implode(',', $itemTagsListIds);
                return $this->getItemList($cid, 1, $perPage, $orderBy, $order, $where, $keywords, $itemTagsListIds, $notUuids,$ids);
            }
        }
    }
    
    public function getItemPushList($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $domain = null)
    {
        if ($cid == '' || $cid == null) {
            $itemList = db($this->articles)->page($page,$perPage)->order($orderBy,$order)->select();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $key => $value) {
                       $cids[] = $value['id'];
                }
            }
            if ($itemCategoryList) {
                $itemList = db($this->articles)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemList = db($this->articles)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
            }
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['categoryInfo'] = $this->getCategoryInfo($v['cid']);
                $itemList[$k]['url'] = $this->getUrlByItemInfo($v,$domain);
            }
        } else {
            $itemList = null;
        }
        return $itemList;
    }
    
    public function getPage($itemId, $limit = 1, $type, $itemType, $page = 1,$prePage = 10)
    {
        if (!$itemId) {
            return false;
        }
        if ($type == 'detail') {
            if ($itemType == 'upPage') {
                $itemList = db($this->item)->where('id','<',$itemId)->limit($limit)->order('id','DESC')->select();
            }
            if ($itemType == 'downPage') {
                $itemList = db($this->item)->where('id','>',$itemId)->limit($limit)->order('id','ASC')->select();
            }
            if ($itemList) {
                foreach ($itemList as $k => $v) {
                    $itemList[$k]['categoryInfo'] = $this->getCategoryInfo($v['cid']);
                }
                
                foreach ($itemList as $k => $v) {
                    $itemList[$k]['url'] = $this->getUrlByItemInfo($v); 
                }
                
                return $itemList;
            } else {
                return false;
            }
        }

        if ($type == 'category') {
            $count = $this->getCount($itemId);
            $pageNum = ceil($count / $prePage);
            if ($itemType == 'upPage') {
                if ($page == 1) {
                    return false;
                } else {
                    $page = $page - 1;
                    $url = $this->getCategoryPageUrl($itemId,$page);
                    $tempArray = [];
                    $tempArray[0]['url'] = $url;
                    $tempArray[0]['num'] = $page;
                    return $tempArray;
                }
            }
            if ($itemType == 'downPage') {
                if ($pageNum <= $page) {
                    return false;
                } else {
                    $page = $page + 1;
                    $url = $this->getCategoryPageUrl($itemId,$page);
                    $tempArray = [];
                    $tempArray[0]['url'] = $url;
                    $tempArray[0]['num'] = $page;
                    return $tempArray;
                }
            }
        }
    }

    public function getPaginationm($cid = null, $per_page = 10, $category = null, $sub = null,$where = null, $keywords = null, $uuids = null,$notUuids = null,$tagIds = null,$tagNames = null)
    {
        $count = $this->getCount($cid,$where, $keywords, $uuids,$notUuids,$tagIds,$tagNames);
        
        if ($cid) {
            $categoryInfo = $this->getCategoryInfo($cid);
            $baseUrl = $categoryInfo['url'];
        }
        $tagInfo = null;
        if ($tagIds) {
            $tempTagIds = explode(',', $tagIds);
            if (count($tempTagIds) == 1) {
                $tagInfo = db($this->tags)->where('id',$tagIds)->find();
            } else {
                return false;
            }
        }
        if ($tagNames) {
            $tempTagNames = explode(',', $tagNames);
            if (count($tempTagNames) == 1) {
                $tagInfo = db($this->tags)->where('name',$tagNames)->find();
            } else {
                return false;
            }
        }
        
        if ($tagInfo) {
            if ($tagInfo['url_name']) {
                $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['url_name'] . '/';
            } else {
                $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['id'] . '/';
            }
        }
        $pagination_array = array(
            'base_url' => $baseUrl,
            'total_rows' => $count,
            'per_page' => $per_page,
            'page_break' => $this->mipInfo['urlPageBreak']
        );
        $pagination = new Paginationm($pagination_array);
        return $pagination->create_links();
    }
    
    public function getCount($cid = null,$where = null, $keywords = null, $uuids = null,$notUuids = null,$tagIds = null,$tagNames = null)
    {
        $count = 0;
        if (empty($tagIds) && empty($tagNames)) {
            if (empty($keywords)) {
                $keywordsWhere = null;
            } else {
                $keywords = explode(',',$keywords);
                foreach ($keywords as $key => $val) {
                    if ($val) {
                        $sq[] = "%".$val."%";
                    }
                }
                $keywordsWhere['title']  = ['like',$sq,'OR'];
            }
            if (empty($uuids)) {
                $uuidsWhere = null;
            } else {
                $uuids = explode(',',$uuids);
                $uuidsWhere['uuid']  = ['in',$uuids];
            }
            if (empty($notUuids)) {
                $notUuidsWhere = null;
            } else {
                $notUuids = explode(',',$notUuids);
                $notUuidsWhere['uuid']  = ['not in',$notUuids];
            }
            if ($cid == '' || $cid == null) {
                $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->count();
            } else {
                $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
                if ($itemCategoryList) {
                    foreach ($itemCategoryList as $key => $value) {
                           $cids[] = $value['id'];
                    }
                }
                if ($itemCategoryList) {
                    $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->count();
                } else {
                    $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('cid',$cid)->count();
                }
            }
        } else {
            if ($tagNames) {
                $tagNames = explode(',',$tagNames);
                foreach ($tagNames as $val) {
                    $tagInfo = db($this->tags)->where('name',$val)->find();
                    if ($tagInfo) {
                        $tempTagIds[] = $tagInfo['id'];
                    }
                }
                $tagIdsWhere['tags_id']  = ['in',$tempTagIds];
            }
            if ($tagIds) {
                $tagIds = explode(',',$tagIds);
                $tagIdsWhere['tags_id']  = ['in',$tagIds];
            }
            $count = db($this->itemTags)->where($tagIdsWhere)->where('item_type','article')->count();
        }
        return $count;
    }
    
    
    public function getCategoryInfo($cid)
    {
        if (!$cid) {
            return false;
        }
        $itemCategoryInfo = db('ArticlesCategory')->where('id',$cid)->find();
        if ($itemCategoryInfo) {
            if ($itemCategoryInfo['pid'] == 0) {
                $urlName = $itemCategoryInfo['url_name'];
            } else {
                $tempCategoryInfo = $this->getCategoryInfo($itemCategoryInfo['pid']);
                $itemCategoryInfo['parent'] = $tempCategoryInfo;
                if ($tempCategoryInfo) {
                    $urlName = $tempCategoryInfo['url_name'] . '/' . $itemCategoryInfo['url_name'];
                }
            }
        }
        $categoryUrl = $itemCategoryInfo['category_url'];
        if ($categoryUrl) {
            $categoryUrl = str_replace('<url_name>',$urlName,$categoryUrl);
            $categoryUrl = str_replace('<id>',$itemCategoryInfo['id'],$categoryUrl);
        } else {
            $categoryUrl = '/article/' . $urlName . '/';
        }
        $itemCategoryInfo['url'] = $this->domain . $categoryUrl;
        $itemCategoryInfo['rule'] = $categoryUrl;
        $categoryPageUrl = $itemCategoryInfo['category_page_url'];
        if ($categoryPageUrl) {
            $categoryPageUrl = str_replace('<category_url>', $categoryUrl,$categoryPageUrl);
            $categoryPageUrl = str_replace('<url_name>',$itemCategoryInfo['url_name'],$categoryPageUrl);
            $categoryPageUrl = str_replace('<id>',$itemCategoryInfo['id'],$categoryPageUrl);
        } else {
            $categoryPageUrl = $categoryUrl . 'index_<page>.html';
        }
        $itemCategoryInfo['pageTempRule'] = $categoryPageUrl;
        if (strpos($categoryPageUrl,'.html')) {
            $categoryPageUrl = str_replace('.html','',$categoryPageUrl);
        }
        $itemCategoryInfo['pageRule'] = $categoryPageUrl;
        $detailUrl = $itemCategoryInfo['detail_url'];
        if ($detailUrl) {
            if (strpos($categoryUrl,'.html')) {
                $categoryUrl = str_replace('.html','/',$categoryUrl);
            }
            $detailUrl = str_replace('<category_url>', $categoryUrl,$detailUrl);
        } else {
            $detailUrl = $categoryUrl . '<id>.html';
        }
        $itemCategoryInfo['detailRule'] = $detailUrl;
        $detailUrl = str_replace('.html','',$detailUrl);
        $tempDetailUrl = substr($detailUrl, 0, 1) == '/' ? substr($detailUrl, 1) : $detailUrl;
        $itemCategoryInfo['detail__url__'] = str_replace('/','\/',str_replace('<id>','[a-zA-Z0-9_-]+$',$tempDetailUrl));
        return $itemCategoryInfo;
    }
    public function getCategory($pid = 0, $orderBy = 'sort', $order = 'asc', $limit = null, $where = null,$ids = null,$type = null)
    {
        $itemCategoryList = null;
        if ($type == 'menu') {
            $itemCategoryList = db($this->itemCategory)->where('pid',$pid)->where('status','<>',2)->where($where)->limit($limit)->order($orderBy,$order)->select();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$pid)->where('status','<>',2)->where('is_page',0)->where($where)->limit($limit)->order($orderBy,$order)->select();
        }
        if($itemCategoryList) {
            foreach ($itemCategoryList as $key => $val) {
                $itemCategoryList[$key] = $this->getCategoryInfo($val['id']);
            }
            foreach ($itemCategoryList as $key => $val) {
                $itemCategoryList[$key]['value'] = $val['id'];
                $itemCategoryList[$key]['label'] = $val['name'];
                $itemCategoryList[$key]['sub'] = db($this->itemCategory)->where('pid',$val['id'])->select();
                if ($itemCategoryList[$key]['sub']) {
                    foreach ($itemCategoryList[$key]['sub'] as $k => $v) {
                    	   $itemCategoryList[$key]['sub'][$k] = $this->getCategoryInfo($v['id']);
                           
                        $itemCategoryList[$key]['sub'][$k]['value'] = $v['id'];
                        $itemCategoryList[$key]['sub'][$k]['label'] = $v['name'];
                    }
                } else {
                    $itemCategoryList[$key]['sub'] = array();
                }
                $itemCategoryList[$key]['children'] = $itemCategoryList[$key]['sub'];
            }
        }
        return $itemCategoryList;
    }

    public function getAllCategory()
    {
        $categoryList = null;
        $itemCategoryList = db($this->itemCategory)->order('sort asc')->select();
        if($itemCategoryList) {
            foreach ($itemCategoryList as $key => $val) {
                $itemCategoryList[$key] = $this->getCategoryInfo($val['id']);
            }
        }
        return $itemCategoryList;
    }
    
    public function getCategoryPageUrl($cid,$page)
    {
        $categoryInfo = $this->getCategoryInfo($cid);
        if ($categoryInfo) {
            $pageUrl = str_replace('<page>', $page,$categoryInfo['pageTempRule']);
            $res = $this->domain . $pageUrl;
            return $res;
        } else {
            return false;
        }
    }
    public function getUrlByItemInfo($item,$domain = null)
    {
        $tempId = $this->mipInfo['idStatus'] ? $item['uuid'] : $item['id'];
        $tempId = $this->mipInfo['diyUrlStatus'] ? $item['url_name'] ? $item['url_name'] : $tempId : $tempId;
        $domain = $domain ? $domain : $this->domain;
        if (!isset($item['categoryInfo'])) {
            $item['categoryInfo'] = $this->getCategoryInfo($item['cid']);
        }
        if ($item['categoryInfo']) {
            $detailUrl = str_replace('<id>',$tempId,$item['categoryInfo']['detailRule']);
            $res = $domain . $detailUrl;
        } else {
            $res = $domain . '/article/' . $tempId . '.html';
        }
        return $res;
    }
    
    
    public function getImgList($item)
    {
        if (!$item) {
            return false;
        }
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        if (!isset($itemInfo['content']) || !$itemInfo['content']) {
            $item['content'] = $this->getContentByItemContentId($item['content_id']);
        }
        if (preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $item['content'], $imgs)) {
            $item['imgCount'] = count($imgs[1]);
            foreach ($imgs[1] as $key => $value) {
               if (@preg_match($patern,$value)) {
                   $imgs[1][$key] = $value;
                } else {
                   $imgs[1][$key] = $this->domainStatic . $value;
                }
            }
            $item['imgList'] = $imgs[1];
            $item['firstImg'] = $imgs[1][0];
        } else {
            $item['firstImg'] = null;
            $item['imgCount'] = 0;
            $item['imgList'] = null;
        }
        return $item;
    }
    
    public function getCrumb($cid = null, $ulClass = 'mipcms-crumb', $liClass = '',$isHome = 1,$separator = '')
    {
        if (!$cid) {
    	       return false;
        }
        $categoryInfo = $this->getCategoryInfo($cid);
        if ($categoryInfo) {
            if ($categoryInfo['pid'] == 0) {
                $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
                $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. $this->mipInfo['siteName'] .'">' . $this->mipInfo['siteName'] . '</a>'.$separator.'</li>' : '';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['url'] .'" title="'. $categoryInfo['name'] .'">';
                $html .= $categoryInfo['name'];
                $html .= '</a>';
                $html .= '</li>';
                $html .= '</ul>';
                return $html;
            } else {
                $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
                $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. $this->mipInfo['siteName'] .'">' . $this->mipInfo['siteName'] . '</a>'.$separator.'</li>' : '';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['parent']['url'] .'" title="'. $categoryInfo['parent']['name'] .'">';
                $html .= $categoryInfo['parent']['name'];
                $html .= '</a>'.$separator;
                $html .= '</li>';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['url'] .'" title="'. $categoryInfo['name'] .'">';
                $html .= $categoryInfo['name'];
                $html .= '</a>';
                $html .= '</li>';
                $html .= '</ul>';
                return $html;
            }
        } else {
            return false;
        }
    }
    
     
    public function updateViews($id, $uid)
    {
        $tempCache = Cache::get('updateViewsArticle' . md5(session_id()) . intval($id));
        if ($tempCache) {
            return false;
        }
        Cache::set('updateViewsArticle' . md5(session_id()) . intval($id), time(), 60);
        db($this->articles)->where('id',$id)->update([
            'views' => ['exp','views+1'],
        ]);
        return true;
    }

    public function getContentByItemContentId($content_id)
    {
        if (!$content_id) {
            return false;
        }
        return htmlspecialchars_decode(db($this->articlesContent)->where('id',$content_id)->find()['content']);
    }
    
    
    public function getContentFilterByArticleInfo($itemInfo)
    {
        if (!$itemInfo) {
            return false;
        }
        if (!isset($itemInfo['content']) || !$itemInfo['content']) {
            $itemContentInfo = db($this->articlesContent)->where('id',$itemInfo['content_id'])->find();
        }
        $content = model('app\common\model\Common')->getContentFilterByContent($itemContentInfo['content']);
        return $content;
    }

    public function itemPushUrl($createInfo)
    {
        
        $domainSitesList = db('domainSites')->select();
        if ($this->mipInfo['superSites'] && $domainSitesList) {
        foreach ($domainSitesList as $key => $val) {
                $domainSettingsInfo = db('domainSettings')->where('id',$val['id'])->find();
                $urls = $this->getUrlByItemInfo($createInfo,$val['http_type'].$val['domain']);
                $urls = explode(',',$urls);
                if ($domainSettingsInfo['mipAutoStatus'] && $domainSettingsInfo['mipApi']) {
                    $result = pushData($domainSettingsInfo['mipApi'],$urls);
                }
                
                if ($domainSettingsInfo['ampAutoStatus'] && $domainSettingsInfo['ampApi']) {
                    $result = pushData($domainSettingsInfo['ampApi'],$urls);
                }
                
                if ($domainSettingsInfo['xiongZhangStatus'] && $domainSettingsInfo['xiongZhangNewAutoStatus'] && $domainSettingsInfo['xiongZhangNewApi']) {
                    $result = pushData($domainSettingsInfo['xiongZhangNewApi'],$urls);
                }
                
                if ($domainSettingsInfo['yuanChuangAutoStatus'] && $domainSettingsInfo['yuanChuangApi']) {
                    $result = pushData($domainSettingsInfo['yuanChuangApi'],$urls);
                }
                
                if ($domainSettingsInfo['linkAutoStatus'] && $domainSettingsInfo['linkApi']) {
                    $result = pushData($domainSettingsInfo['linkApi'],$urls);
                }
            }
        } else {
            $urls = $this->getUrlByItemInfo($createInfo);
            $urls = explode(',',$urls);
            if (is_array($urls)) {
                if ($this->mipInfo['baiduYuanChuangStatus']) {
                    $api = $this->mipInfo['baiduYuanChuangUrl'];
                    $result = pushData($api,$urls);
                }
                if ($this->mipInfo['baiduTimePcStatus']) {
                    $api = $this->mipInfo['baiduTimePcUrl'];
                    $result = pushData($api,$urls);
                }
                if ($this->mipInfo['guanfanghaoStatus']) {
                    if ($this->mipInfo['guanfanghaoStatusPost']) {
                        $api = $this->mipInfo['guanfanghaoRealtimeUrl'];
                        $result = pushData($api,$urls);
                    }
                }
                if ($this->mipInfo['mipPostStatus']) {
                    $api = $this->mipInfo['mipApiAddress'];
                    $result = pushData($api,$urls);
                }
            }
        }
        return $result;

    }

}