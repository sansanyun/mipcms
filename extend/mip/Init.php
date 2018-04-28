<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;
use think\Config;
use think\Session;
class Init extends Controller
{
    public function _initialize()
    {
        $this->articles = 'Articles';
        $this->articlesCategory = 'ArticlesCategory';
        $this->articlesContent = 'ArticlesContent';
        $this->itemTags = 'ItemTags';
        $this->tags = 'Tags';
        $this->tagsCategory =  'TagsCategory';
        $this->settings = 'Settings';
        
        config('articles',$this->articles);
        config('articlesCategory',$this->articlesCategory);
        config('articlesContent',$this->articlesContent);
        config('itemTags',$this->itemTags);
        config('tags',$this->tags);
        config('tagsCategory',$this->tagsCategory);
        config('settings',$this->settings);
     
        $this->mipInit();
    }

    public function mipInit()
    {
        $userInfo = Session::get('userInfo');
        $this->isAdmin = false;
        if ($userInfo) {
            $this->userInfo = db('Users')->where('uid' ,$userInfo['uid'])->find();
            $this->userId = $this->userInfo['uid'];
            $this->groupId = $this->userInfo['group_id'];
            if ($this->groupId == 1) {
                $this->isAdmin = true;
            } else {
                $this->isAdmin = false;
            }
            $this->assign('role',null);
            if ($this->userInfo['status'] == 1) {
                Session::delete('userInfo');
                $this->error('抱歉, 你的账号已经被禁止登录','/');
            }
        } else {
            $this->userInfo = null;
            $this->userId = '';
            $this->groupId = '';
        }
        //系统关闭状态
        if (!config('mipInfo')['systemStatus']) {
            if ($this->request->controller() != 'Admin') {
                if ($this->userInfo['group_id'] != 1) {
                    if (!$this->request->isPost()) {
                        $this->error('站点关闭中...','');
                    }
                }
            }
        }
        config('isAdmin',$this->isAdmin);
        $this->assign('isAdmin',$this->isAdmin);
        config('userInfo',$this->userInfo);
        $this->assign('userInfo',$this->userInfo);
        config('userId',$this->userId);
        $this->assign('userId',$this->userId);
        
//      $userAvatar = getAvatarUrl($this->userId);
//      $this->assign('userAvatar',$userAvatar);

        //js跳转地址
        $this->assign('return_url','');
        //js通用模块内容页ID赋值（uuid）
        $this->assign('itemDetailId','');
        $this->assign('mod',$this->request->module());
        $this->assign('ctr',$this->request->controller());
        $this->assign('act',$this->request->action());
        
        MIP_HOST ? config('assets','public/assets') : config('assets','assets');
        $this->assign('assets',config('assets'));
        $this->assets = config('assets');
        
        config('commonStatic',config('domainStatic') . '/' . config('assets') . '/common');
        $this->assign('commonStatic',config('commonStatic'));
        $this->commonStatic = config('commonStatic');
        
        
        $this->assign('mipInfo',config('mipInfo'));
        $this->mipInfo = config('mipInfo');
        
        $this->assign('rewrite',config('rewrite'));
        
        $this->assign('domain',config('domain'));
        $this->domain = config('domain');
        
        $this->assign('domainStatic',config('domainStatic'));
        $this->domainStatic = config('domainStatic');
        
        $this->assign('config',config());
        
        $this->assign('siteUrl',config('domain') . $this->request->url());
        $this->siteUrl = config('domain') . $this->request->url();
        $this->assign('currentUrl',config('domain') . $this->request->url());
        $this->currentUrl = config('domain') . $this->request->url();
        //
        $this->assign('categoryUrlName','');
        config('dataId','');
        $this->dataId = config('dataId');
        $this->assign('dataId','');
        $itemList = db('GlobalAction')->select();
        if ($itemList) {
            try {
                foreach ($itemList as $key => $val) {
                    $addonsName = $val['name'];
                    if (strpos($addonsName,"mipinit") !== false) {
                        $addonsNameSpace = "addons" . "\\" . $addonsName . "\\" . "controller" . "\\" . "GlobalAction";
                        model($addonsNameSpace)->$addonsName();
                    }
                }
            } catch (\Exception $e) {}
        }
    }
     
    protected function addonsFetch($template = '', $addonsName = '')
    {
        $this->assign('domain',config('domain'));
        $this->assign('domainStatic',config('domainStatic'));
        $this->assign('mipInfo',config('mipInfo'));
        $tplName = config('view_name');
        $this->assign('tplName',$tplName);
        $this->assign('templateStatic', config('domainStatic') . '/template/' . $tplName . '/templateStatic');
        $this->assign('themeStatic', config('domainStatic') . '/' . config('assets') . '/' . $tplName);
        $this->assign('siteUrl',config('domain') . $this->request->url());
        $this->assign('currentUrl',config('domain') . $this->request->url());
        
        $this->assign('config',config());
        
        if (!$addonsName) {
            $this->error('模板渲染，缺少参数','');
        }
        if ($template) {
            $template = '../../addons' . DS . $addonsName  . DS . 'view' . DS . $template;
        } else {
            return false;
        }
        return $this->fetch($template);
    }

}
