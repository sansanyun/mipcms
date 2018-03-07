<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\xiongzhang\controller;
use think\Request;
use mip\Init;
class ApiUserXiongZhang extends Init
{
    private $keyInfo;
    
    public function configAccess()
    {
        $TOKEN = db('Key')->where('key','baiduXZToken')->find()['val'];
        $strSignature = getSHA1($TOKEN, $_GET['timestamp'], $_GET['nonce']);
        if ($strSignature == $_GET['signature']) {
            $accessToken = model('app\xiongzhang\model\xiongZhang')->getAccessToken();
            $xml = file_get_contents('php://input');
            $biaduData = simplexml_load_string($xml);
            // <xml>
            //     <ToUserName><![CDATA[1376007]]></ToUserName>  //开发者熊掌号ID
            //     <FromUserName><![CDATA[mgJNc450EXcoC3Q_U0lRWbFyBO]]></FromUserame> //发送方帐号（一个OpenID）
            //     <CreateTime><![CDATA[1500449281]]></CreateTime> //消息创建时间 （整型）
            //     <MsgType><![CDATA[text]]></MsgType> //text
            //     <Content><![CDATA[消息内容]]></Content> //文本消息内容
            //     <MsgId><![CDATA[15065717731159]]></MsgId> //消息id，64位整型
            // </xml>
            $replyData = null;
            $autoReplyList = db('XiongzhangAutoReply')->select();
            if ($autoReplyList) {
                foreach ($autoReplyList as $key => $val) {
                    if ($biaduData->Content == $val['user_content']) {
                        $replyData = $val;
                    }
                }
            }
            if ($replyData) {
                $fromUserName = $biaduData->FromUserName. "";
                $postArray = array(
                    "touser" => $fromUserName,
                    "msgtype" => "text",
                    "text" => array("content" => $replyData['reply_content'])
                );
                $subUrl = 'https://openapi.baidu.com/rest/2.0/cambrian/message/custom_send?access_token=' . $accessToken; 
                getData($subUrl,json_encode($postArray));
            }
            
            // file_put_contents(ROOT_PATH."log.txt",$biaduData->Content);
            echo $_GET['echostr'];
        } else {
            echo 'failed';
        }
    }
    
}