<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\model\Articles;
use think\Model;
use app\model\Users\Users;
use app\model\Articles\ArticlesCategory;
use app\model\Articles\ArticlesContent;
use app\model\Tags\ItemTags;
use app\model\Tags\Tags;
use think\Db;
use think\Cache;
use mip\ModelBase;

class Articles extends ModelBase
{
    public function getItemList($cid, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null)
    {
        if ($cid == 0) {
            $itemList = $this->where($where)->page($page,$perPage)->order($orderBy,$order)->select();
        } else {
            $articlesCategoryList = ArticlesCategory::where('pid',$cid)->select();
            if ($articlesCategoryList) {
                foreach ($articlesCategoryList as $key => $value) {
                       $cids[] = $value['id'];
                }
            }
            if ($articlesCategoryList) {
                $itemList = $this->where($where)->where('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemList = $this->where($where)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
            }
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['url'] = $this->domainUrl($v);
                $itemList[$k]['mipUrl'] = $this->domainMipUrl($v);
                $itemList[$k] = $this->filter($v);
                $itemList[$k]['users'] = Users::where('uid',$v['uid'])->find();
                $itemList[$k]['articlesCategory'] = articlesCategory::where('id',$v['cid'])->find();
//              $tempItemTags = ItemTags::where('item_id',$v['uuid'])->select();
//              $itemList[$k]['tagsIds'] = array();
//              $tempTagsIds = array();
//              if ($tempItemTags) {
//                  foreach ($tempItemTags as $key => $value) {
//                      $tempTagsIds[] = $value['tags_id'];
//                  }
//                  if ($tempTagsIds) {
//                      $itemList[$k]['tagsIds'] = Tags::where('id','in',$tempTagsIds)->select();
//                  }
//              }
            }
        } else {
            $itemList = null;
        }
        return $itemList;
    }

    public function getItemListNoContent($cid, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null)
    {
        if ($cid == 0) {
            $itemList = $this->where($where)->page($page,$perPage)->order($orderBy,$order)->select();
        } else {
            $articlesCategoryList = ArticlesCategory::where('pid',$cid)->select();
            if ($articlesCategoryList) {
                foreach ($articlesCategoryList as $key => $value) {
                       $cids[] = $value['id'];
                }
            }
            if ($articlesCategoryList) {
                $itemList = $this->where($where)->where('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemList = $this->where($where)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
            }
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['url'] = $this->domainUrl($v);
                $itemList[$k]['mipUrl'] = $this->domainMipUrl($v);
            }
        } else {
            $itemList = null;
        }
        return $itemList;
    }

    public function filter($item)
    {
        if (!$item) {
            return false;
        }
        $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'.
            '|'.
            '([0-9a-z_!~*\'()-]+\.)*'.
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'.
            '[a-z]{2,6})'.
            '(:[0-9]{1,4})?'.
            '((\/\?)|'.
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/';
        $item['content'] = htmlspecialchars_decode($this->getContentByArticleId($item['id'],$item['content_id'])['content']);
        if (preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $item['content'], $imgs)) {
            $item['imgCount'] = count($imgs[1]);
            foreach ($imgs[1] as $key => $value) {
               if (@preg_match($patern,$value)) {
                   $imgs[1][$key] = $value;
                } else {
                   $imgs[1][$key] = $this->mipInfo['httpType'].$this->mipInfo['domain'].$value;
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

    public function users()
    {
        return $this->hasOne('app\model\Users\Users','uid','uid')->field('uid,username,nickname,article_num,article_comments_num');
    }

    public function articlesCategory()
    {
        return $this->hasOne('app\model\Articles\ArticlesCategory','id','cid');
    }

    public function domainUrl($item)
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
        if ($this->mipInfo['articleDomain']) {
            $res = $this->mipInfo['httpType'].$this->mipInfo['articleDomain'] . $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
        } else {
            if ($this->mipInfo['domain']) {
                $res = $this->mipInfo['httpType'] . $this->mipInfo['domain'] . $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
            } else {
                $res = $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
            }
        }
        if ($this->mipInfo['superSites']) {
            $categoryInfo = ArticlesCategory::get($item['cid']);
            if ($categoryInfo) {
                if ($categoryInfo['pid'] != 0) {
                    $tempCategoryInfo = ArticlesCategory::get($categoryInfo['pid']);
                    $res = $this->mipInfo['httpType'] . $tempCategoryInfo['url_name'] . '.' . $this->mipInfo['topDomain'] . $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
                } else {
                    $res = $this->mipInfo['httpType'] . $categoryInfo['url_name'] . '.' . $this->mipInfo['topDomain'] . $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
                }
            } else {
                $res = $this->mipInfo['httpType'] . $this->mipInfo['domain'] . $this->rewrite  . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
            }
        }
        return $res;
    }

    public function domainMipUrl($item)
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
        if ($this->mipInfo['articleDomain']) {
            $res = $this->mipInfo['httpType']. 'm.' .$this->mipInfo['articleDomain'] . $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
        } else {
            $res = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
        }
        if ($this->mipInfo['superSites']) {
            $categoryInfo = ArticlesCategory::get($item['cid']);
            if ($categoryInfo) {
                if ($categoryInfo['pid'] != 0) {
                    $tempCategoryInfo = ArticlesCategory::get($categoryInfo['pid']);
                    $res = $this->mipInfo['httpType'] . 'm.' . $tempCategoryInfo['url_name'] . '.' . $this->mipInfo['topDomain'] . $this->rewrite . '/' .  $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
                } else {
                    $res = $this->mipInfo['httpType'] .  'm.' . $categoryInfo['url_name'] . '.' . $this->mipInfo['topDomain'] .  $this->rewrite . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
                }
            } else {
                $res = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite  . '/' . $this->mipInfo['articleModelUrl'] . '/' . $tempId . '.html';
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
        $this->where('id',$id)->update([
            'views' => ['exp','views+1'],
        ]);
        return true;
    }

    public function getContentByArticleId($id,$content_id)
    {
        if (!$id) {
            return false;
        }
        return ArticlesContent::where('id',$content_id)->find();
    }

    public function getLoveListByTags($tags, $aboutLoveListNum = 8, $currentUuid)
    {
        if (!is_array($tags)) {
            return false;
        }
        $tagNameOne = null;
        $countNum = count($tags);
        switch ($countNum) {
        case 1:
          $tagNameOne = $tags[0]['tags']['name'];
          $tagNameTwo = null;
          $tagNameThree = null;
          $tagNameFour = null;
          break;
        case 2:
          $tagNameOne = $tags[0]['tags']['name'];
          $tagNameTwo = $tags[1]['tags']['name'];
          $tagNameThree = null;
          $tagNameFour = null;
          break;
        case 3:
          $tagNameOne = $tags[0]['tags']['name'];
          $tagNameTwo = $tags[1]['tags']['name'];
          $tagNameThree = $tags[2]['tags']['name'];
          $tagNameFour = null;
          break;
        case 4:
          $tagNameOne = $tags[0]['tags']['name'];
          $tagNameTwo = $tags[1]['tags']['name'];
          $tagNameThree = $tags[2]['tags']['name'];
          $tagNameFour = $tags[3]['tags']['name'];
          break;
        default:
        }
        if ($tagNameOne) {
            $sq = "%".$tagNameOne."%";
            $tagWhere['title']  = ['like',$sq];
            $tagWhere['uuid']  = ['<>',$currentUuid];
            $aboutLoveList = $this->getItemList(0, 1, $aboutLoveListNum, 'publish_time', 'desc', $tagWhere);
            if (!$aboutLoveList) {
                $aboutLoveList = array();
            }
            $aboutLoveListCount = count($aboutLoveList);
            if ($aboutLoveListCount < $aboutLoveListNum) {
                if ($tagNameTwo) {
                    $aboutLoveListNum = $aboutLoveListNum - $aboutLoveListCount;
                    $sqTwo = "%".$tagNameTwo."%";
                    $tagWhere['title']  = ['like',$sqTwo];
                    $tagWhere['uuid']  = ['<>',$currentUuid];
                    $aboutLoveListTwo = $this->getItemList(0, 1, $aboutLoveListNum, 'publish_time', 'desc', $tagWhere);
                    if (!$aboutLoveListTwo) {
                        $aboutLoveListTwo = array();
                    }
                    if (is_array($aboutLoveList) && is_array($aboutLoveListTwo)) {
                        $aboutLoveList = array_merge($aboutLoveList,$aboutLoveListTwo);
                        $aboutLoveListTwoCount = count($aboutLoveListTwo);
                        if ($aboutLoveListTwoCount < $aboutLoveListNum) {
                            if ($tagNameThree) {
                                $aboutLoveListNum = $aboutLoveListNum - $aboutLoveListTwoCount;
                                $sqThree = "%".$tagNameThree."%";
                                $tagWhere['title']  = ['like',$sqThree];
                                $tagWhere['uuid']  = ['<>',$currentUuid];
                                $aboutLoveListThree = $this->getItemList(0, 1, $aboutLoveListNum, 'publish_time', 'desc', $tagWhere);
                                if (!$aboutLoveListThree) {
                                    $aboutLoveListThree = array();
                                }
                                if (is_array($aboutLoveList) && is_array($aboutLoveListThree)) {
                                    $aboutLoveList = array_merge($aboutLoveList,$aboutLoveListThree);
                                    $aboutLoveListThreeCount = count($aboutLoveListThree);
                                    if ($aboutLoveListThreeCount < $aboutLoveListNum) {
                                        if ($tagNameFour) {
                                            $aboutLoveListNum = $aboutLoveListNum - $aboutLoveListThreeCount;
                                            $sqFour = "%".$tagNameFour."%";
                                            $tagWhere['title']  = ['like',$sqFour];
                                            $tagWhere['uuid']  = ['<>',$currentUuid];
                                            $aboutLoveListFour = $this->getItemList(0, 1, $aboutLoveListNum, 'publish_time', 'desc', $tagWhere);
                                            if (is_array($aboutLoveList) && is_array($aboutLoveListFour)) {
                                                $aboutLoveList = array_merge($aboutLoveList,$aboutLoveListFour);
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }

                }
            }
        } else {
            $aboutLoveList = null;
        }
        return $aboutLoveList;
    }

}