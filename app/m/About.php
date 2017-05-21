<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\m;
use mip\Mip;
class About extends Mip
{
    public function index() {
        
        return $this->mipView('m/about/about');
    }
    
}