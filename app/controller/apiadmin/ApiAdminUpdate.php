<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\apiadmin;
use think\Request;
use app\model\Settings;
use app\model\Articles\Articles;
use think\Db;

use mip\AdminBase;
class ApiAdminUpdate extends AdminBase
{
    public function index(){

    }

    public function update(Request $request) {
        if (Request::instance()->isPost()) {

            $versionMum = input('post.versionMum');


            $dbconfig['type'] = "mysql";
            $dbconfig['hostname'] = config('database')['hostname'];
            $dbconfig['username'] = config('database')['username'];
            $dbconfig['password'] = config('database')['password'];
            $dbconfig['hostport'] = config('database')['hostport'];
            $dbname = config('database')['database'];
            $dsn = "mysql:dbname={$dbname};host={$dbconfig['hostname']};port={$dbconfig['hostport']};charset=utf8";
            try {
                $db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
            } catch (\PDOException $e) {
                return jsonError('数据库连接失败');
            }

            $sql = file_get_contents(PUBLIC_PATH . 'package' . DS . $versionMum . '.sql');

            $tablepre = config('database')['prefix'];
            $sql = str_replace("\r", "\n", $sql);
            $sql = explode(";\n", $sql);
            $default_tablepre = "mip_";
            $sql = str_replace(" `{$default_tablepre}", " `{$tablepre}", $sql);
            foreach ($sql as $item) {
                $item = trim($item);
                if(empty($item)) continue;
                preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
                if($matches) {
                    if(false !== $db->exec($item)){

                    } else {
                       return jsonError('安装失败');
                    }
                } else {
                    $db->exec($item);
                }
            }
            return jsonSuccess('升级成功');

        }
    }

    public function oneToTwoUpData() {
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
             $orderBy = 'id';
            }
            if(!$order){
                $order = 'desc';
            }
            $itemCount = Db('ItemTags')->count();
            $TagsList = db::name('ItemTags')->limit($limit)->page($page)->order($orderBy, $order)->select();

            foreach ($TagsList as $key => $val) {
               $articleInfo = db::name('Articles')->where('id',intval($val['item_id']))->find();
                if ($articleInfo) {
                   $upDataInfo =  db::name('ItemTags')->where('id',$val['id'])->update([
                    'item_id' => $articleInfo['uuid'],
                    'tags_id' => intval($val['tags_id'])
                   ]);
                }
            }
            return jsonSuccess('',['articleList' => $TagsList,'total' => $itemCount,'page' => $page]);

        }
    }
}