<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;
use think\Request;

use mip\Init;
class AdminBase extends Init {
 
    public function _initialize() {
        parent::_initialize();
        
        if (!Request::instance()->isPost()) {
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['code'=>1000, 'msg'=>'违法操作']));
        }
        $header = Request::instance()->header();
         
        $this->passStatus = false;
        $passAuthInfo = true;
        $passAuthList = array(
            '0' => 'imgUpload',
        );
        foreach ($passAuthList as $key => $val) {
            if (strtoupper($passAuthList[$key]) == strtoupper($this->request->action())) {
                $passAuthInfo = false;
            }
        }
        if ($passAuthInfo) {
            if (!$this->isAdmin) {
                if (empty($header['secret-key'])) {
                    header('Content-Type:application/json; charset=utf-8');
                    exit(json_encode(['code'=>1008, 'msg'=>'无权限操作']));
                }
                if ($header['secret-key'] == db('AccessKey')->where('id',999)->find()['key']) {
                    $this->passStatus = true;
                } else {
                    header('Content-Type:application/json; charset=utf-8');
                    exit(json_encode(['code'=>1010, 'msg'=>'无权限操作']));
                }
            } else {
                $this->passStatus = true;
            }
            
            if (!$this->passStatus) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['code'=>1009, 'msg'=>'无权限操作']));
            }
            
            if (isset($header['dataid']) && $header['dataid']) {
                config('dataId',$header['dataid']);
                $request = Request::instance();
                $settings = db('Settings')->select();
                foreach ($settings as $k => $v) {
                    $mipInfo[$v['key']] = $v['val'];
                }
                if ($mipInfo['superSites']) {
                    $domainSitesList = db('domainSites')->select();
                    $domainSettingsInfo = null;
                    if ($domainSitesList) {
                        $siteInfo = db('domainSites')->where('domain',$request->server()['HTTP_HOST'])->find();
                        if ($siteInfo) {
                            $domain = $siteInfo['http_type'] . $siteInfo['domain'] . $rewrite;
                            $domainStatic = $siteInfo['http_type'] . $siteInfo['domain'];
                            $tplName = $siteInfo['template'];
                            config('dataId',$siteInfo['id']);
                            $domainSettingsInfo = db('domainSettings')->where('id',$siteInfo['id'])->find();
                            config('domainSettingsInfo',$domainSettingsInfo);
                        } else {
                            $domain = $request->domain() . $rewrite;
                            $domainStatic = str_replace('/index.php?s=', '', $domain);
                        }
                    }
                } else {
                    $domain = $request->domain() . $rewrite;
                    $domainStatic = str_replace('/index.php?s=', '', $domain);
                    if ($mipInfo['domain']) {
                        $domain = $mipInfo['httpType'] . $mipInfo['domain'] . $rewrite;
                        $domainStatic = $mipInfo['httpType'] . $mipInfo['domain'];
                    }
                    if ($mipInfo['articleDomain']) {
                        $domain =  $mipInfo['articleDomain'];
                        if (strpos($domain{(strlen(trim($domain))-1)},'/') !== false) {
                           $domain = substr($domain,0,strlen($domain)-1); 
                        }
                        $domainStatic = $domain;
                        $domain = $domain . $rewrite;
                    }
                }
                config('domain',$domain);
                $this->domain = $domain;
                config('domainStatic',$domainStatic);
                config('mipInfo',$mipInfo);
            }
            
            
        }
         
    }
}
