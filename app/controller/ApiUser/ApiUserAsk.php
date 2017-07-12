<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\controller\ApiUser;
use app\model\Asks\Asks;
use app\model\Asks\AsksCategory;
use app\model\Asks\AsksContent;
use app\model\Asks\AsksAnswers;
use app\model\Asks\AsksAnswersComments;
use app\model\Users\Users;
use app\model\Tags\Tags;
use app\model\Tags\ItemTags;
use think\Request;
use think\Loader;
use mip\Htmlp;
use mip\AuthBase;
class ApiUserAsk extends AuthBase
{
    public function index(){

    }

    public function askAdd(Request $request) {
        if (Request::instance()->isPost()) {

            $title = Htmlp::htmlp(input('post.title'));
            $content = Htmlp::htmlp(input('post.content'));
            $cid = input('post.cid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time') ? input('post.publish_time') : time();;
            $itemType = 'ask';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            if ($tags) {
                $tags = explode(',',$tags);
            }
            if (!$title) {
              return jsonError('请输入标题');
            }
            if (!$cid) {
                $cid = 0;
            }
            if (Asks::where('uid',$this->userId)->where('publish_time','>',strtotime(date('Y-m-d')))->count() >= $this->askSetting['askPublishUserNumDay']) {
                return jsonError('你提问的次数已达到当天限定的数量，请明天进行提问');
            }
            $userItemInfo = Asks::where('uid',$this->userId)->order('publish_time', 'desc')->limit(1)->select();
            if ($userItemInfo) {
                if ($userItemInfo[0]['publish_time'] > time()-($this->askSetting['askPublishUserTime']*60)) {
                    $tempTime = intval($userItemInfo[0]['publish_time'])+(intval($this->askSetting['askPublishUserTime'])*60) - intval(time());
                    return jsonError('提问过快，请'. $tempTime .'秒后进行提问');
                }
            }
            $asksInfo = Asks::where('title',$title)->find();
            if ($asksInfo) {
                return jsonError('该标题已存在，请换个标题');
            } else {
                $createInfo = Asks::create(array(
                   'title'=>htmlspecialchars($title),
                   'uid' => $this->userId,
                   'cid' => $cid,
                   'create_time' => time(),
                   'publish_time' => $publish_time,
                   'uuid' => uuid(),
                   'is_recommend' => $is_recommend,
                   'content_id' => uuid(),
                    ));
                if ($createInfo) {
                    AsksContent::create(array(
                       'id' => $createInfo['content_id'],
                       'content' => htmlspecialchars($content),
                    ));
                    if ($tags) {
                        model('app\model\tags\ItemTags')->innerTags($tags, $itemType, $createInfo);
                    }
                    Users::where('uid',$this->userId)->update([
                        'ask_num' => Asks::where('uid',$this->userId)->count(),
                    ]);
                    return jsonSuccess('发布成功',$this->mipInfo['idStatus'] ? $createInfo->uuid : $createInfo->id);
                } else {
                    return  jsonError('提交失败');
                }
            }

        }
    }
    /*
     *查询用户发布
     */
    public function askSelectByUserInfo(Request $request) {
        if (Request::instance()->isPost()) {
            $page = input('post.page');
            $limit = input('post.limit');
            $orderBy = input('post.orderBy');
            $order = input('post.order');
            $uid = $this->userId;
            if (!$page) {
                $page = 1;
            }
            if (!$limit) {
                $limit = 10;
            }
            if (!$orderBy) {
                $orderBy = 'publish_time';
            }
            if (!$order) {
                $order = 'desc';
            }
            $where['uid'] = $uid;
            $itemList = Asks::where('uid',$uid)->limit($limit)->page($page)->order($orderBy, $order)->select();
            $total = Asks::where('uid',$uid)->count();
            if ($itemList) {
                foreach ($itemList as $key => $val) {
                    $val['content'] = htmlspecialchars_decode($itemList[$key]->getContentByAskId($val['id'],$val['content_id'])['content']);
                    $itemList[$key]->users;
                    $itemList[$key]->asksCategory;
                    $itemList[$key]['url'] = $itemList[$key]->domainUrl($val);
                }
            }
            return jsonSuccess('',['itemList' => $itemList,'total' => $total,'page' => $page]);
        }
    }

    public function answerAdd(Request $request) {
        if (Request::instance()->isPost()) {

            $itemId = input('post.itemId');
            $content = Htmlp::htmlp(input('post.content'));

            if (!$itemId) {
                return jsonError('缺少参数');
            }
            if (!$content) {
              return jsonError('请输入内容');
            }

            if (AsksAnswers::where('uid',$this->userId)->where('item_id',$itemId)->count() >= $this->askSetting['answerUserNum']) {
                return jsonError('这个问题你已经回答过了');
            }

            $itemInfo = Asks::where('uuid',$itemId)->find();
            if (!$itemInfo) {
                return jsonError('问答不存在');
            } else {
                $asksAnswerInfo = AsksAnswers::create(array(
                    'id' => uuid(),
                    'item_id' => $itemId,
                    'uid' => $this->userId,
                    'content' => htmlspecialchars($content),
                    'create_time'=>time(),
                    'edit_time'=>time()
                ));
                if ($asksAnswerInfo) {
                    $itemCount = AsksAnswers::where('item_id',$itemId)->count();
                    Asks::where('uuid',$itemId)->update(array('answer_num' => $itemCount));
                    Users::where('uid',$this->userId)->update([
                        'ask_answers_num' => AsksAnswers::where('uid',$this->userId)->count(),
                    ]);
                    return jsonSuccess('回答成功');
                }else{
                    return  jsonError('添加失败');
                }
            }

        }
    }

     public function answerCommentAdd(Request $request) {
        if (Request::instance()->isPost()) {

            $itemId = input('post.itemId');
            $answerId = input('post.answerId');
            $isReply = input('post.isReply');
            $replyUid = input('post.replyUid');
            $replyItemId = input('post.replyItemId');
            if (!$isReply) {
                $isReply = 0;
            }
            if (!$replyUid) {
                $replyUid = 0;
            }
            $answerCommentContent = Htmlp::htmlp(input('post.answerCommentContent'));
            if (!$itemId || !$answerId) {
                return jsonError('缺少参数');
            }
            if (!$answerCommentContent) {
              return jsonError('请输入内容');
            }
            $itemInfo = Asks::where('uuid',$itemId)->find();
            if (!$itemInfo) {
                return jsonError('问题不存在');
            } else {
                $asksAnswersCommentsInfo = AsksAnswersComments::create(array(
                    'id' => uuid(),
                    'item_id' => $answerId,
                    'uid' => $this->userId,
                    'content' => htmlspecialchars($answerCommentContent),
                    'create_time' => time(),
                    'edit_time' => time(),
                    'is_reply' => $isReply,
                    'reply_uid' => $replyUid,
                    'reply_item_id' => $replyItemId,
                ));
                Asks::where('id',$itemId)->update([
                    'answer_num' => AsksAnswers::where('item_id',$itemId)->count()
                ]);
                if ($asksAnswersCommentsInfo) {
                    return jsonSuccess('评论成功');
                }else{
                    return  jsonError('评论失败');
                }
            }

        }
    }
    public function answerCommentSelect(Request $request) {
        if (Request::instance()->isPost()) {

            $itemId = input('post.itemId');
            $answerId = input('post.answerId');
            $page = input('post.page');
            $limit = input('post.limit');
            $orderBy = input('post.orderBy');
            $order = input('post.order');
            if (!$itemId || !$answerId) {
                return jsonError('缺少参数');
            }
            if (!$page) {
              $page = 1;
            }
            if (!$limit) {
              $limit = 10;
            }
            if (!$orderBy) {
             $orderBy = 'create_time';
            }
            if (!$order) {
                $order = 'asc';
            }
            $itemInfo = Asks::where('uuid',$itemId)->find();
            if (!$itemInfo) {
                return jsonError('问题不存在');
            } else {
                $asksAnswersCommentsList = AsksAnswersComments::where('item_id',$answerId)->limit($limit)->page($page)->order($orderBy, $order)->select();
                if ($asksAnswersCommentsList) {
                    foreach ($asksAnswersCommentsList as $k => $v) {
                        $asksAnswersCommentsList[$k]->users;
                        $asksAnswersCommentsList[$k]->avatar = getAvatarUrl($v['uid']);
                        $asksAnswersCommentsList[$k]->replyUsers;
                        if ($v['is_reply'] == 1) {
                            $asksAnswersCommentsList[$k]['replyInfo'] = AsksAnswersComments::where('id',$v['reply_item_id'])->find();
                        } else {
                            $asksAnswersCommentsList[$k]['replyInfo'] = null;
                        }
                        $asksAnswersCommentsList[$k]['answerCommentReplyStatus'] = false;
                    }
                }
                $total = AsksAnswersComments::where('item_id',$answerId)->count();
                return jsonSuccess('ok',['asksAnswersCommentsList' => $asksAnswersCommentsList,'total' => $total,'page' => $page]);
            }

        }
    }

    public function askDel(Request $request) {
        if (Request::instance()->isPost()) {
            $itemId = input('post.itemId');
            if (!$itemId) {
                return jsonError('缺少ID');
            }
            $askInfo = Asks::where('uuid',$itemId)->find();
            if (!$askInfo) {
                return jsonError('问题不存在');
            }
            if ($this->userId == $askInfo['uid'] || $this->isAdmin) {
                Asks::where('uuid',$itemId)->delete();
               $asksAnswersList = AsksAnswers::where('item_id',$askInfo['uuid'])->select();
               if ($asksAnswersList) {
                   foreach ($asksAnswersList as $k => $v) {
                        AsksAnswersComments::where('item_id',$v['id'])->delete();
                   }
                   foreach ($asksAnswersList as $k => $v) {
                        $asksAnswersList[$k]->delete();
                   }
               }

                return jsonSuccess('删除成功');
            } else {
                return jsonError('无权限操作');
            }
        }
    }
    public function AskInfo(Request $request) {
        if (Request::instance()->isPost()) {
             $itemId = input('post.itemId');
             if (!$itemId) {
                return jsonError('缺少ID');
            }
            $itemInfo = Asks::where('uuid',$itemId)->find();
            $itemInfo['content'] = htmlspecialchars_decode($itemInfo->getContentByAskId($itemInfo['id'],$itemInfo['content_id'])['content']);
            $itemInfo['userAnswerStatus'] = AsksAnswers::where('uid',$this->userId)->where('item_id',$itemId)->count() >= $this->askSetting['answerUserNum'];
            if (!$itemInfo) {
                return jsonError('问题不存在');
            }
            return jsonSuccess('ok',['itemInfo' => $itemInfo]);
        }
    }

    public function askEdit(Request $request)
    {
        if (Request::instance()->isPost()) {

            $id = input('post.id');
            $title = Htmlp::htmlp(input('post.title'));
            $content = Htmlp::htmlp(input('post.content'));
            $cid = input('post.cid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time');
            $itemType = 'ask';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            $tags = explode(',',$tags);
            if (!$title) {
              return jsonError('请输入标题');
            }
            if (!$cid) {
                $cid = 0;
            }
            if (!$id) {
                return jsonError('缺少ID');
            }
            if (!$title) {
                return jsonError('请输入标题');
            }
            if (!$content) {
                return jsonError('请输入内容');
            }
            $askInfo = Asks::where('uuid',$id)->find();
            if (!$askInfo) {
                return jsonError('问题不存在');
            }
            if ($this->userId == $askInfo['uid'] || $this->isAdmin) {
                $updateArticleInfo = $askInfo->where('uuid',$id)->update([
                    'title' => htmlspecialchars($title),
                    'cid' => $cid,
                    'edit_time'=>time(),
                    'publish_time' => $publish_time,
                    'is_recommend' => $is_recommend,
                   ]);

                if ($askInfo) {
                    AsksContent::where('id',$askInfo['content_id'])->update(array(
                       'content' => htmlspecialchars($content),
                    ));
                    if ($tags) {
                        model('app\model\tags\ItemTags')->innerTags($tags, $itemType, $askInfo);
                    }
                    return jsonSuccess('修改成功',$this->mipInfo['idStatus'] ? $askInfo->uuid : $askInfo->id);
                }
            } else {
               return jsonError('无权限操作');
            }
        }
   }
    public function answerDel(Request $request) {
        if (Request::instance()->isPost()) {
            $itemId = input('post.itemId');
            $answerId = input('post.answerId');
            if (!$itemId || !$answerId) {
                return jsonError('缺少ID');
            }
            if (!Asks::where('uuid',$itemId)->find()) {
                return jsonError('问题不存在');
            }
            if(!$asksAnswerInfo = AsksAnswers::getById($answerId)) {
                return jsonError('回答不存在');
            }
            if ($asksAnswerInfo) {
                if ($this->userId == $asksAnswerInfo['uid'] || $this->isAdmin) {
                    AsksAnswersComments::where('item_id',$asksAnswerInfo['id'])->delete();
                    $asksAnswerInfo->delete();
                    Asks::where('id',$itemId)->update([
                        'answer_num' => AsksAnswers::where('item_id',$itemId)->count()
                    ]);
                    return jsonSuccess('删除成功');
                } else {
                    return jsonError('无权限操作');
                }
            } else {
                return  jsonError('删除不存在');
            }

        }
    }
    public function answersSelect(Request $request) {
        if (Request::instance()->isPost()) {
            $itemId = input('post.itemId');
            $page = input('post.page');
            $limit = input('post.limit');
            $orderBy = input('post.orderBy');
            $order = input('post.order');
            if (!$itemId) {
              $itemId = 1;
            }
            if (!$page) {
              $page = 1;
            }
            if (!$limit) {
              $limit = 10;
            }
            if (!$orderBy) {
             $orderBy = 'create_time';
            }
            if (!$order) {
                $order = 'asc';
            }
            $asksAnswersList = AsksAnswers::where('item_id',$itemId)->limit($limit)->page($page)->order($orderBy, $order)->select();
            $total = AsksAnswers::where('item_id',$itemId)->count();
            if ($asksAnswersList) {
                foreach ($asksAnswersList as $k => $v) {
                    $asksAnswersList[$k]['content'] = htmlspecialchars_decode($v['content']);
                    $asksAnswersList[$k]->users;
                    $asksAnswersList[$k]->avatar = getAvatarUrl($v['uid']);
                    $asksAnswersList[$k]['comments'] = '';
                    $asksAnswersList[$k]['commentsCount'] = AsksAnswersComments::where('item_id',$v['id'])->count();;
                }
            }

            return jsonSuccess('ok',['itemList' => $asksAnswersList,'total' => $total,'page' => $page]);
        }
    }
    public function answerEdit(Request $request) {
        if (Request::instance()->isPost()) {
            $itemId = input('post.itemId');
            $answerId = input('post.answerId');
            $content = Htmlp::htmlp(input('post.content'));
            if(!$itemId || !$answerId){
                return jsonError('缺少ID');
            }
            if(!$content){
                return jsonError('请输入内容');
            }
            if (!Asks::where('uuid',$itemId)->find()) {
                return jsonError('问题不存在');
            }
            if(!$asksAnswerInfo = AsksAnswers::getById($answerId)){
                return jsonError('回答不存在');
            }
            if ($this->userId == $asksAnswerInfo['uid'] || $this->isAdmin) {
                if($asksAnswerInfo->where('id',$answerId)->update(['content' => htmlspecialchars($content),'edit_time'=>time()])){
                    return jsonSuccess('修改成功');
                }
            } else {
                return jsonError('无权限操作');
            }
        }
    }


    public function categorySelect(Request $request) {
        if (Request::instance()->isPost()) {
            $page = input('post.page');
            $limit = input('post.limit');
            $orderBy = input('post.orderBy');
            $order = input('post.order');
            if(!$page){
              $page = 1;
            }
            if(!$limit){
              $limit = 10;
            }
            if(!$orderBy){
             $orderBy = 'id';
            }
            if(!$order){
                $order = 'desc';
            }
            $categoryList = AsksCategory::limit($limit)->page($page)->order($orderBy, $order)->select();
            return jsonSuccess('',['categoryList' => $categoryList,'total' => AsksCategory::count(),'page' => $page]);
        }
    }
}