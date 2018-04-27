<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\view\controller;
use mip\Mip;
class View extends Mip
{
    public function index()
    {
        $name = input('name');
        $dir = input('dir');
        $params = input('params');
        $this->assign('name',$name);
        $this->assign('dir',$dir);
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
        
        $this->assign('mipTitle','');
        
        $this->assign('mipKeywords','');
        
        $this->assign('mipDescription','');
        
        if ($dir) {
            return $this->mipView('view/' . $dir . '/' . $name);
        } else {
            return $this->mipView('view/' . $name);
        }
    }
 
    
}
