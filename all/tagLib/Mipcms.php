<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace tagLib;
use think\template\TagLib;
use mip\Paginationm;
class Mipcms extends TagLib {
    
    protected $tags   =  [
        
        'articlecategory'      => ['attr' => 'pid,where,orderBy,order,value,key,limit,ids,notIds', 'close' => 1], 
        'articleinfo'      => ['attr' => 'table,cid,where,orderBy,order,value,keywords,key,limit,page,category,sub,uuids,notUuids,tagIds,tagNames', 'close' => 0],
        'article'      => ['attr' => 'table,cid,where,orderBy,order,value,key,keywords,limit,page,category,sub,ids,uuids,notUuids,tagIds,tagNames', 'close' => 1],
        
        'tagscategory'      => ['attr' => 'pid,where,orderBy,order,value,key,limit,ids,notIds', 'close' => 1], 
        'tags'      => ['attr' => 'table,cid,where,orderBy,order,value,key,limit,keywords,page,category,sub,articleIds,notIds', 'close' => 1],
        
        'productcategory'      => ['attr' => 'pid,where,orderBy,order,value,key,limit,ids,notIds', 'close' => 1], 
        'productinfo'      => ['attr' => 'table,cid,where,orderBy,order,value,keywords,key,limit,page,category,sub,uuids,notUuids,tagIds,tagNames', 'close' => 0],
        'product'      => ['attr' => 'table,cid,where,orderBy,order,value,key,keywords,limit,page,category,sub,ids,uuids,notUuids,tagIds,tagNames', 'close' => 1],
        
        'itemselect'      => ['attr' => 'table,where,orderBy,order,value,key,limit,page', 'close' => 1],
    ];
    
    public function tagArticlecategory($tag, $content)
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
        $html .= '$mipcms_db_list = model("app\article\model\Articles")->getCategory($mipcms_pid,$mipcms_orderBy,$mipcms_order,$mipcms_limit,$mipcms_where);';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_list" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
 
    public function tagArticle($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $cid = !empty($tag['cid']) ? $tag['cid'] : '';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'k';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '10';
        $page  = !empty($tag['page'])  ? $tag['page'] : '';
        $category  = !empty($tag['category'])  ? $tag['category'] : '';
        $sub  = !empty($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  !empty($tag['keywords']) ? $tag['keywords'] : '';
        $uuids =  !empty($tag['uuids']) ? $tag['uuids'] : '';
        $ids =  !empty($tag['ids']) ? $tag['ids'] : '';
        $notUuids =  !empty($tag['notUuids']) ? $tag['notUuids'] : '';
        $tagIds =  !empty($tag['tagIds']) ? $tag['tagIds'] : '';
        $tagNames =  !empty($tag['tagNames']) ? $tag['tagNames'] : '';
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
        $html .= '$mipcms_db_info = model("app\article\model\Articles")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames,$mipcms_ids);';
        if ($page) {
            $html .= '$pagination = model("app\article\model\Articles")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames,$mipcms_ids);';
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
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $cid = !empty($tag['cid']) ? $tag['cid'] : '';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'i';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '10';
        $page  = !empty($tag['page'])  ? $tag['page'] : '';
        $category  = !empty($tag['category'])  ? $tag['category'] : '';
        $sub  = !empty($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  !empty($tag['keywords']) ? $tag['keywords'] : '';
        $uuids =  !empty($tag['uuids']) ? $tag['uuids'] : '';
        $notUuids =  !empty($tag['notUuids']) ? $tag['notUuids'] : '';
        $tagIds =  !empty($tag['tagIds']) ? $tag['tagIds'] : '';
        $tagNames =  !empty($tag['tagNames']) ? $tag['tagNames'] : '';
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
        $html .= '$mipcms_db_list = model("tagLib\TagsModel")->getCategory($mipcms_pid,$mipcms_orderBy,$mipcms_order,$mipcms_limit,$mipcms_where);';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_list" id="' . $value . '" key="' . $key . '"}';
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
        $articleIds =  !empty($tag['articleIds']) ? $tag['articleIds'] : '';
        $notIds =  !empty($tag['notIds']) ? $tag['notIds'] : '';
        $html  = '<?php ';
        $html .= '$mipcms_table = "'.$table.'";';
        $html .= '$mipcms_cid = "'.$cid.'";';
        $html .= '$mipcms_where = "'.$where.'";';
        $html .= '$mipcms_order = "'.$order.'";';
        $html .= '$mipcms_orderBy = "'.$orderBy.'";';
        $html .= '$mipcms_limit = "'.$limit.'";';
        $html .= '$mipcms_page = "'.$page.'";';
        $html .= '$mipcms_category = "'.$category.'";';
        $html .= '$mipcms_sub = "'.$sub.'";';
        $html .= '$mipcms_keywords = "'.$keywords.'";';
        
        if (substr($articleIds, 0, 1) == '$') {
            $html .= '$mipcms_articleIds = '.$articleIds.';';
        } else {
            $html .= '$mipcms_articleIds = "'.$articleIds.'";';
        }
        
        $html .= '$mipcms_notIds = "'.$notIds.'";';
        
        
        $tempEmpty = '';
        $html .= '$mipcms_db_info = model("tagLib\TagsModel")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_articleIds);';
        if ($page) {
            $html .= '$pagination = model("tagLib\TagsModel")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_articleIds);';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_info" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
 
    //
    public function tagProductcategory($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $pid = !empty($tag['pid']) ? $tag['pid'] : '';
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
        $html .= '$mipcms_db_list = model("app\product\model\Product")->getCategory($mipcms_pid,$mipcms_orderBy,$mipcms_order,$mipcms_limit,$mipcms_where);';
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_list" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
 
    public function tagProduct($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $cid = !empty($tag['cid']) ? $tag['cid'] : '';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'i';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '10';
        $page  = !empty($tag['page'])  ? $tag['page'] : '';
        $category  = !empty($tag['category'])  ? $tag['category'] : '';
        $sub  = !empty($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  !empty($tag['keywords']) ? $tag['keywords'] : '';
        $uuids =  !empty($tag['uuids']) ? $tag['uuids'] : '';
        $notUuids =  !empty($tag['notUuids']) ? $tag['notUuids'] : '';
        $tagIds =  !empty($tag['tagIds']) ? $tag['tagIds'] : '';
        $tagNames =  !empty($tag['tagNames']) ? $tag['tagNames'] : '';
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
        $html .= '$mipcms_db_info = model("app\product\model\Product")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames);';
        if ($page) {
            $html .= '$pagination = model("app\product\model\Product")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames);';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="mipcms_db_info" id="' . $value . '" key="' . $key . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }

    public function tagProductinfo($tag, $content)
    {
        $table = !empty($tag['table']) ? $tag['table'] : '';
        $cid = !empty($tag['cid']) ? $tag['cid'] : '';
        $where = !empty($tag['where']) ? $tag['where'] : '';
        $order = !empty($tag['order']) ? $tag['order'] : 'desc';
        $orderBy = !empty($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $value = !empty($tag['value']) ? $tag['value'] : 'v';
        $key   = !empty($tag['key'])   ? $tag['key'] : 'i';
        $limit = !empty($tag['limit']) ? $tag['limit'] : '10';
        $page  = !empty($tag['page'])  ? $tag['page'] : '';
        $category  = !empty($tag['category'])  ? $tag['category'] : '';
        $sub  = !empty($tag['sub'])  ? $tag['sub'] : '';
        $keywords =  !empty($tag['keywords']) ? $tag['keywords'] : '';
        $uuids =  !empty($tag['uuids']) ? $tag['uuids'] : '';
        $notUuids =  !empty($tag['notUuids']) ? $tag['notUuids'] : '';
        $tagIds =  !empty($tag['tagIds']) ? $tag['tagIds'] : '';
        $tagNames =  !empty($tag['tagNames']) ? $tag['tagNames'] : '';
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
        $html .= '$mipcms_info = model("app\product\model\Product")->getItemList($mipcms_cid,$mipcms_page,$mipcms_limit,$mipcms_orderBy,$mipcms_order,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames);';
        if ($page) {
            $html .= '$pagination = model("app\product\model\Product")->getPaginationm($mipcms_cid,$mipcms_limit,$mipcms_category,$mipcms_sub,$mipcms_where,$mipcms_keywords,$mipcms_uuids,$mipcms_notUuids,$mipcms_tagIds,$mipcms_tagNames);';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= '$'. $value .' = $mipcms_info; ?>';
        return $html;
    }

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
