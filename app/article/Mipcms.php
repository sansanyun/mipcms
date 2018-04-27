<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article;
use think\template\TagLib;
use mip\Paginationm;
class Mipcms extends TagLib {
    
    protected $tags   =  [
        
        'articlecategory'      => ['attr' => 'pid,where,orderBy,order,value,key,limit,ids,type', 'close' => 1], 
        'articleinfo'      => ['attr' => 'table,cid,where,orderBy,order,value,keywords,key,limit,page,category,sub,uuids,notUuids,tagIds,tagNames', 'close' => 0],
        'article'      => ['attr' => 'table,cid,where,orderBy,order,value,key,keywords,limit,page,category,sub,ids,uuids,notUuids,tagIds,tagNames,itemId,type', 'close' => 1],
        'page' => ['attr' => 'itemId,value,key,limit,type,itemType,page,prePage', 'close' => 1],
        'crumb' => ['attr' => 'cid,ulClass,liClass,isHome,separator', 'close' => 0],
        
    ];
    
    public function tagCrumb($tag, $content)
    {
        $cid = isset($tag['cid']) ? $tag['cid'] : '';
        $value = isset($tag['value']) ? $tag['value'] : 'v';
        $key   = isset($tag['key'])   ? $tag['key'] : 'i';
        $ulClass = isset($tag['ulClass']) ? $tag['ulClass'] : 'mipcms-crumb';
        $liClass = isset($tag['liClass']) ? $tag['liClass'] : '';
        $isHome = isset($tag['isHome']) ? $tag['isHome'] : 1;
        $separator = isset($tag['separator']) ? $tag['separator'] : '';
        $html  = '<?php ';
        $html .= substr($cid, 0, 1) == '$' ? '$mipcms_cid = '.$cid.';' : '$mipcms_cid = "'.$cid.'";';
        $html .= substr($ulClass, 0, 1) == '$' ? '$mipcms_ulClass = '.$ulClass.';' : '$mipcms_ulClass = "'.$ulClass.'";';
        $html .= substr($liClass, 0, 1) == '$' ? '$mipcms_liClass = '.$liClass.';' : '$mipcms_liClass = "'.$liClass.'";';
        $html .= substr($isHome, 0, 1) == '$' ? '$mipcms_isHome = '.$isHome.';' : '$mipcms_isHome = "'.$isHome.'";';
        $html .= substr($separator, 0, 1) == '$' ? '$mipcms_separator = '.$separator.';' : '$mipcms_separator = "'.$separator.'";';
        $html .= '$mipcms_info = model("app\article\model\Articles")->getCrumb($mipcms_cid,$mipcms_ulClass,$mipcms_liClass,$mipcms_isHome,$mipcms_separator);';
        $html .= 'echo $mipcms_info; ?>';
        return $html;
    }
    public function tagPage($tag, $content)
    {
        $itemId = isset($tag['itemId']) ? $tag['itemId'] : '';
        $value = isset($tag['value']) ? $tag['value'] : 'v';
        $key   = isset($tag['key'])   ? $tag['key'] : 'i';
        $limit = isset($tag['limit']) ? $tag['limit'] : '';
        $type = isset($tag['type']) ? $tag['type'] : '';
        $itemType = isset($tag['itemType']) ? $tag['itemType'] : '';
        $page = isset($tag['page']) ? $tag['page'] : 1;
        $prePage = isset($tag['prePage']) ? $tag['prePage'] : 10;
        
        $html  = '<?php ';
        $html .= substr($itemId, 0, 1) == '$' ? '$mipcms_itemId = '.$itemId.';' : '$mipcms_itemId = "'.$itemId.'";';
        $html .= substr($limit, 0, 1) == '$' ? '$mipcms_limit = '.$limit.';' : '$mipcms_limit = "'.$limit.'";';
        $html .= substr($type, 0, 1) == '$' ? '$mipcms_type = '.$type.';' : '$mipcms_type = "'.$type.'";';
        $html .= substr($itemType, 0, 1) == '$' ? '$mipcms_itemType = '.$itemType.';' : '$mipcms_itemType = "'.$itemType.'";';
        $html .= substr($page, 0, 1) == '$' ? '$mipcms_page = '.$page.';' : '$mipcms_page = "'.$page.'";';
        $html .= substr($prePage, 0, 1) == '$' ? '$mipcms_prePage = '.$prePage.';' : '$mipcms_prePage = "'.$prePage.'";';
        
        $html .= '$mipcms_db_list = model("app\article\model\Articles")->getPage($mipcms_itemId,$mipcms_limit,$mipcms_type,$mipcms_itemType,$mipcms_page,$mipcms_prePage);';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_list" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    public function tagArticlecategory($tag, $content)
    {
        $table = isset($tag['table']) ? $tag['table'] : '';
        $pid = isset($tag['pid']) ? $tag['pid'] : 0;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : 'asc';
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'sort';
        $value = isset($tag['value']) ? $tag['value'] : 'v';
        $key   = isset($tag['key'])   ? $tag['key'] : 'i';
        $limit = isset($tag['limit']) ? $tag['limit'] : '';
        $ids = isset($tag['ids']) ? $tag['ids'] : '';
        $type = isset($tag['type']) ? $tag['type'] : '';
        $html  = '<?php ';
        $html .= '$mipcms_table = "'.$table.'";';
        $html .= '$mipcms_pid = "'.$pid.'";';
        $html .= '$mipcms_where = "'.$where.'";';
        $html .= '$mipcms_order = "'.$order.'";';
        $html .= '$mipcms_orderBy = "'.$orderBy.'";';
        $html .= '$mipcms_limit = "'.$limit.'";';
        $html .= '$mipcms_ids = "'.$ids.'";';
        $html .= '$mipcms_type = "'.$type.'";';
        
        $html .= '$mipcms_db_list = model("app\article\model\Articles")->getCategory($mipcms_pid,$mipcms_orderBy,$mipcms_order,$mipcms_limit,$mipcms_where,$mipcms_ids,$mipcms_type);';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_list" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
 
    public function tagArticle($tag, $content)
    {
        $table = isset($tag['table']) ? $tag['table'] : '';
        $cid = isset($tag['cid']) ? $tag['cid'] : '';
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $value = isset($tag['value']) ? $tag['value'] : 'v';
        $key   = isset($tag['key'])   ? $tag['key'] : 'k';
        $limit = isset($tag['limit']) ? $tag['limit'] : '10';
        $page  = isset($tag['page'])  ? $tag['page'] : '';
        $category  = isset($tag['category'])  ? $tag['category'] : '';
        $sub  = isset($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  isset($tag['keywords']) ? $tag['keywords'] : '';
        $uuids =  isset($tag['uuids']) ? $tag['uuids'] : '';
        $ids =  isset($tag['ids']) ? $tag['ids'] : '';
        $notUuids =  isset($tag['notUuids']) ? $tag['notUuids'] : '';
        $tagIds =  isset($tag['tagIds']) ? $tag['tagIds'] : '';
        $tagNames =  isset($tag['tagNames']) ? $tag['tagNames'] : '';
        
        $itemId =  isset($tag['itemId']) ? $tag['itemId'] : '';
        $type =  isset($tag['type']) ? $tag['type'] : '';
        
        
        $html  = '<?php ';
        if (substr($table, 0, 1) == '$') {
            $html .= '$mipcms_table = '.$table.';';
        } else {
            $html .= '$mipcms_table = "'.$table.'";';
        }
        if (substr($cid, 0, 1) == '$') {
            $html .= '$mipcms_cid = '.$cid.';';
        } else {
            $html .= '$mipcms_cid = "'.$cid.'";';
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
        if (substr($category, 0, 1) == '$') {
            $html .= '$mipcms_category = '.$category.';';
        } else {
            $html .= '$mipcms_category = "'.$category.'";';
        }
        if (substr($sub, 0, 1) == '$') {
            $html .= '$mipcms_sub = '.$sub.';';
        } else {
            $html .= '$mipcms_sub = "'.$sub.'";';
        }
        if (substr($keywords, 0, 1) == '$') {
            $html .= '$mipcms_keywords = '.$keywords.';';
        } else {
            $html .= '$mipcms_keywords = "'.$keywords.'";';
        }
        if (substr($uuids, 0, 1) == '$') {
            $html .= '$mipcms_uuids = '.$uuids.';';
        } else {
            $html .= '$mipcms_uuids = "'.$uuids.'";';
        }
        if (substr($ids, 0, 1) == '$') {
            $html .= '$mipcms_ids = '.$ids.';';
        } else {
            $html .= '$mipcms_ids = "'.$ids.'";';
        }
        if (substr($notUuids, 0, 1) == '$') {
            $html .= '$mipcms_notUuids = '.$notUuids.';';
        } else {
            $html .= '$mipcms_notUuids = "'.$notUuids.'";';
        }
        if (substr($tagIds, 0, 1) == '$') {
            $html .= '$mipcms_tagIds = '.$tagIds.';';
        } else {
            $html .= '$mipcms_tagIds = "'.$tagIds.'";';
        }
        if (substr($tagNames, 0, 1) == '$') {
            $html .= '$mipcms_tagNames = '.$tagNames.';';
        } else {
            $html .= '$mipcms_tagNames = "'.$tagNames.'";';
        }
        
        if (substr($itemId, 0, 1) == '$') {
            $html .= '$mipcms_itemId = '.$itemId.';';
        } else {
            $html .= '$mipcms_itemId = "'.$itemId.'";';
        }
        if (substr($type, 0, 1) == '$') {
            $html .= '$mipcms_type = '.$type.';';
        } else {
            $html .= '$mipcms_type = "'.$type.'";';
        }
        $html .= '$mipcms_db_info = model("app\article\model\Articles")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames,$mipcms_ids,$mipcms_itemId,$mipcms_type);';
        if ($page) {
            $html .= '$pagination = model("app\article\model\Articles")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames,$mipcms_ids,$mipcms_itemId,$mipcms_type);';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_info" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }

    public function tagArticleinfo($tag, $content)
    {
        $table = isset($tag['table']) ? $tag['table'] : '';
        $cid = isset($tag['cid']) ? $tag['cid'] : '';
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $value = isset($tag['value']) ? $tag['value'] : 'v';
        $key   = isset($tag['key'])   ? $tag['key'] : 'i';
        $limit = isset($tag['limit']) ? $tag['limit'] : '10';
        $page  = isset($tag['page'])  ? $tag['page'] : '';
        $category  = isset($tag['category'])  ? $tag['category'] : '';
        $sub  = isset($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  isset($tag['keywords']) ? $tag['keywords'] : '';
        $uuids =  isset($tag['uuids']) ? $tag['uuids'] : '';
        $notUuids =  isset($tag['notUuids']) ? $tag['notUuids'] : '';
        $tagIds =  isset($tag['tagIds']) ? $tag['tagIds'] : '';
        $tagNames =  isset($tag['tagNames']) ? $tag['tagNames'] : '';
        $html  = '<?php ';
        if (substr($table, 0, 1) == '$') {
            $html .= '$mipcms_table = '.$table.';';
        } else {
            $html .= '$mipcms_table = "'.$table.'";';
        }
        if (substr($cid, 0, 1) == '$') {
            $html .= '$mipcms_cid = '.$cid.';';
        } else {
            $html .= '$mipcms_cid = "'.$cid.'";';
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
        if (substr($category, 0, 1) == '$') {
            $html .= '$mipcms_category = '.$category.';';
        } else {
            $html .= '$mipcms_category = "'.$category.'";';
        }
        if (substr($sub, 0, 1) == '$') {
            $html .= '$mipcms_sub = '.$sub.';';
        } else {
            $html .= '$mipcms_sub = "'.$sub.'";';
        }
        if (substr($keywords, 0, 1) == '$') {
            $html .= '$mipcms_keywords = '.$keywords.';';
        } else {
            $html .= '$mipcms_keywords = "'.$keywords.'";';
        }
        if (substr($uuids, 0, 1) == '$') {
            $html .= '$mipcms_uuids = '.$uuids.';';
        } else {
            $html .= '$mipcms_uuids = "'.$uuids.'";';
        }
        if (substr($notUuids, 0, 1) == '$') {
            $html .= '$mipcms_notUuids = '.$notUuids.';';
        } else {
            $html .= '$mipcms_notUuids = "'.$notUuids.'";';
        }
        if (substr($tagIds, 0, 1) == '$') {
            $html .= '$mipcms_tagIds = '.$tagIds.';';
        } else {
            $html .= '$mipcms_tagIds = "'.$tagIds.'";';
        }
        if (substr($tagNames, 0, 1) == '$') {
            $html .= '$mipcms_tagNames = '.$tagNames.';';
        } else {
            $html .= '$mipcms_tagNames = "'.$tagNames.'";';
        }
        $html .= '$mipcms_info = model("app\article\model\Articles")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames);';
        if ($page) {
            $html .= '$pagination = model("app\article\model\Articles")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames);';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= '$'. $value .' = $mipcms_info; ?>';
        return $html;
    }
  
     
}
