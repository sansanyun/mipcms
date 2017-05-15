<?php

namespace mip;

use think\Cache;
use think\Request;
use think\Session;
use think\Response;

use mip\Mip;
class AuthBase extends Mip
{

    public static $expires = 172800;
    
    public static $requestExpiresTime = 30;
  
    public static $accessTokenPrefix = 'accessToken_';

    public static $accessTokenAndClientPrefix = 'accessTokenAndClient_';

    public function _initialize() {
        parent::_initialize();
        $header = Request::instance()->header();
        if (empty($header['terminal'])) {
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['code'=>1001, 'msg'=>'缺少请求参数']));
        }
        if (empty($header['access-key'])) {
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['code'=>$header, 'msg'=>'缺少请求参数']));
        }
        $accessKey = db('AccessKey')->where('key',$header['access-key'])->where('type',$header['terminal'])->find();
        if (!$accessKey) {
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['code'=>1004, 'msg'=>'请求参数错误']));
        }
        $this->terminal = $header['terminal'];
        $this->accessKey = $header['access-key'];
        $passAuthList = array(
            '0' => 'userAdd',
            '1' => 'loginOut',
            '2' => 'login',
        );
        $passAuthInfo = true;
        foreach ($passAuthList as $key => $val) {
            if (strtoupper($passAuthList[$key]) == strtoupper($this->request->action())) {
                $passAuthInfo = false;
            }
        }
        if ($passAuthInfo) {
            if (empty($header['access-token']) || empty($header['uid'])) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['code'=>1002, 'msg'=>'缺少请求参数']));
            }
            $accessTokenInfo = $this->auth($header['access-token'], $this->terminal, $header['uid']);
            if (!$accessTokenInfo) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['code'=>1005, 'msg'=>'身份失效']));
            }
            $this->accessTokenId = $header['uid'];
            $this->accessToken = $header['access-token'];
            $this->passStatus = false;
            $roleAccessList = db('RolesAccess')->where('group_id',$accessTokenInfo['client']['client_group_id'])->select(); 
            if ($roleAccessList) {
                foreach ($roleAccessList as $k => $v) {
                    $modeIds[$k] = $v['node_id'];
                    $rolesAccessPids[$k] = $v['pid'];
                }
                $roleList = db('RolesNode')->where(['id' => ['in', $modeIds]])->whereOr(['id' => ['in', $rolesAccessPids]])->select();
                foreach ($roleList as $key => $val) {
                    if (strtoupper($val['name']) == strtoupper($this->request->action())) {
                        $this->passStatus = true;
                    }
                }
            }
            if (!$this->passStatus) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['code'=>1006, 'msg'=>'无权限操作']));
            }
        }
    }
    
    public function auth($accessToken,$terminal,$uid) {
        if (empty($accessToken) || strlen($accessToken) < 32 || $accessToken == false) {
            return false;
        }
        $userInfo = db('Users')->where('uid',$uid)->find();
        $accessTokenInfo = $this->getAccessTokenInfo($accessToken,$terminal,$userInfo['group_id']);
        if (!$accessTokenInfo) {
            return false;
        }
        return $accessTokenInfo;
    }
    
    public function accessToken($uid,$terminal) {
        if (empty($uid)) {
            return false;
        }
        $clientInfo = self::getClient($uid,$this->accessKey,$terminal);
        if (!$clientInfo) {
            return false;
        }
        $access_token = $this->getAccessTokenAndClient($uid, $terminal, $clientInfo['client_group_id']);
        $access_token = (!$access_token) ? self::setAccessToken($clientInfo,$terminal) : $access_token;
        return array([
            'access-token' => $access_token,
            'expires' => self::$expires,
        ]);

    }

    protected function getClient($client_id, $secret,$terminal) {
        $userInfo = db('Users')->where('uid',$client_id)->find();
        return [
            'client_name' => $userInfo['username'],
            'client_id' => $userInfo['uid'],
            'client_group_id' => $userInfo['group_id'],
            'client_terminal' => $terminal,
            'client_time' => time(),
            'secret' => $secret,
        ];
    }

    protected function setAccessToken($clientInfo,$terminal) {
        $accessToken = self::buildAccessToken();
        $accessTokenInfo = [
            'access_token' => $accessToken,
            'expires_time' => time() + self::$expires,
            'client' => $clientInfo,
        ];
        self::saveAccessToken($accessToken, $accessTokenInfo,$terminal);
        return $accessToken;
    }

    protected function getAccessTokenInfo($accessToken,$terminal,$group_id)
    {
        $keys = self::$accessTokenPrefix . $this->terminal . $accessToken . $group_id;
        $info = Cache::get($keys);
        if ($info == false || $info['expires_time'] < time()) return false;
        $client_id = $info['client']['client_id'];
        if ($this->getAccessTokenAndClient($client_id,$terminal,$group_id) != $accessToken) return false;
        return $info;
    }

    protected static function buildAccessToken()
    {
        $factory = new \RandomLib\Factory();
        $generator = $factory->getMediumStrengthGenerator();
        return $generator->generateString(32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

    }
    
    protected static function saveAccessToken($accessToken, $accessTokenInfo,$terminal) {
        Cache::set(self::$accessTokenPrefix . $terminal . $accessToken . $accessTokenInfo['client']['client_group_id'], $accessTokenInfo, self::$expires);
        Cache::set(self::$accessTokenAndClientPrefix . $terminal .  $accessTokenInfo['client']['client_id'] . $accessTokenInfo['client']['client_group_id'], $accessToken, self::$expires);
    }

    protected function getAccessTokenAndClient($client_id,$terminal,$group_id) {
        return Cache::get(self::$accessTokenAndClientPrefix . $terminal . $client_id . $group_id);
    }

}