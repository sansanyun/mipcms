<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\addons\controller;
use think\Request;
use think\Db;
use com\File;

use mip\AdminBase;
class ApiAdminAddons extends AdminBase
{
    public function save()
    {
        $itemList = input('post.itemList');
        $itemList = json_decode($itemList,true);
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                 $itemList[$key]['side_status'] =  $itemList[$key]['side_status'] ? 1 : 0;
                 $itemList[$key]['header_status'] =  $itemList[$key]['header_status'] ? 1 : 0;
            }
            foreach ($itemList as $key => $val) {
                db('Addons')->where('id',$val['id'])->update(array(
                    'side_status' => $itemList[$key]['side_status'],
                    'header_status' => $itemList[$key]['header_status'],
                ));
            }
        }
        
        $addonsList = db('Addons')->select();
        db('AddonsMenu')->where('type','addons')->delete();
        foreach ($addonsList as $key => $val) {
            if ($addonsList[$key]['side_status'] == 1) {
                db('AddonsMenu')->insert(array(
                    'name' => $val['name'],
                    'title' => $val['title'],
                    'item_id' => $val['id'],
                    'status' => 1,
                    'admin_url' => $val['admin_url'],
                    'type' => 'addons',
                    'sort' => 0,
                ));
            }
        }
        db('headerMenu')->where('type','addons')->delete();
        foreach ($addonsList as $key => $val) {
            if ($addonsList[$key]['header_status'] == 1) {
                db('headerMenu')->insert(array(
                    'name' => $val['name'],
                    'title' => $val['title'],
                    'item_id' => $val['id'],
                    'admin_url' => $val['admin_url'],
                    'status' => 1,
                    'type' => 'addons',
                    'sort' => 0,
                ));
            }
        }
        
        return jsonSuccess('ok');
    }
    
    public function install()
    {
        $name = input('post.name');
        if (!$name) {
            return jsonError('缺少参数');
        }
        $itemInfo = db('Addons')->where('name',$name)->find();
        if ($itemInfo) {
            return jsonError('该插件已安装，无法再次安装');
        }
        $class = get_addon_class($name);
        $addons = new $class();
        if (!$addons->install()) {
            return jsonError('执行插件预安装操作失败');
        }
        $addonsDir = ROOT_PATH . 'addons' . DS . $name . DS;
        $globalFileList = [
            'all',
            'app',
            'public'
        ];
        $assetsFile = $addonsDir . 'assets' . DS;
        if (is_dir($assetsFile)) {
            $assetsDir = ROOT_PATH . str_replace("/", DS, "public/assets/addons/{$name}/");
            if (!is_dir($assetsDir)) {
                try {
                    mkdir($assetsDir, 0755, true);
                } catch (\Exception $e) {
                    return jsonError('插件安装失败，原因：' . $assetsDir . '文件权限问题，创建失败');
                }
            }
            File::copy_dir($assetsFile, $assetsDir);
            foreach ($globalFileList as $dir) {
                if (is_dir($addonsDir . $dir)) {
                    File::copy_dir($addonsDir . $dir, ROOT_PATH . $dir);
                }
            }
        }
        
        $sqlFile = ROOT_PATH . 'addons' . DS . $name . DS . 'install.sql';
        if (is_file($sqlFile)) {
            try {
                $prefix = "mip_";
                $orginal = config('database.prefix');
                $sql = str_replace(" `{$orginal}"," `{$prefix}", file_get_contents($sqlFile));
                Db::getPdo()->exec($sql);
            } catch (\PDOException $e) {
//              return jsonError('数据库安装失败，原因：' . $e);
            }
        }
        $info = $addons->info;
        db('Addons')->insert(array(
            'name' => $info['name'],
            'title' => $info['title'],
            'description' => $info['description'],
            'author' => $info['author'],
            'status' => $info['status'],
            'version' => $info['version'],
            'admin_url' => $info['adminUrl'],
            'add_time' => time(),
            'config' => json_encode($addons->getConfig()),
        ));
        if ($info['isGlobalAction']) {
            db('GlobalAction')->where('type','addons')->where('name',$info['name'])->delete();
            db('GlobalAction')->insert(array(
                'name' => $info['name'],
                'title' => $info['title'],
                'status' => 1,
                'type' => 'addons',
                'sort' => 0,
            ));
        }
        return jsonSuccess('ok');
    }

    public function uninstall()
    {
        $id = input ('id');
        if (!$id) {
            return jsonError('缺少参数');
        }
        $itemInfo = db('Addons')->where('id',$id)->find();
        if (!$itemInfo) {
            return jsonError('卸载项不存在');
        }
        $name = $itemInfo['name'];
        try {
            $class = get_addon_class($itemInfo['name']);
            $addons = new $class();
            if (!$addons->uninstall()) {
                return jsonError('执行插件预卸载操作失败');
            }
        } catch (\Exception $e) {
            //解决文件被删除 卸载问题
        }
        $assetsDir = ROOT_PATH . str_replace("/", DS, "public/assets/addons/{$name}/");
        if (is_dir($assetsDir)) {
            File::del_dir($assetsDir);
        }
        db('Addons')->where('id',$id)->delete();
        $sqlFile = ROOT_PATH . 'addons' . DS . $itemInfo['name'] . DS . 'uninstall.sql';
        if (is_file($sqlFile)) {
            try {
                $prefix = "mip_";
                $orginal = config('database.prefix');
                $sql = str_replace(" `{$orginal}"," `{$prefix}", file_get_contents($sqlFile));
                Db::getPdo()->exec($sql);
            } catch (\PDOException $e) {
                return jsonError('数据库安装失败，原因：' . $e);
            }
        }
        db('AddonsMenu')->where('type','addons')->where('item_id',$id)->delete();
        db('headerMenu')->where('type','addons')->where('item_id',$id)->delete();
        db('GlobalAction')->where('type','addons')->where('name',$itemInfo['name'])->delete();
        return jsonSuccess('ok');
    }
    
    public function enable()
    {
        $id = input ('id');
        if (!$id) {
            return jsonError('缺少参数');
        }
        db('Addons')->where('id',$id)->update(array('status' => 1));
        return jsonSuccess('ok');
    }
    
    public function disable()
    {
        $id = input ('id');
        if (!$id) {
            return jsonError('缺少参数');
        }
        db('Addons')->where('id',$id)->update(array('status' => 0));
        return jsonSuccess('ok');
    }
    
    public function itemList()
    {
        $addonsPath = ROOT_PATH . 'addons' . DS;
        $templateDir = opendir($addonsPath);
        if ($templateDir) {
            while (false !== ($file = readdir($templateDir))) {
                if (substr($file, 0, 1) != '.' AND is_dir($addonsPath . $file)) {
                    $dirs[] = $file;
                }
            }
            closedir($templateDir);
        }
        $addons = array();
        $list = db('Addons')->where('name','in',$dirs)->select();
        if ($list) {
            foreach ($list as $key => $val ) {
                $list[$key]['uninstall'] = 0;
                $addons[$val['name']] = $list[$key];
            }
        }
        $tempAddons = array();
        if ($dirs) {
            foreach ($dirs as $val) {
                if (!isset($addons[$val])) {
                    try {
                        $class = get_addon_class($val);
                        if (!class_exists($class)) {
                            trace($class);
                            continue;
                        }
                        $obj = new $class();
                        $addons[$val] = $obj->info;
                        if ($addons[$val]) {
                            $addons[$val]['uninstall'] = 1;
                            unset($addons[$val]['status']);
                        }
                    } catch (\Exception $e) {
                        
                    }
                }
                $tempAddons[] = $addons[$val];
            }
        }
        return jsonSuccess('',array('itemList' => $tempAddons));
    }
 
}