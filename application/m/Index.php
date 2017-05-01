<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\m;
use app\api\model\Articles;
use app\api\model\ArticlesCategory;
use think\Request;
use think\Validate;
use mip\Pagination;
use mip\Mip;
class Index extends Mip
{
    public function index()
    {
        if ($this->mipInfo['systemType'] == 'Blog' || $this->mipInfo['systemType'] == 'CMS') {
            $categoryList = ArticlesCategory::order('sort desc')->select();
            $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
            '|'. 
            '([0-9a-z_!~*\'()-]+\.)*'. 
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. 
            '[a-z]{2,6})'.   
            '(:[0-9]{1,4})?'.  
            '((\/\?)|'.  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/'; 
            foreach ($categoryList as $key => $val) {
                $val['articles'] = Articles::where('publish_time','<',time())->where('cid',$val['id'])->order('publish_time desc')->limit(10)->select();
                foreach ($val['articles']  as $k => $v) {
                    $v['content'] = htmlspecialchars_decode($v['content']);
                    $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                    if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                        if (@preg_match($patern,$imgs[1][0])) {
                            $v['firstImg'] = $imgs[1][0];
                        } else {
                            $v['firstImg'] = $this->domain.$imgs[1][0];
                        }
                    } else {
                        $v['firstImg'] = null;
                    }
                }
                if(!Validate::regex($categoryList[$key]['url_name'],'\d+') AND $categoryList[$key]['url_name']){
                    $categoryList[$key]['url_name'] = $categoryList[$key]['url_name'];
                }else{
                    $categoryList[$key]['url_name'] = 'cid_'.$categoryList[$key]['id'];
                }
            }
            $this->assign('categoryList',$categoryList);
            
            $recommendList = Articles::where('publish_time','<',time())->limit(4)->where('is_recommend',1)->order('publish_time desc')->select();
            foreach ($recommendList as $k => $v){
                $v['content'] = htmlspecialchars_decode($v['content']);
                $v['id'] = $this->mipInfo['idStatus'] ? $v['uuid'] : $v['id'];
                if (preg_match_all('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/', bbc2html($v['content']), $imgs)) {
                    if (@preg_match($patern,$imgs[1][0])) {
                        $v['firstImg'] = $imgs[1][0];
                    } else {
                        $v['firstImg'] = $this->domain.$imgs[1][0];
                    }
                } else {
                    $v['firstImg'] = null;
                }
            };
            $this->assign('recommendList',$recommendList);
            
            return $this->mipView('m/index/index');
        }
    }
    
}
