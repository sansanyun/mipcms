<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\common;
use think\template\TagLib;
use mip\Paginationm;
class Mipitem extends TagLib {
    
    protected $tags   =  [
        'itemselect'      => ['attr' => 'table,where,orderBy,order,value,key,limit,page', 'close' => 1],
    ];

    public function tagItemselect($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $page = !empty($tag['page']) ? $tag['page'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'asc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'sort';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'i';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $html  = '<?php ';
        
        if (substr($table, 0, 1) == '$') {
            $html .= '$mipcms_table = '.$table.';';
        } else {
            $html .= '$mipcms_table = "'.$table.'";';
        }
         
         if (strpos($where,"$")) {
            $html .= '$mipcms_where = '.$where.';';
        } else {
            $html .= '$mipcms_where = "'.$where.'";';
        }
        if (substr($order, 0, 1) == '$') {
            $html .= '$mipcms_order = '.$order.';';
        } else {
            $html .= '$mipcms_order = "'.$order.'";';
        }
        if (substr($orderBy, 0, 1) == '$') {
            $html .= '$mipcms_orderBy = '.$orderBy.';';
        } else {
            $html .= '$mipcms_orderBy = "'.$orderBy.'";';
        }
        if (substr($limit, 0, 1) == '$') {
            $html .= '$mipcms_limit = '.$limit.';';
        } else {
            $html .= '$mipcms_limit = "'.$limit.'";';
        }
        if (substr($page, 0, 1) == '$') {
            $html .= '$mipcms_page = '.$page.';';
        } else {
            $html .= '$mipcms_page = "'.$page.'";';
        }
        $html .= '$mipcms_db_list = db($mipcms_table)->where($mipcms_where)->page($mipcms_page,$mipcms_limit)->order($mipcms_orderBy,$mipcms_order)->select();';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_list" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
     
}
