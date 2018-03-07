<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\tag\model;

use mip\Init;
class ItemTags extends Init
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
            db($this->itemTags)->where('item_id',$itemId)->where('item_type',$itemType)->delete();
            foreach ($tags as $name) {
                if ($name) {
                    $tagInfo = db($this->tags)->where('name',$name)->find();
                    if (!$tagInfo) {
                        db($this->tags)->insert(array(
                            'id' => uuid(),
                            'name' => $name,
                            'item_type' => $itemType,
                        ));
                        $tagInfo = db($this->tags)->where('name',$name)->find();
                    }
                    db($this->itemTags)->insert(array(
                        'id' => uuid(),
                        'tags_id'=>$tagInfo['id'],
                        'item_id' => $itemId,
                        'item_type' => $itemType,
                        'item_add_time' => $publish_time,
                    ));
                    $tagsCount = db($this->itemTags)->where('tags_id',$tagInfo['id'])->count();
                    if ($tagsCount) {
                        db($this->tags)->where('id',$tagInfo['id'])->update(array(
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