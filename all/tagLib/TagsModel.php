<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace tagLib;
use mip\Paginationm;
use mip\Init;
class TagsModel extends Init
{
    public $item;
    public $itemCategory;
    public function _initialize()
    {
        parent::_initialize();
        $this->item = $this->tags;
        $this->itemCategory = $this->tagsCategory;
        
    }
    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'add_time', $order = 'desc', $where = null,$keywords = null,$articleIds = null)
    {
        if (empty($keywords)) {
                $keywordsWhere = null;
        } else {
            $keywords = explode(',',$keywords);
            foreach ($keywords as $key => $val) {
                if ($val) {
                    $sq[] = "%".$val."%";
                }
            }
            $keywordsWhere['name']  = ['like',$sq,'OR'];
        }
   
        if ($this->mipInfo['topDomain']) {
            $itemUuids = db($this->articles)->where('site_id',$this->dataId)->column('uuid');
            $tagsIds = db($this->itemTags)->where('item_id','in',$itemUuids)->column('tags_id');
            if ($cid == 0 || empty($cid)) {
                $itemList = db($this->tags)->where($where)->where($keywordsWhere)->where('id','in',$tagsIds)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemList = db($this->tags)->where($where)->where($keywordsWhere)->where('id','in',$tagsIds)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
            }
            if ($itemList) {
                foreach ($itemList as $k => $v) {
                    if ($itemList[$k]['url_name']) {
                        $itemList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $v['url_name'] . '/';
                    } else {
                        $itemList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $v['id'] . '/';
                    }
                }
            }
        } else {
            if ($articleIds) {
                $itemTags = db($this->itemTags)->where('item_id','in',$articleIds)->select();
                $itemList = [];
                if ($itemTags) {
                    foreach ($itemTags as $k => $v) {
                        $itemList[] = db($this->tags)->where('id',$v['tags_id'])->find();
                    }
                    if ($itemList) {
                        foreach ($itemList as $k => $v) {
                            if ($v) {
                                if ($itemList[$k]['url_name']) {
                                    $itemList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $itemList[$k]['url_name'] . '/';
                                } else {
                                    $itemList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $itemList[$k]['id'] . '/';
                                }
                            }
                        }
                    }
                    
                }
            } else {
                if ($cid == '' || $cid == null) {
                    $itemList = db($this->tags)->where($where)->where($keywordsWhere)->page($page,$perPage)->order($orderBy,$order)->select();
                } else {
                    $itemList = db($this->tags)->where($where)->where($keywordsWhere)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                }
            }
            if ($itemList) {
                foreach ($itemList as $k => $v) {
                    if ($itemList[$k]['url_name']) {
                        $itemList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $v['url_name'] . '/';
                    } else {
                        $itemList[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $v['id'] . '/';
                    }
                    $itemList[$k]['categoryInfo'] = db($this->itemCategory)->where('id',$v['cid'])->find();
                }
            } else {
                $itemList = array();
            }
        }
        return $itemList;
    }
    
    public function getPaginationm($cid = 0, $per_page = 10, $category = null, $sub = null,$where = null)
    {
        $count = $this->getCount($cid);;
        if ($category) {
            $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $category . '/';
            if ($sub) {
                $baseUrl = $this->domain . '/' . $category . '/' . $sub . '/';
            }
        } else {
            $baseUrl = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/';
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
    
    public function getCount($cid)
    {
        $count = 0;
        if ($cid == '' || $cid == null) {
            $count = db($this->tags)->count();
        } else {
            if ($cid == 0) {
                $count = db($this->tags)->where('cid',$cid)->count();
            } else {
                $subCategoryList = db($this->tagsCategory)->where('pid',$cid)->select();
                if ($subCategoryList) {
                    foreach ($subCategoryList as $key => $value) {
                           $cids[] = $value['id'];
                    }
                }
                if ($subCategoryList) {
                    $count = db($this->tags)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->count();
                } else {
                    $count = db($this->tags)->where('cid',$cid)->count();
                }
            }
            
        }
        
        return $count;
    }
    
    public function getTagsListByItemType($type, $itemId)
    {
        if (!$type) {
            $type = 'article';
        }
        $tags = db($this->itemTags)->where('item_id',$itemId)->select();
        $tempItemTagsList = [];
        if ($tags) {
            foreach ($tags as $k => $v) {
                $tags[$k]['tags'] = db($this->tags)->where('id',$v['tags_id'])->find();
                if ($tags[$k]['tags']) {
                    $tempName[] = $tags[$k]['tags']['name'];
                    if ($tags[$k]['tags']['url_name']) {
                        $tags[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tags[$k]['tags']['url_name'] . '/';
                    } else {
                        $tags[$k]['url'] = $this->domain . '/' . $this->mipInfo['tagModelUrl'] . '/' . $tags[$k]['tags']['id'] . '/';
                    }
                    $tempItemTagsList[] = $tags[$k];
                }
            }
        }
        return $tempItemTagsList;
    }
    
    public function getCategory($pid = 0, $orderBy = 'sort', $order = 'asc', $limit = null, $where = null)
    {
        $itemCategoryList = null;
        if ($pid == 0) {
            $itemCategoryList = db($this->itemCategory)->where('pid',0)->where($where)->limit($limit)->order($orderBy,$order)->select();
            if($itemCategoryList) {
                foreach ($itemCategoryList as $key => $val) {
                    $itemCategoryList[$key]['url'] =  $this->domain . '/' . $this->mipInfo['productModelUrl']  . '/' . $val['url_name'] . '/';
                    $itemCategoryList[$key]['sub'] = db($this->itemCategory)->where('pid',$val['id'])->select();
                    if ($itemCategoryList[$key]['sub']) {
                        foreach ($itemCategoryList[$key]['sub'] as $k => $v) {
                            $itemCategoryList[$key]['sub'][$k]['url'] = $this->domain  . '/' . $this->mipInfo['productModelUrl'] . '/' . $val['url_name'] . '/' . $v['url_name'] . '/';
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
                    $itemCategoryList[$k]['url'] = $this->domain . '/' . $this->mipInfo['productModelUrl'] . '/' . $itemCategoryInfo['url_name'] . '/' . $v['url_name'] . '/';
                }
            }
        }
        
        return $itemCategoryList;
    }
    
}