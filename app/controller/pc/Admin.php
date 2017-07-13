<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\pc;
use mip\AdminBase;
use think\Request;
class Admin extends AdminBase
{
	protected $beforeActionList = ['start'];

    public function start() {
    $this->assign('mipInfoToJson',json_encode($this->mipInfo));
        if (!$this->userId) {
            exit;
        }
    }
    public function index() {
        $model = input('model');
        $this->model = $model;
        $this->assign('model',$model);
        $templateDir = opendir(ROOT_PATH . 'template' . DS . 'pc');
        if ($templateDir) {
            while (false !== ($file = readdir($templateDir))) {
                if (substr($file, 0, 1) != '.' AND is_dir(ROOT_PATH . 'template' . DS . 'pc' . DS . $file)) {
                    $dirs[] = $file;
                }
            }
            closedir($templateDir);
        }

        $templateArray = array();
        foreach ($dirs as $key => $val) {
            $templateArray[] = $val;
        }
        $this->assign('templateArray',json_encode($templateArray));

        $mipTemplateDir = opendir(ROOT_PATH . 'template' . DS . 'm');
        if ($mipTemplateDir) {
            while (false !== ($file = readdir($mipTemplateDir))) {
                if (substr($file, 0, 1) != '.' AND is_dir(ROOT_PATH . 'template' . DS . 'm' . DS . $file)) {
                    $mipDirs[] = $file;
                }
            }
            closedir($mipTemplateDir);
        }
        $mipTemplateArray = array();
        foreach ($mipDirs as $key => $val) {
            $mipTemplateArray[] = $val;
        }
        $this->assign('mipTemplateArray',json_encode($mipTemplateArray));

        $this->assign('todayUserCount',db('Spiders')->where('add_time','>',strtotime(date('Y-m-d')))->count());
        $this->assign('yesterdayUserCount',db('Spiders')->where('add_time>'.strtotime(date('Y-m-'.(date('d')-1))).' AND add_time < ' . strtotime(date('Y-m-d')))->count());
        $this->assign('allUserCount',db('Spiders')->count());
        $request = Request::instance();
        if (input('model') != 'index.php') {
            return $this->fetch('/@admin/' . input('model'));
        } else {
            return $this->fetch('/@admin/index');
        }
    }

}