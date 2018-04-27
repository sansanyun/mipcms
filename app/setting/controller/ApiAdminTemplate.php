<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\setting\controller;
use think\Request;
use think\Loader;
use mip\Htmlp;

use mip\AdminBase;
class ApiAdminTemplate extends AdminBase
{
    public function index()
    {
        
    }
    public function templateList()
    {
        $templateDir = opendir(ROOT_PATH . 'template');
        if ($templateDir) {
            while (false !== ($file = readdir($templateDir))) {
                if (substr($file, 0, 1) != '.' AND is_dir(ROOT_PATH . 'template' . DS . $file)) {
                    $dirs[] = $file;
                }
            }
            closedir($templateDir);
        }

        $templateArray = array();
        foreach ($dirs as $key => $val) {
            if (is_file(ROOT_PATH . 'template' . DS . $val . DS . 'template.json')) {
                $templateJson = file_get_contents(ROOT_PATH . 'template' . DS . $val . DS . 'template.json');
                $templateArray[] = array('fileName' => $val,'info' => json_decode($templateJson));
            }
        }
 
        return jsonSuccess('',$templateArray);
    }
     
    public function templateSave() {
        if (Request::instance()->isPost()) {
            $fileName = input('post.fileName');
            db($this->settings)->where('key','template')->update(array('val' => $fileName));
            return jsonSuccess('保存成功');
        }
    }
}