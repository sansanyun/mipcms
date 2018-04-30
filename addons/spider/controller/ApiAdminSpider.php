<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace addons\spider\controller;
use think\Request;
use think\Loader;

use mip\AdminBase;
class ApiAdminSpider extends AdminBase
{
    public function index() {

    }

    public function spidersSelect(Request $request) {
      	$page = input('post.page');
		$limit = input('post.limit');
		$orderBy = input('post.orderBy');
		$order = input('post.order');
		if (!$page) {
		  $page = 1;
		}
		if (!$limit) {
		  $limit = 10;
		}
		if (!$orderBy) {
		 $orderBy = 'add_time';
		}
		if (!$order) {
			$order = 'desc';
		}
	    $spidersList = db('spiders')->limit($limit)->page($page)->order($orderBy, $order)->select();
//          foreach ($spidersList as $k => $v) {
//              $ip = @file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=Json&ip=".$v['ua']);
//              $spidersList[$k]['ip'] = json_decode($ip,true);
//          }
	    return jsonSuccess('',['spidersList' => $spidersList,'total' => db('spiders')->count(),'page' => $page]);
    }

    public function spidersToday()
    {
        $type = input('post.type');
        $type ? $type : $type = 'pc';
        $todyStartTime = strtotime(date('Y-m-d'));
        $thisDaySeconds = 24 * 60 *60;
        for ($i = 0; $i < 25; $i++) {
            $spidersTodayList[] = db('spiders')->where('add_time','between',[$todyStartTime+($i*3600),$todyStartTime+(($i+1)*3600)])->where('type',$type)->count();
        }
        return jsonSuccess('',['spidersTodayList' => $spidersTodayList]);
    }
    
    public function getCount()
    {
        $todayUserCount = db('spiders')->where('add_time','>',strtotime(date('Y-m-d')))->count();
        $yesterdayUserCount = db('spiders')->where('add_time','>',strtotime(date('Y-m-d'))-(60*60*24*1))->where('add_time','<',strtotime(date('Y-m-d')))->count();
        $weekUserCount = db('spiders')->where('add_time','>',strtotime(date('Y-m-d'))-(60*60*24*7))->where('add_time','<',strtotime(date('Y-m-d')))->count();
        $allUserCount = db('spiders')->count();
        
        return jsonSuccess('',['todayUserCount' => $todayUserCount,'yesterdayUserCount' => $yesterdayUserCount, 'weekUserCount'=> $weekUserCount, 'allUserCount' => $allUserCount] );
    }
    
}