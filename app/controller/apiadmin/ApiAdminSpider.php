<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\ApiAdmin;
use think\Request;
use think\Loader;
use app\model\Spiders;

use mip\AdminBase;
class ApiAdminSpider extends AdminBase
{
    public function index(){
		 
    }
   
    public function spidersSelect(Request $request){
		if (Request::instance()->isPost()) {
		    
	      	$page = input('post.page');
			$limit = input('post.limit');
			$orderBy = input('post.orderBy');
			$order = input('post.order');
			if(!$page){
			  $page = 1;
			}
			if(!$limit){
			  $limit = 10;
			}
			if(!$orderBy){
			 $orderBy = 'add_time';
			}
			if(!$order){
				$order = 'desc';
			}
		    $spidersList = Spiders::limit($limit)->page($page)->order($orderBy, $order)->select();
		    return jsonSuccess('',['spidersList' => $spidersList,'total' => Spiders::count(),'page' => $page]); 
		    
        }
    }

    public function spidersToday(Request $request){
        if (Request::instance()->isPost()) {
            
            $type = input('post.type');
            $type ? $type : $type = 'pc';
            $todyStartTime = strtotime(date('Y-m-d'));
            $thisDaySeconds = 24 * 60 *60;
            for ($i = 0; $i < 25; $i++) {
                $spidersTodayList[] =Spiders::where('add_time','between',[$todyStartTime+($i*3600),$todyStartTime+(($i+1)*3600)])->where('type',$type)->count();
            }
            return jsonSuccess('',['spidersTodayList' => $spidersTodayList]); 
        }
    }
}