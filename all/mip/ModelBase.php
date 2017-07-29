<?php

namespace mip;

use think\Controller;
use think\Model;
use think\Db;

class ModelBase extends Model
{
    public $mipInfo;
    public $articleSetting;
    public $rewrite;
    protected function initialize()
    {
        parent::initialize();
        if ($settings = db('Settings')->select()) {
            foreach ($settings as $k => $v) {
                $this->mipInfo[$v['key']] = $v['val'];
            }
        } else {
            $this->mipInfo = null;
        }

        $articleSetting = db('articlesSetting')->select();
        if ($articleSetting) {
            foreach ($articleSetting as $k => $v){
                $this->articleSetting[$v['key']] = $v['val'];
            }
        } else {
            $this->articleSetting = null;
        }

        if ($this->mipInfo['rewrite']) {
            $this->rewrite = '';
        } else {
            $this->rewrite = '/index.php?s=';
        }
    }


}