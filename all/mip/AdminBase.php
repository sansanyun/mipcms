<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Controller;

use mip\Mip;
class AdminBase extends Mip {
    public function _initialize() {
        parent::_initialize();
        if(!$this->user_info){
            $this->error('尚未登录','/login.html',301);
        }
        $this->passStatus = false;
        $roleAccessList = db('RolesAccess')->where('group_id',$this->user_info['group_id'])->select();     
        if ($roleAccessList) {
            foreach ($roleAccessList as $k => $v) {
                $modeIds[$k] = $v['node_id'];
            }
            $roleList = db('RolesNode')->where(['id' => ['in', $modeIds], 'status' => "1"])->select();
            $roleList = list_to_tree($roleList);
            foreach ($roleList as $key => $val) {
                if ($val['pid'] == 0 && strtoupper($val['name']) == strtoupper($this->request->controller())) {
                    $this->passStatus = true;
                }
            }
        }
        if (!$this->passStatus) {
            $this->error('无权限操作','/',301);
        }
    }
}
