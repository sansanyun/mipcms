<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\controller;
use think\Db;
use mip\AdminBase;
class ApiAdminArticleDiy extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $table = 'articles';
        $this->currentTableName = $table;
        $this->currentTable = $table;
        if (empty($this->currentTable)) {
            $this->currentTable = $table;
            $this->currentTable = $this->currentTable;
        }
        
    }
    
    public function index() {

    }
    
    public function itemFieldList() 
    {
//      $fieldList = Db::query('SHOW COLUMNS FROM ' . 'mip_' . $this->currentTable);
//      $tempField = array();
//      foreach ($fieldList AS $k => $v) {
//          if(strpos($v['Field'],'diy_') !== false) {
//              $tempField[] = $v;
//          }
//      }
        $tebleList = db($this->currentTable.'Table')->select();
                     
        return jsonSuccess('',$tebleList);
    }
    
    public function addField() 
    {
        $fieldTitle  = input('fieldTitle');
        $field  = input('fieldName');
        $fieldType  = input('fieldType');
        
        if (!$fieldTitle) {
            return jsonError('请输入字段名称');
        }
        if (!$field) {
            return jsonError('请输入字段别名');
        }
        if (!$fieldType) {
            return jsonError('请选择类型');
        }
        
        $field = 'diy_' . $field;
        
        $tebleList = db($this->currentTable.'Table')->select();
        if ($tebleList) {
            foreach ($tebleList as $key => $val) {
                if ($fieldTitle == $val['name']) {
                    return jsonError('你输入的字段名称已存在');
                }
                if ($field == $val['value']) {
                    return jsonError('你输入的字段别名已存在');
                }
            }
        }
        
        if ($fieldType == 'string') {
            $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` ADD `'. $field .'` varchar(255) DEFAULT NULL';
        }
        if ($fieldType == 'longtext') {
            $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` ADD `'. $field .'` longtext';
        }
        if ($fieldType == 'int') {
            $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` ADD `'. $field .'` int(11) unsigned DEFAULT 0';
        }
        
        try {
           Db::query($sql);
        } catch(\Exception $e) {
           return jsonError('添加失败');
        }
        
        db($this->currentTable . 'Table')->insert(array(
            'id' => uuid(),
            'name' => $fieldTitle,
            'value' => $field,
            'type' => $fieldType,
        ));
        return jsonSuccess('添加成功');
    }
    public function delItem()
    {
        $field  = input('fieldName');
        if (!$field) {
            return jsonError('请输入字段名称');
        }
        $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` drop `'. $field .'` ';
        try {
           Db::query($sql);
        } catch (\Exception $e) {
           return jsonError('操作失败');
        }
        db($this->currentTable . 'Table')->where('value',$field)->delete();
        return jsonSuccess('');
    }
    
     public function editField() 
    {
        $fieldTitle  = input('fieldTitle');
        $field  = input('fieldName');
        $fieldNewName  = input('fieldNewName');
        $fieldType  = input('fieldType');
        
        if (!$fieldTitle) {
            return jsonError('请输入字段名称');
        }
        if (!$field) {
            return jsonError('请输入字段名称');
        }
        if (!$fieldType) {
            return jsonError('请选择类型');
        }
        if ($fieldNewName) {
            $field = 'diy_' . $field;
            $fieldNewName = 'diy_' . $fieldNewName;
            if ($fieldType == 'string') {
                $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` CHANGE `'. $field .'` `'. $fieldNewName .'` varchar(255) DEFAULT NULL';
            }
            if ($fieldType == 'longtext') {
                $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` CHANGE `'. $field .'` `'. $fieldNewName .'` longtext';
            }
            if ($fieldType == 'int') {
                $sql = 'ALTER TABLE `'.config('database')['prefix'].$this->currentTable.'` CHANGE `'. $field .'` `'. $fieldNewName .'` int(11) unsigned DEFAULT 0';
            }
        }
        try {
           Db::query($sql);
        } catch (\Exception $e) {
           return jsonError('操作失败');
        }
        if ($fieldNewName) {
            db($this->currentTable . 'Table')->where('value',$field)->update(array(
                'name' => $fieldTitle,
                'value' => $fieldNewName,
                'type' => $fieldType,
            ));
        } else {
            db($this->currentTable . 'Table')->where('value',$field)->update(array(
                'name' => $fieldTitle,
                'type' => $fieldType,
            ));
        }
        return jsonSuccess('');
    }
    


}