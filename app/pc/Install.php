<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use think\Controller;
use think\Request;

use mip\Mip;
class Install extends Mip
{
    public function index()
    {
        if (is_file(CONF_PATH.'install'.DS.'install.lock')) {
            header('Location: ' . url('@/'));
            exit();
        }
        if (!defined('__ROOT__')) {
            $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
            define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
        }
        $data=array();
        $icon_correct='<i class="el-icon-circle-check"></i> ';
        $icon_error='<i class="el-icon-circle-close"></i> ';
        //php版本、操作系统版本
        $data['phpversion'] = @phpversion();
        $data['os']=PHP_OS;
        //环境检测
        $err = 0;
        if (class_exists('pdo')) {
            $data['pdo'] = $icon_correct.'已开启';
        } else {
            $data['pdo'] = $icon_error.'未开启';
            $err++;
        }
        //扩展检测
        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = $icon_correct.'已开启';
        } else {
            $data['pdo_mysql'] =$icon_error.'未开启';
            $err++;
        }
        if (extension_loaded('curl')) {
            $data['curl'] = $icon_correct.'已开启';
        } else {
            $data['curl'] = $icon_error.'未开启';
            $err++;
        }
        if (extension_loaded('mbstring')) {
            $data['mbstring'] = $icon_correct.'已开启';
        } else {
            $data['mbstring'] = $icon_error.'未开启';
            $err++;
        }
        if (extension_loaded('exif')) {
            $data['exif'] = $icon_correct.'已开启';
        } else {
            $data['exif'] = $icon_error.'未开启';
            $err++;
        }
        //设置获取
        if (ini_get('file_uploads')) {
            $data['upload_size'] = $icon_correct . ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = $icon_error.'禁止上传';
        }
        if (ini_get('allow_url_fopen')) {
            $data['allow_url_fopen'] = $icon_correct.'已开启';
        } else {
            $data['allow_url_fopen'] = $icon_error.'未开启';
            $err++;
        }
        //函数检测
        if (function_exists('file_get_contents')) {
            $data['file_get_contents'] = $icon_correct.'已开启';
        } else {
            $data['file_get_contents'] = $icon_error.'未开启';
            $err++;
        }
        if (function_exists('session_start')) {
            $data['session'] = $icon_correct.'已开启';
        } else {
            $data['session'] = $icon_error.'未开启';
            $err++;
        }
        //检测文件夹属性
        $checklist = array(
            'cache',
            'public/install',
            'system/config',
        );
        $new_checklist = array();
        foreach($checklist as $dir){
            if(is_writable($dir)){
                $new_checklist[$dir]['w']=true;
            }else{
                $new_checklist[$dir]['w']=false;
                $err++;
            }
            if(is_readable($dir)){
                $new_checklist[$dir]['r']=true;
            }else{
                $new_checklist[$dir]['r']=false;
                $err++;
            }
        }
        $data['checklist'] = $new_checklist;
        $this->assign('data',$data);
        return $this->fetch('pc@admin/install');
    }
    
    public function installPost(Request $request) {
            if (Request::instance()->isPost()) {
                
                $dbconfig['type']="mysql";
                $dbconfig['hostname']=input('post.dbhost');
                $dbconfig['username']=input('post.dbuser');
                $dbconfig['password']=input('post.dbpw');
                $dbconfig['hostport']=input('post.dbport');
                $dbname=strtolower(input('post.dbname'));
                $dsn = "mysql:dbname={$dbname};host={$dbconfig['hostname']};port={$dbconfig['hostport']};charset=utf8";
                try {
                    $db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
                } catch (\PDOException $e) {
                    return jsonError('数据库连接失败，请检查连接地址或者账号密码是否错误');
                }
                $dbconfig['database'] = $dbname;
                $dbconfig['prefix']=trim(input('dbprefix'));
                $tablepre = input("dbprefix");
                $sql = file_get_contents(ROOT_PATH.'package'.DS.'mipcms_v_2_0_0.sql');
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);
                $default_tablepre = "mip_";
                $sql = str_replace(" `{$default_tablepre}", " `{$tablepre}", $sql);
                foreach ($sql as $item) {
                    $item = trim($item);
                    if(empty($item)) continue;
                    preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
                    if($matches) {
                        if(false !== $db->exec($item)){
                            
                        } else {
                           return jsonError('安装失败');
                        }
                    } else {
                        $db->exec($item);
                    }
                }
                try {
                    touch(PUBLIC_PATH.'install'.DS.'install.lock');
                } catch (\PDOException $e) {
                    return jsonError('install.lock文件写入失败，请检查public/install 文件夹是否可写入');
                }
                if(is_array($dbconfig)){
                    $conf = file_get_contents(PUBLIC_PATH.'package'.DS.'database.php');
                    foreach ($dbconfig as $key => $value) {
                        $conf = str_replace("#{$key}#", $value, $conf);
                    }
                    try {
                        $re = file_put_contents(CONF_PATH. '/database.php', $conf);
                    } catch (\PDOException $e) {
                        return jsonError('database.php文件写入失败，请检查system/config 文件夹是否可写入');
                    }
                    if(file_put_contents(CONF_PATH. '/database.php', $conf)){
                        return jsonSuccess('配置文件写入成功');
                    } else {
                        return jsonError('配置文件写入失败');
                    }
                }
                
        }
    
    }
}
