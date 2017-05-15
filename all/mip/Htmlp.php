<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use HTMLPurifier;
use HTMLPurifier_Config;
class Htmlp
{
    static public function htmlp($dirty_html){
       $config = HTMLPurifier_Config::createDefault();
       $purifier = new HTMLPurifier($config);
       return $purifier->purify($dirty_html);
   }

}