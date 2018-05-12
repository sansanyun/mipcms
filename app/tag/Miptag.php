<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\tag;
use think\template\TagLib;
use mip\Paginationm;
class Miptag extends TagLib {
    
    protected $tags   =  [
        'tagscategory'      => ['attr' => 'pid,where,orderBy,order,value,key,limit,ids,notIds', 'close' => 1], 
        'tags'      => ['attr' => 'table,cid,where,orderBy,order,value,key,limit,keywords,page,category,sub,itemIds,notIds,tagIds', 'close' => 1],
    ];
    
    public function tagTagscategory($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $pid = !empty($tag['pid']) ? $tag['pid'] : 0;
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'asc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'sort';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'i';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $html  = '<?php ';
        $html .= '$mipcms_table = "'.$table.'";';
        $html .= '$mipcms_pid = "'.$pid.'";';
        $html .= '$mipcms_where = "'.$where.'";';
        $html .= '$mipcms_order = "'.$order.'";';
        $html .= '$mipcms_orderBy = "'.$orderBy.'";';
        $html .= '$mipcms_limit = "'.$limit.'";';
        $html .= '$mipcms_tagscategory_list = model("app\common\model\Tags")->getCategory($mipcms_pid,$mipcms_orderBy,$mipcms_order,$mipcms_limit,$mipcms_where);';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_tagscategory_list" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
    public function tagTags($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $cid = !empty($tag['cid']) ? $tag['cid'] : '';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'add_time';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'i';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '20';
        $page  = !empty($tag['page'])  ? $tag['page'] : '';
        $category  = !empty($tag['category'])  ? $tag['category'] : '';
        $sub  = !empty($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  !empty($tag['keywords']) ? $tag['keywords'] : '';
        $itemIds =  !empty($tag['itemIds']) ? $tag['itemIds'] : '';
        $notIds =  !empty($tag['notIds']) ? $tag['notIds'] : '';
        $html  = '<?php ';
        $html .= '$mipcms_table = "'.$table.'";';
        if (substr($cid, 0, 1) == '$') {
            $html .= '$mipcms_cid = '.$cid.';';
        } else {
            $html .= '$mipcms_cid = "'.$cid.'";';
        }
        $html .= '$mipcms_where = "'.$where.'";';
        $html .= '$mipcms_order = "'.$order.'";';
        $html .= '$mipcms_orderBy = "'.$orderBy.'";';
        $html .= '$mipcms_limit = "'.$limit.'";';
        $html .= '$mipcms_page = "'.$page.'";';
        $html .= '$mipcms_category = "'.$category.'";';
        $html .= '$mipcms_sub = "'.$sub.'";';
        $html .= '$mipcms_keywords = "'.$keywords.'";';
        
        if (substr($itemIds, 0, 1) == '$') {
            $html .= '$mipcms_itemIds = '.$itemIds.';';
        } else {
            $html .= '$mipcms_itemIds = "'.$itemIds.'";';
        }

        
        $html .= '$mipcms_notIds = "'.$notIds.'";';
        
        $tempEmpty = '';
        $html .= '$mipcms_tag_info = model("app\\common\\model\\Tags")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_itemIds);';
        if ($page) {
            $html .= '$tagpagination = model("app\\common\\model\\Tags")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_itemIds);';
        } else {
            $html .= '$tagpagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="mipcms_tag_info" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }

}
