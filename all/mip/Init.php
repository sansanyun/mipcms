<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;
use think\Config;
use think\Session;
class Init extends Controller
{
    public $mipInfo;
    public $siteUrl;
    public $domain;
    public $userInfo;
    public $rewrite;
    public $userId;
    public $tplName;
    public $dataId;
    public $domainSettingsInfo;
    //DB
    public $siteId;
    public $siteDomain;
    public $articles;
    public $articlesTime;
    public $articlesTimeMain;
    public $articlesCategory;
    public $articlesContent;
    public $itemTags;
    public $tags;
    public $tagsCategory;
    public $settings;
    public $product;
    public $productCategory;
    public function _initialize()
    {
        //常用变量转换
        hook('corsHook');
        $this->assign('mod',$this->request->module());
        $this->assign('ctr',$this->request->controller());
        $this->assign('act',$this->request->action());
        $this->domain = $this->request->domain();
        $this->assign('domain',$this->domain);
        $this->webDomain = $this->request->domain();
        $this->assign('webDomain',$this->domain);
        if (!MIP_HOST) {
            $this->assign('assets','assets');
            $this->assets = 'assets';
        } else {
            $this->assign('assets','public/assets');
            $this->assets = 'public/assets';
        }
        //初始化数据库名称
        $this->articles = 'Articles';
        $this->articlesCategory = 'ArticlesCategory';
        $this->articlesContent = 'ArticlesContent';
        $this->itemTags = 'ItemTags';
        $this->tags = 'Tags';
        $this->tagsCategory =  'TagsCategory';
        $this->settings = 'Settings';
        $this->product = 'Product';
        $this->productCategory = 'ProductCategory';
        //网站配置信息查询
        $settings = db('Settings')->select();
        foreach ($settings as $k => $v) {
            $this->mipInfo[$v['key']] = $v['val'];
        }
        $this->assign('mipInfo',$this->mipInfo);
        //伪静态判断
        if ($this->mipInfo['rewrite']) {
            $this->rewrite = '';
        } else {
            $this->rewrite = '/index.php?s=';
        }
        $this->assign('rewrite',$this->rewrite);
        //域名判断
        if ($this->mipInfo['domain']) {
            $domain = $this->mipInfo['httpType'] . $this->mipInfo['domain'] . $this->rewrite;
            $this->assign('domainStatic',$this->mipInfo['httpType'] . $this->mipInfo['domain']);
            $this->domainStatic = $this->mipInfo['httpType'] . $this->mipInfo['domain'];
            $this->webDomain = $this->domainStatic;
            $this->assign('webDomain',$this->domainStatic);
        } else {
            $domain = $this->domain . $this->rewrite;
            $this->assign('domainStatic',$this->domain);
            $this->domainStatic = $this->domain;
            $this->webDomain = $this->domainStatic;
            $this->assign('webDomain',$this->domainStatic);
        }
        if ($this->mipInfo['articleDomain']) {
            if ($this->mipInfo['rewrite']) {
                $this->rewrite = '';
            } else {
                $this->rewrite = '/index.php?s=';
            }
            $this->assign('rewrite',$this->rewrite);
            $domain = $this->mipInfo['articleDomain'];
            if (strpos($domain{(strlen(trim($domain))-1)},'/') !== false) {
               $domain = substr($domain,0,strlen($domain)-1); 
            }
            $this->domainStatic = $domain;
            $this->assign('domainStatic',$this->domainStatic);
            $domain = $domain . $this->rewrite;
            
        }
        $this->assign('domain',$domain);
        $this->domain = $domain;
        $tplName = $this->mipInfo['template'];
        //超级站
        if ($this->mipInfo['superSites']) {
            $domainSitesList = db('domainSites')->select();
            $this->domainSettingsInfo = null;
            if ($domainSitesList) {
                $siteInfo = db('domainSites')->where('domain',$this->request->server()['HTTP_HOST'])->find();
                if ($siteInfo) {
                    $domain = $siteInfo['http_type'] . $siteInfo['domain'] . $this->rewrite;
                    $this->assign('domain',$domain);
                    $this->domain = $domain;
                    $this->domainStatic = $siteInfo['http_type'] . $siteInfo['domain'];
                    $this->assign('domainStatic',$this->domainStatic);
                    $tplName = $siteInfo['template'];
                    $this->mipInfo['biaduZn'] = db('domainSettings')->where('id',$siteInfo['id'])->find()['baiduSearchKey'];
                    $this->assign('mipInfo',$this->mipInfo);
                    $this->dataId = $siteInfo['id'];
                    $this->domainSettingsInfo = db('domainSettings')->where('id',$siteInfo['id'])->find();
                }
            }
        }
        //模板配置
        $this->tplName = $tplName;
        Config::set('view_name',$tplName);
        $this->assign('config',Config::get());
        //当前网站地址
        $this->siteUrl = $this->domain . $this->request->url();
        $this->assign('siteUrl',$this->siteUrl);
        $this->currentUrl = $this->domain . $this->request->url();
        $this->assign('currentUrl',$this->currentUrl);
        //初始化信息
        $this->mipInit();
    }

    public function mipInit()
    {
        $userInfo = Session::get('userInfo');
        $this->isAdmin = false;
        $this->isLogin = false;
        if ($userInfo) {
            $this->userInfo = db('Users')->where('username' ,$userInfo['username'])->find();
            $this->userId = $this->userInfo['uid'];
            $this->groupId = $this->userInfo['group_id'];
            
            if ($this->groupId == 1) {
                $this->isAdmin = true;
            } else {
                $this->isAdmin = false;
            }
            $this->isLogin = true;

            $this->assign('role',null);

            if ($this->userInfo['status'] == 1) {
                Session::delete('userInfo');
                $this->error('抱歉, 你的账号已经被禁止登录','/');
            }
        } else {
            $this->userInfo = null;
            $this->userId = '';
            $this->passStatus = false;
        }
        
        //系统关闭状态
        if (!$this->mipInfo['systemStatus']) {
            if ($this->request->controller() != 'Account') {
                if ($this->userInfo['group_id'] != 1) {
                    if (!$this->request->isPost()) {
                        $this->error('站点关闭中...','');
                    }
                }
            }
        }
        $this->assign('isLogin',$this->isLogin);
        $this->assign('isAdmin',$this->isAdmin);
        $this->assign('userInfo',$this->userInfo);
        $this->assign('userId',$this->userId);
//      $userAvatar = getAvatarUrl($this->userId);
//      $this->assign('userAvatar',$userAvatar);

        //js跳转地址
        $this->assign('return_url','');
        //js通用模块内容页ID赋值（uuid）
        $this->assign('itemDetailId','');
    }
     
    protected function addonsFetch($template = '', $addonsName = '')
    {
        if (!$addonsName) {
            $this->error('模板渲染，缺少参数','');
        }
        if ($template) {
            $template = '../addons' . DS . $addonsName  . DS . 'view' . DS . $template;
        } else {
            return false;
        }
        return $this->fetch($template);
    }

}
