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
        $this->siteUrl = $request->domain().$request->url();
        $this->assign('siteUrl',$this->siteUrl);
        //网站配置信息查询
        if ($settings = db('Settings')->select()){
            foreach ($settings as $k => $v){
                if (is_serialized($v['val'])){
                    $v['val'] =@unserialize($v['val']);
                }
                $this->mipInfo[$v['key']] = $v['val'];
            }
        } else {
            $this->mipInfo = null;
        }
        if (!$this->mipInfo['systemStatus']) {
            $this->error('工程师正在抢修中...');
        }
        $this->assign('main',$this->mipInfo['template'].'/main/main');
        $this->assign('mipInfo',$this->mipInfo);
        /*设置模板名称*/
        $tplPath = config('template')['view_path'];

        $tplName = $this->mipInfo['template'];
        $this->assign('tplName',$tplName);
        $this->config('view_name',$tplName);
        //默认ui路径
        $this->assign('mipui','./assets/mipui/dist/css/mipui.css');
        
        if ($this->mipInfo['mipDomain']) {
            //如果当前是手机访问
            if (Request::instance()->isMobile()) {
                //判断是否移动域名
                if (Request::instance()->header('host') != $this->mipInfo['mipDomain']) {
                    //在不同域名下 判断是否开启了手机可以查看pc资源
                    if (!Session::get('isMobile')) {
                        
                        header('Location: ' . 'http://'.$this->mipInfo['mipDomain'].Request::instance()->url());
                        exit();
                    }
                }
            }
        }
//      Session::set('isMobile',false);
        $this->mipInit();
        $this->categoryInit();
        $this->friendLink(); 
        $this->spider(); 
//      if (!$this->passStatus) {
//          if ($this->request->method() == 'POST') {
//              return jsonError('无权限操作');
//          } else {
//              exception("无权限操作");
//          }
//      }
    }
    //用户信息初始化
    public function mipInit(){
      
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
            
//          $roleAccessList = db('RolesAccess')->where('group_id',$this->groupId)->select(); 
//          if ($roleAccessList) {
//              foreach ($roleAccessList as $k => $v) {
//                  $modeIds[$k] = $v['node_id'];
//                  $rolesAccessPids[$k] = $v['pid'];
//              }
//              $rolesNodeList = db('RolesNode')->select();
//              foreach ($rolesNodeList as $k => $v) {
//                 $this->assign('is'.ucfirst($v['name']),'');
//              }
//              $roleList = db('RolesNode')->where(['id' => ['in', $modeIds]])->whereOr(['id' => ['in', $rolesAccessPids]])->select();
//              foreach ($roleList as $k => $v) {
//                 $this->assign('is'.ucfirst($v['name']),$v['name']);
//              }
//          }
            
            if ($this->userInfo['status'] == 1) {
                Session::delete('userInfo');
                $this->error('抱歉, 你的账号已经被禁止登录','/');
            }
        } else {
            $this->userInfo = null;
            $this->userId = '';
            $this->passStatus = false;
        }
        $this->assign('isLogin',$this->isLogin);
        $this->assign('isAdmin',$this->isAdmin);
        $this->assign('userInfo',$this->userInfo);
        $this->assign('user_info',$this->userInfo);
        $this->user_info = $this->userInfo;
        $this->assign('userId',$this->userId);
        $this->assign('user_id',$this->userId);
        $this->user_id = $this->userId;
        
//      if( $this->CORS ){
//          header('Access-Control-Allow-Origin: *');
//          header('Access-Control-Allow-Credentials: true');
//          header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//          header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
//          $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlHttpRequest';
//      }
    }
    public function categoryInit() {
        $this->assign('articleModelName',$this->mipInfo['articleModelName']);
        $this->assign('articleModelUrl',$this->mipInfo['articleModelUrl']);
        $this->articleModelUrl = $this->mipInfo['articleModelUrl'];
        $this->assign('askModelName',$this->mipInfo['askModelName']);
        $this->assign('askModelUrl',$this->mipInfo['askModelUrl']);
        $this->askModelUrl = 'ask';
        $categoryUrlName = null;
        $itemCategoryList = null;
        $articleCategoryList = null;
        $askCategoryList = null;
        $itemCategoryUrlName = '';
        if ($this->mipInfo['systemType'] == 'CMS' || $this->mipInfo['systemType'] == 'Blog') {
            $itemCategoryList = db('ArticlesCategory')->order('sort desc')->select();
            $itemCategoryUrlName = $this->mipInfo['articleModelUrl'];
        }
        if ($this->mipInfo['systemType'] == 'ASK') {
            $itemCategoryList = db('AsksCategory')->order('sort desc')->select();
            $itemCategoryUrlName = $this->mipInfo['askModelUrl'];
        }
        if($itemCategoryList) {
            foreach ($itemCategoryList as $key => $val) {
                if(!Validate::regex($itemCategoryList[$key]['url_name'],'\d+') AND $itemCategoryList[$key]['url_name']){
                    $itemCategoryList[$key]['url_name']=$itemCategoryList[$key]['url_name'];
                }else{
                    $itemCategoryList[$key]['url_name']='cid_'.$itemCategoryList[$key]['id'];
                }
            }
        }
        $this->assign('itemCategoryUrlName',$itemCategoryUrlName); 
        $this->assign('itemCategoryList',$itemCategoryList); 
        if ($this->mipInfo['systemType'] == 'SNS') {
            $articleCategoryList = db('ArticlesCategory')->order('sort desc')->select();
            if($articleCategoryList) {
                foreach ($articleCategoryList as $key => $val) {
                    if(!Validate::regex($articleCategoryList[$key]['url_name'],'\d+') AND $articleCategoryList[$key]['url_name']){
                        $articleCategoryList[$key]['url_name']=$articleCategoryList[$key]['url_name'];
                    }else{
                        $articleCategoryList[$key]['url_name']='cid_'.$articleCategoryList[$key]['id'];
                    }
                }
            }
            $this->assign('articleCategoryList',$articleCategoryList); 
            $askCategoryList = db('AsksCategory')->order('sort desc')->select();
            if($askCategoryList) {
                foreach ($askCategoryList as $key => $val) {
                    if(!Validate::regex($askCategoryList[$key]['url_name'],'\d+') AND $askCategoryList[$key]['url_name']){
                        $askCategoryList[$key]['url_name']=$askCategoryList[$key]['url_name'];
                    }else{
                        $askCategoryList[$key]['url_name']='cid_'.$askCategoryList[$key]['id'];
                    }
                }
            }
            $this->assign('askCategoryList',$askCategoryList); 
        }
        
        
        $this->assign('categoryUrlName',$categoryUrlName);
        
    }
    public function spider() {
        $userAgent = Request::instance()->header()['user-agent'];
        if (strpos($userAgent,"Baiduspider")) {
            if (strpos($userAgent,"Mobile")) {
                if (strpos($userAgent,"render")) {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobileRender','pageUrl' => $this->siteUrl));
                } else {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobile','pageUrl' => $this->siteUrl));
                }
            } else {
                if (strpos($userAgent,"render")) {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pcRender','pageUrl' => $this->siteUrl));
                } else {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pc','pageUrl' => $this->siteUrl));
                }
            }
        }
        
    }
    public function friendLink() {
        $friendLink = db('friendlink')->order('id desc')->select();
        $this->assign('friendLink',$friendLink);
        
    }
    //模板渲染处理
    public function mipView($parent){
        $tplName = $this->mipInfo['template'];
//           return $this->fetch($tplName.'/'.$parent); //前端代码不压缩
     return compress_html($this->fetch($tplName.'/'.$parent));//前端代码压缩
    }




}
