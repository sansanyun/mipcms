<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\admin\controller;
use think\Controller;
use think\Config;
use think\Session;
use think\Request;
use mip\Init;
class Admin extends Init
{
	protected $beforeActionList = ['start'];

    public function start() {
        $this->assign('mipInfoToJson',json_encode(config('mipInfo')));
        
        $model = input('model');
        if ($model != 'login') {
            if (!$this->userId) {
                $this->redirect($this->domain.'/'. Config::get('admin') . '/' .'login',301);
            }
            if (!$this->isAdmin) {
                $this->redirect($this->domain.'/'. Config::get('admin') . '/' .'login',301);
            }
        }
    }
    public function index() {
        $model = input('model');
        $action = input('action');
        $params = input('params');
        $addonsCtr = input('addonsCtr');
        $addonsAct = input('addonsAct');
        $this->model = $model;
        $this->assign('model',$model);
        $this->assign('action',$action);
        $id = input('id');
        $this->assign('id',$id);
        $type = input('type');
        $this->assign('type',$type);
        $this->assign('params',$params);
        if (!empty($params)) {
            $params = explode('__', $params);
            if ($params) {
                foreach ($params as $key => $val) {
                    if (strpos($val, '-') !== false) {
                        $tempVal = explode('-', $val);
                        $this->assign($tempVal[0],$tempVal[1]);
                    }
                }
            }
        }
        
        $this->assign('indexTitle','首页');
        
        $tempAdminMenu = [];
        foreach (fetch_file_lists(APP_PATH) as $key => $file) {
            if (strstr($file,'adminMenu.php')) {
                $tempAdminMenu[] = require_once $file;
            }
        }
        if ($tempAdminMenu) {
            sort($tempAdminMenu);
            foreach ($tempAdminMenu as $key => $value) {
                   if (isset($tempAdminMenu[$key]['path'])) {
                       $tempAdminMenu[$key]['html'] = $this->display(file_get_contents(ROOT_PATH . $tempAdminMenu[$key]['path']));
                   }
            }
        }
        $this->assign('AdminMenu',$tempAdminMenu);
        
        
        $request = Request::instance();
//      try {
            if (input('model') != 'index.php' && input('model')) {
                if (input('action')) {
                    if ($model == 'addons' && $action != 'addons') {
                        $addonsAction = $addonsAction ? $addonsAction : $action;
                        $addonsNameSpace = "addons" . "\\" . $action . "\\" . "controller" . "\\" . $addonsCtr;
                        return model($addonsNameSpace)->$addonsAct();
                    } else {
                        return $this->fetch(input('model').'@admin' .'/' .input('action'));
                    }
                } else {
                    return $this->fetch('admin@' .'/'. input('model'));
                }
            } else {
                return $this->fetch('index@index');
            }
//      }catch(\Exception $e) {
//          $this->error('模板不存在','');
//      }
    }

}