<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\m;
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

        return $this->mipView('tag/tag','m');
    }
    public function tagDetail() {
        $id = input('param.id');
        $page = input('param.page');
        $page = $page ? $page : 1;
        $articleList = null;
        $askList = null;
        $tagInfo =  Tags::where('url_name',$id)->find();
        if (!$tagInfo) {
            $tagInfo =  Tags::where('name',$id)->find();
            $tempWhere = $tagInfo['name'];
            if (!$tagInfo) {
                $tagInfo = Tags::where('id',$id)->find();
                $tempWhere = $id;
            }
        } else {
            $tempWhere = $tagInfo['url_name'];
        }
        if (!$tagInfo) {
            $this->error('标签不存在','/');
        }
        $this->assign('tagInfo',$tagInfo);

            //标题关键词描述
        $this->assign('mipTitle',$tagInfo['name'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName']);
        $this->assign('mipKeywords',$tagInfo['name']);
        $this->assign('mipDescription',$tagInfo['description']);

        $itemTagsList = ItemTags::where('tags_id',$tagInfo['id'])->order('item_add_time desc')->where('item_type','article')->page($page,10)->select();
        $count = 0;
        if ($itemTagsList) {
            foreach ($itemTagsList as $k => $v) {
                $itemTagsListIds[] = $v['item_id'];
            }
            $whereTagsList['uuid'] = ['in', implode(',', $itemTagsListIds)];
            $itemTagsList = model('app\model\Articles\Articles')->getItemList(0, 1, 10, 'publish_time', 'desc', $whereTagsList);
            $count = ItemTags::where('tags_id',$tagInfo['id'])->count();
        }
        $this->assign('articleList',$itemTagsList);


        $hotTagsList = Tags::limit(20)->order('relevance_num', 'desc')->select();
        if ($hotTagsList) {
            foreach ($hotTagsList as $k => $v) {
                if ($hotTagsList[$k]['url_name']) {
                    $hotTagsList[$k]['url'] = $this->mipInfo['httpType'] . $this->mipInfo['domain'] . $this->rewrite . '/' . $this->mipInfo['tagModelUrl'] . '/' . $v['url_name'] . '/';
                } else {
                    $hotTagsList[$k]['url'] = $this->mipInfo['httpType'] . $this->mipInfo['domain'] . $this->rewrite . '/' . $this->mipInfo['tagModelUrl'] . '/' . $v['id'] . '/';
                }
            }
        }

        $this->assign('hotTagsList',  $hotTagsList);
        $pagination_array = array(
            'base_url' => $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite .'/'.$this->mipInfo['tagModelUrl'] . '/' . $tempWhere ,
            'total_rows' => $count, //总共条数
            'per_page' => 10, //每页展示数量
            'page_break' => $this->mipInfo['urlPageBreak'] //分页符号
        );
        $pagination = new Pagination($pagination_array);
        $this->assign('pagination',  $pagination->create_links());

        return $this->mipView('tag/tagDetail','m');
    }


}