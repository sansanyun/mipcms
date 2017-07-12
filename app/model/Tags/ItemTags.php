<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.

namespace app\model\Tags;

use think\Model;

class ItemTags extends Model
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
            $this->where('item_id',$itemId)->where('item_type',$itemType)->delete();
            foreach ($tags as $name) {
                if ($name) {
                    $tagInfo = Tags::where('name',$name)->find();
                    if (!$tagInfo) {
                        $tagInfo =  Tags::create(array(
                            'id' => uuid(),
                            'name' => $name,
                            'item_type' => $itemType,
                        ));
                    }
                    $this->create(array(
                        'id' => uuid(),
                        'tags_id'=>$tagInfo['id'],
                        'item_id' => $itemId,
                        'item_type' => $itemType,
                        'item_add_time' => $publish_time,
                    ));
                    $tagsCount = $this->where('tags_id',$tagInfo['id'])->count();
                    if ($tagsCount) {
                        Tags::where('id',$tagInfo['id'])->update(array(
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