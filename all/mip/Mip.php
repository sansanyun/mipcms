<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;
use think\Config;
use think\Cache;
use think\Session;
use think\Validate;
use think\template;
class Mip extends Controller
{
    public $mipInfo;
    public $siteUrl;
    public $domain;
    public $userInfo;
    public function _initialize()
    {
        //常用变量转换
        $request = Request::instance();
        $this->assign('mod',$request->module());
        $this->assign('ctr',$request->controller());
        $this->assign('act',$request->action());
        $this->domain = $request->domain();
        $this->assign('domain',$this->domain);
        //网站配置信息查询
        if ($settings = db('Settings')->select()){
            foreach ($settings as $k => $v){
                $this->mipInfo[$v['key']] = $v['val'];
            }
        } else {
            $this->mipInfo = null;
        }
        $this->assign('mipInfo',$this->mipInfo);

        $tplName = $this->mipInfo['template'];
        $this->assign('tplName',$tplName);
        $this->config('view_name',$tplName);
        if ($this->mipInfo['rewrite']) {
            $this->rewrite = '';
        } else {
            $this->rewrite = '/index.php?s=';
        }
        $this->assign('rewrite',$this->rewrite);

        if ($this->mipInfo['mipDomain']) {
            //如果当前是手机访问
            if (Request::instance()->isMobile()) {
                if ($this->mipInfo['superSites']) {
                     if (Request::instance()->header('host') == $this->mipInfo['domain']) {
                        //在不同域名下 判断是否开启了手机可以查看pc资源
                        if (!Session::get('isMobile')) {
                            header('Location: ' . $this->mipInfo['httpType'] .$this->mipInfo['mipDomain'].Request::instance()->url());
                            exit();
                        }
                    }
                    $itemCategoryList = db('ArticlesCategory')->where('pid',0)->order('sort asc')->select();
                    if($itemCategoryList) {
                        foreach ($itemCategoryList as $key => $val) {
                            if (Request::instance()->header('host') == $val['url_name'].'.'.$this->mipInfo['topDomain']) {
                                    header('Location: ' . $this->mipInfo['httpType'] .'m.'.$val['url_name'].'.'.$this->mipInfo['topDomain'].Request::instance()->url());
                                    exit();
                            }
                        }
                    }
                } else {

                    if (Request::instance()->header('host') != $this->mipInfo['mipDomain']) {
                        //在不同域名下 判断是否开启了手机可以查看pc资源
                        if (!Session::get('isMobile')) {

                            header('Location: ' . $this->mipInfo['httpType'] .$this->mipInfo['mipDomain'].Request::instance()->url());
                            exit();
                        }
                    }
                }
            }
        }
        $this->siteUrl = $this->mipInfo['httpType'].$this->domain.$request->url();
        $this->assign('siteUrl',$this->siteUrl);
        $this->pcSiteUrl = $this->mipInfo['httpType'].$this->mipInfo['domain'].$request->url();
        $this->assign('pcSiteUrl',$this->pcSiteUrl);
        $this->mipSiteUrl = $this->mipInfo['httpType'].$this->mipInfo['mipDomain'].$request->url();
        $this->assign('mipSiteUrl',$this->mipSiteUrl);

        $this->isMobile = Request::instance()->isMobile();
//      Session::set('isMobile',false);
        $this->mipInit();
        $this->modelName();
        $this->categoryInit();
        $this->headerInfo();
        $this->crumb();
        $this->friendLink();
        $this->spider();
        $this->articleSetting();
        $this->askSetting();
    }

    /**
     * 用户信息初始化
     * 全局通用
     * */
    public function mipInit()
    {
        $request = Request::instance();
        $userInfo = Session::get('userInfo');
        $this->isAdmin = false;
        $this->isLogin = false;
        if ($userInfo) {
            $this->userInfo = Db::name('Users')->where('username' ,$userInfo['username'])->find();
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
            if ($request->controller() != 'Account') {
                if ($this->userInfo['group_id'] != 1) {
                    if (!$request->isPost()) {
                        $this->error('工程师正在抢修中...','');
                    }
                }
            }
        }
        $this->assign('isLogin',$this->isLogin);
        $this->assign('isAdmin',$this->isAdmin);
        $this->assign('userInfo',$this->userInfo);
        $this->assign('user_info',$this->userInfo);
        $this->user_info = $this->userInfo;
        $this->assign('userId',$this->userId);
        $this->assign('user_id',$this->userId);
        $this->user_id = $this->userId;
        $userAvatar = getAvatarUrl($this->userId);
        $this->assign('userAvatar',$userAvatar);

        $tpl_path = config('template')['view_path'];
        foreach (fetch_file_lists($tpl_path) as $key => $file){
            if(strstr($file,'config.php')){
                require_once $file;
            }
        }
        //js跳转地址
        $this->assign('return_url','');
        //js通用模块内容页ID赋值（uuid）
        $this->assign('itemDetailId','');

        $this->assign('assets','assets');
        //跨域
//      if( $this->CORS ){
//          header('Access-Control-Allow-Origin: *');
//          header('Access-Control-Allow-Credentials: true');
//          header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//          header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
//          $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlHttpRequest';
//      }
    }

    /**
     * 模块化变量赋值
     * 控制器、模板调用
     * */
    public function modelName()
    {
        $this->assign('articleModelName',$this->mipInfo['articleModelName']);
        $this->assign('articleModelUrl',$this->mipInfo['articleModelUrl']);
        $this->articleModelName = $this->mipInfo['articleModelName'];
        $this->articleModelUrl = $this->mipInfo['articleModelUrl'];
        $this->assign('askModelName',$this->mipInfo['askModelName']);
        $this->assign('askModelUrl',$this->mipInfo['askModelUrl']);
        $this->askModelName = $this->mipInfo['askModelName'];
        $this->askModelUrl = $this->mipInfo['askModelUrl'];
        $this->assign('userModelName',$this->mipInfo['userModelName']);
        $this->assign('userModelUrl',$this->mipInfo['userModelUrl']);
        $this->userModelName = $this->mipInfo['userModelName'];
        $this->userModelUrl = $this->mipInfo['userModelUrl'];
        $this->assign('tagModelName',$this->mipInfo['tagModelName']);
        $this->assign('tagModelUrl',$this->mipInfo['tagModelUrl']);
        $this->userModelUrl = $this->mipInfo['tagModelUrl'];
        $this->tagModelName = $this->mipInfo['tagModelName'];

    }

    /**
     * 项目分类（文章）
     * 全局调用项目分类，支持子栏目
     * */
    public function categoryInit()
    {
        $itemCategoryList = null;
        $itemCategoryList = db('ArticlesCategory')->where('pid',0)->order('sort asc')->select();
        if($itemCategoryList) {
            foreach ($itemCategoryList as $key => $val) {
                $itemCategoryList[$key]['sub'] = db('ArticlesCategory')->where('pid',$val['id'])->select();
                if ($itemCategoryList[$key]['sub']) {
                    foreach ($itemCategoryList[$key]['sub'] as $k => $v) {
                        if ($this->mipInfo['superSites']) {
                            $itemCategoryList[$key]['sub'][$k]['url'] = $this->mipInfo['httpType'] . $val['url_name'] . '.' . $this->mipInfo['topDomain'] . $this->rewrite .'/' . $v['url_name'] . '/';
                            if ($this->mipInfo['mipDomain']) {
                                $itemCategoryList[$key]['sub'][$k]['mipUrl'] = $this->mipInfo['httpType'] . 'm.' . $val['url_name'] . '.' . $this->mipInfo['topDomain'] . $this->rewrite .'/' . $v['url_name'] . '/';
                            }
                        } else {
                            $itemCategoryList[$key]['sub'][$k]['url'] = $this->mipInfo['httpType'] . $this->mipInfo['domain'] . $this->rewrite . '/' . $val['url_name'] . '/' . $v['url_name'] . '/';
                            $itemCategoryList[$key]['sub'][$k]['mipUrl'] = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite . '/' . $val['url_name'] . '/' . $v['url_name'] . '/';
                        }
                    }
                }
                if ($this->mipInfo['superSites']) {
                    $itemCategoryList[$key]['url'] = $this->mipInfo['httpType'] . $val['url_name'] . '.' . $this->mipInfo['topDomain'] . '/';
                    if ($this->mipInfo['mipDomain']) {
                        $itemCategoryList[$key]['mipUrl'] = $this->mipInfo['httpType'] . 'm.' .$val['url_name'] . '.' . $this->mipInfo['topDomain'] . '/';
                    }

                    if (Request::instance()->header('host') == $val['url_name'].'.'.$this->mipInfo['topDomain']) {
                        $this->pcSiteUrl = $this->mipInfo['httpType'] . $val['url_name'].'.'.$this->mipInfo['topDomain'].Request::instance()->url();
                        $this->assign('pcSiteUrl',$this->pcSiteUrl);
                        $this->mipSiteUrl = $this->mipInfo['httpType'] .'m.'.$val['url_name'].'.'.$this->mipInfo['topDomain'].Request::instance()->url();
                        $this->assign('mipSiteUrl',$this->mipSiteUrl);
                    }
                    if (Request::instance()->header('host') == 'm.' . $val['url_name'].'.'.$this->mipInfo['topDomain']) {
                        $this->pcSiteUrl = $this->mipInfo['httpType'] . $val['url_name'].'.'.$this->mipInfo['topDomain'].Request::instance()->url();
                        $this->assign('pcSiteUrl',$this->pcSiteUrl);
                        $this->mipSiteUrl = $this->mipInfo['httpType'] .'m.'.$val['url_name'].'.'.$this->mipInfo['topDomain'].Request::instance()->url();
                        $this->assign('mipSiteUrl',$this->mipSiteUrl);
                    }

                } else {
                    if ($this->mipInfo['articleDomain']) {
                        $itemCategoryList[$key]['url'] = $this->mipInfo['httpType'] . $this->mipInfo['articleDomain'] . $this->rewrite . '/' . $val['url_name'] . '/';
                        $itemCategoryList[$key]['mipUrl'] = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite . '/' . $val['url_name'] . '/';
                    } else {
                        if ($this->mipInfo['aritcleLevelRemove']) {
                            $itemCategoryList[$key]['url'] =  $this->rewrite . '/' . $val['url_name'] . '/';
                            $itemCategoryList[$key]['mipUrl'] = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] . $this->rewrite . '/' . $val['url_name'] . '/';
                        } else {
                            $itemCategoryList[$key]['url'] =  $this->rewrite . '/' . $this->mipInfo['articleModelUrl']  . '/' . $val['url_name'] . '/';
                            $itemCategoryList[$key]['mipUrl'] = $this->mipInfo['httpType'] . $this->mipInfo['mipDomain'] .$this->rewrite . '/' . $this->mipInfo['articleModelUrl']  . '/' . $val['url_name'] . '/';
                        }
                    }
                }
            }
        }
        $this->assign('itemCategoryList',$itemCategoryList);
        $this->itemCategoryList = $itemCategoryList;
        $this->assign('categoryUrlName',null);
    }

    /**
     * 标题关键词描述
     * 不同的页面根据不同的控制器控制
     * */
    public function headerInfo()
    {
        $this->assign('mipTitle',$this->mipInfo['siteName']);
        $this->assign('mipKeywords',$this->mipInfo['keywords']);
        $this->assign('mipDescription',$this->mipInfo['description']);
    }

    /**
     * 面包屑导航
     * 根据不同的赋值开启层级
     * */
    public function crumb()
    {
        if ($this->mipInfo['domain']) {
            $domain = $this->mipInfo['httpType'] . $this->mipInfo['domain'] .$this->rewrite;
            $this->assign('domain',$domain);
            $this->domain = $domain;
            $this->assign('articleDomain',$domain);
            $this->articleDomain  = $domain;
            if ($this->mipInfo['articleDomain']) {
                $domain = $this->mipInfo['httpType'] . $this->mipInfo['articleDomain'] . $this->rewrite;
                $this->assign('articleDomain',$domain);
                $this->articleDomain  = $domain;
            }
        } else {
            $this->assign('articleDomain',$this->domain . $this->rewrite);
            $this->articleDomain  = $this->domain . $this->rewrite ;
        }
        $this->assign('crumb',true);
        $this->assign('crumbDomain',true);
        $this->assign('crumbCategory',true);
        $this->assign('crumbCategorySub',null);
        $this->assign('crumbDetail',null);

        $this->assign('crumbCategoryName',$this->articleModelName);
        $this->assign('crumbCategoryUrl',$this->domain . $this->rewrite . '/' . $this->articleModelUrl . '/');
        if ($this->mipInfo['articleDomain']) {
            $this->assign('crumbCategoryName',$this->articleModelName);
            $this->assign('crumbCategoryUrl',$this->articleDomain . '/');
        }
    }

    /**
     * 蜘蛛爬取
     * 根据不同的蜘蛛，记录爬行的页面时间和地址
     * */
    public function spider()
    {
        $userAgent = @Request::instance()->header()['user-agent'];
        if ($this->mipInfo['baiduSpider']) {
            if (strpos($userAgent,"Baiduspider")) {
                if (strpos($userAgent,"Mobile")) {
                    if (strpos($userAgent,"render")) {
                        db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobileRender','pageUrl' => $this->siteUrl, 'ua' => $userAgent, 'vendor' => 'baidu'));
                    } else {
                        db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobile','pageUrl' => $this->siteUrl, 'ua' => $userAgent, 'vendor' => 'baidu'));
                    }
                } else {
                    if (strpos($userAgent,"render")) {
                        db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pcRender','pageUrl' => $this->siteUrl, 'ua' => $userAgent, 'vendor' => 'baidu'));
                    } else {
                        db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pc','pageUrl' => $this->siteUrl, 'ua' => $userAgent, 'vendor' => 'baidu'));
                    }
                }
            }
        }

    }

    /**
     * 友情链接
     * 全局友情链接，后台支持按照不同的页面展示不同的友情链接
     * */
    public function friendLink()
    {
        $friendLink = db('friendlink')->order('sort ASC')->select();
        $friendLink['friendLinkAll'] = false;
        $friendLink['friendLinkIndex'] = false;
        $friendLink['friendLinkNotIndex'] = false;
        foreach ($friendLink as $key => $val) {
            if ($val['type'] == 'all') {
                $friendLink['friendLinkAll'] = true;
            }
            if ($val['type'] == 'index') {
                $friendLink['friendLinkIndex'] = true;
            }
            if ($val['type'] == 'notIndex') {
                $friendLink['friendLinkNotIndex'] = true;
            }
        }
        $this->assign('friendLink',$friendLink);

    }

    /**
     * 文章模块设置信息
     * 获取后台文章模块设置的信息
     * */
    public function articleSetting()
    {
        $articleSetting = db('articlesSetting')->select();
        if ($articleSetting) {
            foreach ($articleSetting as $k => $v){
                $this->articleSetting[$v['key']] = $v['val'];
            }
        } else {
            $this->articleSetting = null;
        }
        $this->assign('articleSetting',$this->articleSetting);
    }

    /**
     * 问答模块设置信息
     * 获取后台问答模块设置的信息
     * */
    public function askSetting() {
        $this->askSetting = db('asksSetting')->select();
        if ($this->askSetting) {
            foreach ($this->askSetting as $k => $v) {
                $this->askSetting[$v['key']] = $v['val'];
            }
        } else {
            $this->askSetting = null;
        }
        $this->assign('askSetting',$this->askSetting);

    }

    /**
     * 模板渲染处理
     * 根据后台是否一键开启模板代码压缩
     * */
    public function mipView($parent,$type = 'pc',$categoryUrlName = null) {
        if ($type == 'm') {
            $tplName = $this->mipInfo['mipTemplate'];
        } else {
            $tplName = $this->mipInfo['template'];
        }
        $this->assign('tplName',$tplName);
        $this->config('view_name',$type . DS .$tplName);

        if ($this->mipInfo['superTpl']) {
            if ($categoryUrlName) {
                $tplName = $categoryUrlName;
                $this->assign('tplName',$tplName);
                $this->config('view_name',$type . DS .$tplName);
            }
        }


        if ($this->mipInfo['codeCompression']) {
            return compress_html($this->fetch($type . '/' . $tplName.'/'.$parent));
        } else {
            return $this->fetch($type . '/' . $tplName.'/'.$parent);
        }
    }



}
