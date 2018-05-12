<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\tag\model;

use think\Controller;
class ItemTags extends Controller
{
    public function tags() {
        return $this->hasOne('Tags','id','tags_id');
    }

    public function innerTags($tags, $itemType, $itemInfo) {
        $itemId = $itemInfo['uuid'];
        $itemUid = $itemInfo['uid'];
        $publish_time = $itemInfo['publish_time'] ? $itemInfo['publish_time'] : time();
        if (!is_array($tags)) {
            return false;
        }
        if (is_array($tags)) {
            db('ItemTags')->where('item_id',$itemId)->delete();
            foreach ($tags as $name) {
                if ($name) {
                    $tagInfo = db('Tags')->where('name',$name)->find();
                    if (!$tagInfo) {
                        db('Tags')->insert(array(
                            'id' => uuid(),
                            'name' => $name,
                        ));
                        $tagInfo = db('Tags')->where('name',$name)->find();
                    }
                    db('ItemTags')->insert(array(
                        'id' => uuid(),
                        'tags_id'=>$tagInfo['id'],
                        'item_id' => $itemId,
                        'item_add_time' => $publish_time,
                    ));
                    $tagsCount = db('ItemTags')->where('tags_id',$tagInfo['id'])->count();
                    if ($tagsCount) {
                        db('Tags')->where('id',$tagInfo['id'])->update(array(
                            'relevance_num' => $tagsCount,
                            'creator_uid' => $itemUid,
                            'add_time' => time(),
                        ));
                    }
                }
            }
        }
        return true;
    }

}