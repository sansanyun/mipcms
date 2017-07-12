<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\pc;

use app\model\Asks\Asks;
use app\model\Asks\AsksAnswers;
use app\model\Asks\AsksCategory;
use app\model\Tags\ItemTags;
use app\model\Tags\Tags;
use app\model\Articles\Articles;
use app\model\Users\Users;
use mip\Pagination;
use mip\Mip;
class Tag extends Mip {

    protected $beforeActionList = ['start'];
    public function start() {

    }
    public function index() {

        return $this->mipView('tag/tag','pc');
    }
    public function tagDetail() {
        $id = input('param.id');
        $articleList = null;
        $askList = null;
        $tagInfo =  Tags::where('url_name',$id)->find();
        if (!$tagInfo) {
            $tagInfo =  Tags::where('name',$id)->find();
            if (!$tagInfo) {
                $tagInfo = Tags::where('id',$id)->find();
            }
        }
        if (!$tagInfo) {
            $this->error('标签不存在','/');
        }
        $this->assign('tagInfo',$tagInfo);

            //标题关键词描述
        $this->assign('mipTitle',$tagInfo['name'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName']);
        $this->assign('mipKeywords',$tagInfo['name']);
        $this->assign('mipDescription',$tagInfo['description']);

        $itemTagsList = ItemTags::where('tags_id',$id)->order('item_add_time desc')->where('item_type','article')->limit(10)->select();

        if ($itemTagsList) {
            foreach ($itemTagsList as $k => $v) {
                $itemTagsListIds[] = $v['item_id'];
            }
            $whereTagsList['uuid'] = ['in', implode(',', $itemTagsListIds)];
            $itemTagsList = model('app\model\Articles\Articles')->getItemList(0, 1, 10, 'publish_time', 'desc', $whereTagsList);
        }
        $this->assign('articleList',$itemTagsList);

        $itemTagsList = ItemTags::where('tags_id',$id)->order('item_add_time desc')->where('item_type','ask')->limit(10)->select();
        if ($itemTagsList) {
            foreach ($itemTagsList as $k => $v) {
                $itemTagsListIds[] = $v['item_id'];
            }
            $askList = Asks::where('id','in', implode(',', $itemTagsListIds))->select();
            if ($askList) {
                foreach($askList as $k => $v) {
                    $askList = model('app\model\Asks\Asks')->filter($askList, $this->mipInfo['idStatus'], $this->domain, $this->public);
                    $askList[$k]['tags'] = $askList[$k]->tagsSelect($v['id'],'ask');
                    $askList[$k]['url'] = $askList[$k]->domainUrl($v);
                }
            }
        }
        $this->assign('askList',$askList);

        return $this->mipView('tag/tagDetail','pc');
    }


}