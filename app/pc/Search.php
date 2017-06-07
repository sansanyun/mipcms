<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use app\api\model\Articles;
use mip\Htmlp;
use mip\Mip;

class Search extends Mip {
    public function index() {
        
        $keywords = Htmlp::htmlp(input('param.keywords'));
        $cid = 0;
        $page = 1;
        $limit = 10;
        $orderBy = 'id';
        $order = 'desc';
        if ($keywords) {
            if($cid) {
                $where['cid'] = $cid;
            } 
            $sq = "%".$keywords."%";
            $where['title']  = ['like',$sq];
            $articleList = Articles::where($where)->limit($limit)->page($page)->order($orderBy, $order)->select();
            $itemCount = Articles::where($where)->count();
        } else {
            if(empty($cid)) {
                $articleList = Articles::limit($limit)->page($page)->order($orderBy, $order)->select();
                $itemCount = Articles::count();
            } else {
                $where['cid'] = $cid;
                $articleList = Articles::where($where)->limit($limit)->page($page)->order($orderBy, $order)->select();
                $itemCount = Articles::where($where)->count();
            }
        }
        if ($articleList) {
           $articleList = model('api/Articles')->filter($articleList, $this->mipInfo['idStatus'], $this->domain, $this->public);
        } else {
            $articleList = null;
        }
        
         $this->assign('keywords',$keywords);
         $this->assign('articleList',$articleList);
        return $this->mipView('pc/Search/search');
    }
}