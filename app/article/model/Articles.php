<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\model;
use think\Cache;
use mip\Paginationm;
use mip\Init;

class Articles extends Init
{
    public $item;
    public $itemCategory;
    public function _initialize()
    {
        parent::_initialize();
        $this->item = $this->articles;
        $this->itemCategory = $this->articlesCategory;
        
    }
    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null, $uuids = null,$notUuids = null,$tagIds = null,$tagNames = null,$ids = null)
    {
        $itemList = null;
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
                if ($this->mipInfo['topDomain']) {
                    $itemList = db($this->item)->where('site_id',$this->dataId)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where($idsWhere)->page($page,$perPage)->order($orderBy,$order)->select();
                } else {
                    $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where($idsWhere)->page($page,$perPage)->order($orderBy,$order)->select();
                }
            } else {
                if ($cid == 0) {
                    if ($this->mipInfo['topDomain']) {
                        $itemList = db($this->item)->where($where)->where('site_id',$this->dataId)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where($idsWhere)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                    } else {
                        $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where($idsWhere)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                    }
                } else {
                    $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
                    if ($itemCategoryList) {
                        foreach ($itemCategoryList as $key => $value) {
                               $cids[] = $value['id'];
                        }
                    }
                    if ($itemCategoryList) {
                        if ($this->mipInfo['topDomain']) {
                            $itemList = db($this->item)->where($where)->where('site_id',$this->dataId)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
                        } else {
                            $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
                        }
    
                    } else {
                        if ($this->mipInfo['topDomain']) {
                            $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('cid',$cid)->where('site_id',$this->dataId)->page($page,$perPage)->order($orderBy,$order)->select();
                        } else {
                            $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                        }
                    }
                }
                
            }
        } else {
            return $this->getItemListbyTags($cid, $page, $perPage, $orderBy, $order, $where,$keywords, $uuids,$notUuids,$tagIds,$tagNames,$ids);                
        }
        
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k] = $this->getImgList($v);
                $itemList[$k]['users'] = db('Users')->where('uid',$v['uid'])->find();
                $itemList[$k]['categoryInfo'] = db($this->itemCategory)->where('id',$v['cid'])->find();
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
        if ($this->mipInfo['topDomain']) {
            $itemTagsList = db($this->itemTags)->where($tagIdsWhere)->order('item_add_time',$order)->where('item_type','article')->select();
            if ($itemTagsList) {
                foreach ($itemTagsList as $k => $v) {
                    $itemTagsListIds[] = $v['item_id'];
                }
                $itemTagsListIds = implode(',', $itemTagsListIds);
                return $this->getItemList($cid, $page, $perPage, $orderBy, $order, $where, $keywords, $itemTagsListIds, $notUuids, $ids);
            }
        } else {
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
    }
    
    
    
    
    public function getItemPushList($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $domain)
    {
        if ($cid == '' || $cid == null) {
            if ($this->mipInfo['topDomain']) {
                $itemList = db($this->articles)->where('site_id',$this->dataId)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemList = db($this->articles)->page($page,$perPage)->order($orderBy,$order)->select();
            }
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $key => $value) {
                       $cids[] = $value['id'];
                }
            }
            if ($itemCategoryList) {
                if ($this->mipInfo['topDomain']) {
                    $itemList = db($this->articles)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->where('site_id',$this->dataId)->page($page,$perPage)->order($orderBy,$order)->select();
                } else {
                    $itemList = db($this->articles)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
                }
            } else {
                if ($this->mipInfo['topDomain']) {
                    $itemList = db($this->articles)->where('cid',$cid)->where('site_id',$this->dataId)->page($page,$perPage)->order($orderBy,$order)->select();
                } else {
                    $itemList = db($this->articles)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                }
            }
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['itemCategory'] = db($this->itemCategory)->where('id',$v['cid'])->find();
                $itemList[$k]['articlesLastCategory'] = array();
                if ($itemList[$k]['itemCategory']) {
                    $itemList[$k]['articlesLastCategory'] = db($this->itemCategory)->where('id',$itemList[$k]['itemCategory']['pid'])->find();
                }
                $itemList[$k]['url'] = $this->getUrlByItemInfo($v,$domain);
            }
        } else {
            $itemList = null;
        }
        return $itemList;
    }
    
    public function getPaginationm($cid = null, $per_page = 10, $category = null, $sub = null,$where = null, $keywords = null, $uuids = null,$notUuids = null,$tagIds = null,$tagNames = null)
    {
        $count = $this->getCount($cid,$where, $keywords, $uuids,$notUuids,$tagIds,$tagNames);
        if ($category) {
            if ($this->mipInfo['aritcleLevelRemove']) {
                $baseUrl = $this->domain . '/' . $category . '/';
            } else {
                $baseUrl = $this->domain . '/' . $this->mipInfo['articleModelUrl'] . '/' . $category . '/';
            }
            if ($sub) {
                $baseUrl = $this->domain . '/' . $category . '/' . $sub . '/';
            }
        } else {
            $baseUrl = $this->domain . '/' .  $this->mipInfo['articleModelUrl'] . '/';
        }
        if ($tagIds) {
            $tempTagIds = explode(',', $tagIds);
            if (count($tempTagIds) == 1) {
                $tagInfo = db($this->tags)->where('id',$tagIds)->find();
                if ($tagInfo) {
                    if ($tagInfo['url_name']) {
                        $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['url_name'] . '/';
                    } else {
                        $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['id'] . '/';
                    }
                }
            } else {
                return false;
            }
        }
        if ($tagNames) {
            $tempTagNames = explode(',', $tagNames);
            if (count($tempTagNames) == 1) {
                $tagInfo = db($this->tags)->where('name',$tagNames)->find();
                if ($tagInfo) {
                    if ($tagInfo['url_name']) {
                        $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['url_name'] . '/';
                    } else {
                        $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tagInfo['id'] . '/';
                    }
                }
            } else {
                return false;
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
                if ($this->mipInfo['topDomain']) {
                    $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('site_id',$this->dataId)->count();
                } else {
                    $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->count();
                }
            } else {
                 if ($cid == 0) {
                    if ($this->mipInfo['topDomain']) {
                        $count = db($this->articles)->where('cid',$cid)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('site_id',$this->dataId)->count();
                    } else {
                        $count = db($this->articles)->where('cid',$cid)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->count();
                    }
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
                        if ($this->mipInfo['topDomain']) {
                            $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('cid',$cid)->where('site_id',$this->dataId)->count();
                        } else {
                            $count = db($this->articles)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($notUuidsWhere)->where('cid',$cid)->count();
                        }
                    }
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
            
            if ($this->mipInfo['topDomain']) {
                $itemTagsList = db($this->itemTags)->where($tagIdsWhere)->where('item_type','article')->select();
                if ($itemTagsList) {
                    foreach ($itemTagsList as $k => $v) {
                        $itemTagsListIds[] = $v['item_id'];
                    }
                    $itemTagsListIds = implode(',', $itemTagsListIds);
                    $count = db($this->articles)->where('uuid','in',$itemTagsListIds)->where('site_id',$this->dataId)->count();
                }
            } else {
                $count = db($this->itemTags)->where($tagIdsWhere)->where('item_type','article')->count();
            }
        }
        return $count;
    }
    
    public function getCategory($pid = 0, $orderBy = 'sort', $order = 'asc', $limit = null, $where = null)
    {
        $itemCategoryList = null;
        if ($pid == 0) {
            $itemCategoryList = db($this->itemCategory)->where('pid',0)->where('status',1)->where($where)->limit($limit)->order($orderBy,$order)->select();
            if($itemCategoryList) {
                foreach ($itemCategoryList as $key => $val) {
                    if ($this->mipInfo['aritcleLevelRemove']) {
                        $itemCategoryList[$key]['url'] =  $this->domain . '/' . $val['id'] . '/';
                    } else {
                        $itemCategoryList[$key]['url'] =  $this->domain . '/' . $this->mipInfo['articleModelUrl']  . '/' . $val['id'];
                    }
                    $itemCategoryList[$key]['sub'] = db($this->itemCategory)->where('pid',$val['id'])->order($orderBy,$order)->select();
                    if ($itemCategoryList[$key]['sub']) {
                        foreach ($itemCategoryList[$key]['sub'] as $k => $v) {
                            $itemCategoryList[$key]['sub'][$k]['url'] = $this->domain . '/' . $val['url_name'] . '/' . $v['url_name'] . '/';
                        }
                    } else {
                        $itemCategoryList[$key]['sub'] = array();
                    }
                    $itemCategoryList[$key]['children'] = $itemCategoryList[$key]['sub'];
                }
            }
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$pid)->where($where)->limit($limit)->order($orderBy,$order)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $k => $v) {
                    $itemCategoryInfo = db($this->itemCategory)->where('id',$pid)->find();
                    $itemCategoryList[$k]['url'] = $this->domain . '/' . $itemCategoryInfo['url_name'] . '/' . $v['url_name'] . '/';
                }
            }
        }
        
        return $itemCategoryList;
    }
    
    public function getImgList($item)
    {
        if (!$item) {
            return false;
        }
        $patern = '/^^((https|http|ftp)?:\/\/)[^\s]+$/';
        $item['content'] = htmlspecialchars_decode($this->getContentByItemId($item['id'],$item['content_id'])['content']);
        if (preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $item['content'], $imgs)) {
            $item['imgCount'] = count($imgs[1]);
            foreach ($imgs[1] as $key => $value) {
               if (@preg_match($patern,$value)) {
                   $imgs[1][$key] = $value;
                } else {
                   $imgs[1][$key] = $this->domain . $value;
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
     
    public function getCategoryParentInfoByCid($cid)
    {
        if (!$cid) {
            return false;
        }
        $articleCategoryInfo = db($this->itemCategory)->where('id',$cid)->find();
        if ($articleCategoryInfo) {
            if ($articleCategoryInfo['pid'] == 0) {
                if ($this->mipInfo['aritcleLevelRemove']) {
                    $articleCategoryInfo['url'] =  $this->domain . '/' . $articleCategoryInfo['url_name'] . '/';
                } else {
                    $articleCategoryInfo['url'] =  $this->domain . '/' . $this->mipInfo['articleModelUrl']  . '/' . $articleCategoryInfo['url_name'] . '/';
                }
                $articleCategoryInfo['name'] = $articleCategoryInfo['name'];
                $articleCategoryInfo['url_name'] = $articleCategoryInfo['url_name'];
            } else {
                $articleCategoryInfo['parent'] = db($this->itemCategory)->where('id',$articleCategoryInfo['pid'])->find();
                if ($articleCategoryInfo['parent']) {
                    if ($this->mipInfo['aritcleLevelRemove']) {
                        $articleCategoryInfo['url'] =  $this->domain . '/' . $articleCategoryInfo['parent']['url_name'] . '/';
                    } else {
                        $articleCategoryInfo['url'] =  $this->domain . '/' . $this->mipInfo['articleModelUrl']  . '/' . $articleCategoryInfo['parent']['url_name'] . '/';
                    }
                    $articleCategoryInfo['name'] = $articleCategoryInfo['parent']['name'];
                    $articleCategoryInfo['url_name'] = $articleCategoryInfo['parent']['url_name'];
                }
            }
        } else {
            return false;
        }
        return $articleCategoryInfo;
    }

    public function getCategorySubInfoByCid($cid)
    {
        if (!$cid) {
            return false;
        }
        $articleCategoryInfo = db($this->itemCategory)->where('id',$cid)->find();
        if ($articleCategoryInfo && $articleCategoryInfo['pid'] !== 0) {
            $articleCategoryInfo['parent'] = db($this->itemCategory)->where('id',$articleCategoryInfo['pid'])->find();
            if ($articleCategoryInfo['parent']) {
                $articleCategoryInfo['name'] = $articleCategoryInfo['name'];
                $articleCategoryInfo['url_name'] = $articleCategoryInfo['url_name'];
                $articleCategoryInfo['url'] = $this->domain . '/' . $articleCategoryInfo['parent']['url_name'] . '/' . $articleCategoryInfo['url_name'] . '/';
                return $articleCategoryInfo;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
     
    public function getUrlByItemInfo($item,$domain = null)
    {
        
        if ($this->mipInfo['idStatus']) {
            $tempId = $item['uuid'];
        } else {
            $tempId = $item['id'];
        }
        if ($this->mipInfo['diyUrlStatus']) {
            if ($item['url_name']) {
                $tempId = $item['url_name'];
            }
        }
        
        if (!$domain) {
            $domain = $this->domain;
        }
        
        $item['categoryInfo'] = db($this->itemCategory)->where('id',$item['cid'])->find();
        
        $res = $domain . '/' . $this->mipInfo['articleModelUrl'] . '/details/' . $tempId;
        
        if ($this->mipInfo['urlCategory']) {
            $item['itemCategory'] = db($this->itemCategory)->where('id',$item['cid'])->find();
            if ($item['itemCategory']) {
                $item['articlesLastCategory'] = db($this->itemCategory)->where('id',$item['itemCategory']['pid'])->find();
                if ($item['articlesLastCategory']) {
                    $res = $domain . '/' . $item['articlesLastCategory']['url_name'] . '/' . $item['itemCategory']['url_name'] . '/' . $tempId . '.html';
                } else {
                    $res = $domain . '/' . $item['itemCategory']['url_name'] . '/' . $tempId . '.html';
                }
            }
        }
        return $res;
    }
    
    public function getArticleDetailCurrentUrlNotHtml($item)
    {
        if ($this->mipInfo['idStatus']) {
            $tempId = $item['uuid'];
        } else {
            $tempId = $item['id'];
        }
        if ($this->mipInfo['diyUrlStatus']) {
            if ($item['url_name']) {
                $tempId = $item['url_name'];
            }
        }
        $item['itemCategory'] = db($this->itemCategory)->where('id',$item['cid'])->find();
        
        $res = $this->domain . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId;
        
        if ($this->mipInfo['urlCategory']) {
            $item['itemCategory'] = db($this->itemCategory)->where('id',$item['cid'])->find();
            if ($item['itemCategory']) {
                $item['articlesLastCategory'] = db($this->itemCategory)->where('id',$item['itemCategory']['pid'])->find();
                if ($item['articlesLastCategory']) {
                    $res = $this->domain . '/' . $item['articlesLastCategory']['url_name'] . '/' . $item['itemCategory']['url_name'] . '/' . $tempId;
                } else {
                    $res = $this->domain . '/' . $item['itemCategory']['url_name'] . '/' . $tempId;
                }
            }
        }
        return $res;
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

    public function getContentByItemId($id,$content_id)
    {
        if (!$id) {
            return false;
        }
        return db($this->articlesContent)->where('id',$content_id)->find();
    }
    
    public function getContentFilterByArticleId($id,$content_id)
    {
        if (!$id) {
            return false;
        }
        $itemInfo = db($this->articlesContent)->where('id',$content_id)->find();
        $itemInfo['content'] = htmlspecialchars_decode($itemInfo['content']);
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
        $patern = '/^^((https|http|ftp)?:\/\/)[^\s]+$/';
        foreach($imagesArray[0] as $key => $val) {
            @preg_match('/alt=".+?"/',$val,$tempAlt);
            @preg_match('/<img.+(width=\"?\d*\"?).+>/i',$val,$tempWidth);
            @preg_match('/<img.+(height=\"?\d*\"?).+>/i',$val,$tempHeight);
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
                $src = $this->domain . $imagesArray[1][$key];
            }
            if ($tempWidth && $tempHeight) {
                @preg_match('/\d+/i',$tempWidth[1],$width);
                if (intval($width[0]) > 320) {
                    $layout = 'layout="container"';
                    $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                } else {
                    $layout = 'layout="fixed"';
                    $tempImg = '<mip-img ' .$layout. ' ' . $tempWidth[1] . '" ' . $tempHeight[1] .'" alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                }
            } else {
                $layout = 'layout="container"';
                $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
            }
            $itemInfo['content'] =  str_replace($val,$tempImg,$itemInfo['content']);
        }
        $itemInfo['content'] =  preg_replace("/style=.+?['|\"]/i",'', $itemInfo['content']);
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
        return $itemInfo['content'];
    }
    public function getTagsLink($itemInfo)
    {
        $tagsList = explode(',',$itemInfo['link_tags']);
        $tempTagsList = [];
        if ($tagsList) {
            foreach ($tagsList as $key => $val) {
                $tagsInfo = db($this->tags)->where('name',$val)->find();
                if ($tagsInfo) {
                    $tempTagsList[] = $tagsInfo;
                }
            }
            if ($tempTagsList) {
                foreach ($tempTagsList as $k => $v) {
                    if ($tempTagsList[$k]['url_name']) {
                        $tempTagsList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] .'/' . $tempTagsList[$k]['url_name'] . '/';
                    } else {
                        $tempTagsList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] .'/' . $tempTagsList[$k]['id'] . '/';
                    }
                }
            }
            foreach ($tempTagsList as $ke => $va) {
                $url = '<a href="' . $tempTagsList[$ke]["url"] . '" data-type="mip" data-title="' . $tempTagsList[$ke]['name'] . '" target="_blank" title="' . $tempTagsList[$ke]['name'] . '">' . $tempTagsList[$ke]["name"] . '</a>';
                $keyword = $va['name'];
                $content  = $itemInfo['content'];
                $content = preg_replace_callback('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$keyword.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si',
                function ($param)use($url) {
                    return $url;
                },$content,1);
                $itemInfo['content'] = $content;
            }
        }
        return $itemInfo;
    }
    public function getAllTagsLink($itemInfo)
    {
        $tagsList = db($this->tags)->select();
        if ($tagsList) {
            foreach ($tagsList as $key => $val) {
                if ($val['url_name']) {
                    $tagsList[$key]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] .'/' . $val['url_name'] . '/';
                } else {
                    $tagsList[$key]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] .'/' . $val['id'] . '/';
                }
            }
            foreach ($tagsList as $key => $val) {
                $url = '<a href="' . $tagsList[$key]["url"] . '" data-type="mip" data-title="' . $tagsList[$key]['name'] . '" target="_blank" title="' . $tagsList[$key]['name'] . '">' . $tagsList[$key]["name"] . '</a>';
                $keyword = $val['name'];
                $content  = $itemInfo['content'];
                try {
                    $content = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$keyword.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', $url, $content,1);
                    $itemInfo['content'] = $content;
                } catch (\Exception $e) {
                    $itemInfo['content'] = $content;
                }
            }
        }
        return $itemInfo;
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