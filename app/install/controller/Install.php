<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\install\controller;
use think\Controller;
use think\Request;
use app\user\model\Users;
use think\Loader;

use mip\Mip;
class Install extends Controller
{
    public function index()
    {
       
        if (is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
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
        }
        //检测文件夹属性
        $cache =  ROOT_PATH.'runtime';
        $install = ROOT_PATH.'public/install';
        $config = ROOT_PATH.'app';
        $new_checklist = array();
        if(is_writable($cache)){
            $new_checklist['cache']['w']=true;
        }else{
            $new_checklist['cache']['w']=false;
        }
        if(is_writable($install)){
            $new_checklist['install']['w']=true;
        }else{
            $new_checklist['install']['w']=false;
        }
        if(is_writable($config)){
            $new_checklist['config']['w']=true;
        }else{
            $new_checklist['config']['w']=false;
        }
        $data['checklist'] = $new_checklist;
        $this->assign('data',$data);
        $request = Request::instance();
        $installAddress = $request->domain() . $request->url();
        
        $this->assign('installAddress',$installAddress);
        
        return $this->fetch('install@/install');
    }

    public function installPost(Request $request)
    {
        if (!is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
            
            if (Request::instance()->isPost()) {
                $dbconfig['type']="mysql";
                $dbconfig['hostname']=input('post.dbhost');
                $dbconfig['username']=input('post.dbuser');
                $dbconfig['password']=input('post.dbpw');
                $dbconfig['hostport']=input('post.dbport');
                $dbname = input('post.dbname');
                
                $username = input('post.username');
                $password = input('post.password');
                $rpassword = input('post.rpassword');
                if (!$username) {
                    return jsonError('请输入用户名');
                }
                if (!$password) {
                    return jsonError('请输入密码');
                }
                if (!$rpassword) {
                    return jsonError('请输入重复密码');
                }
                
                $dsn = "mysql:dbname={$dbname};host={$dbconfig['hostname']};port={$dbconfig['hostport']};charset=utf8";
                try {
                    $db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
                } catch (\PDOException $e) {
                    return jsonError('错误代码:'.$e->getMessage());
                }
                $dbconfig['database'] = $dbname;
                $dbconfig['prefix']=trim(input('dbprefix'));
                $tablepre = input("dbprefix");
                $sql = file_get_contents(PUBLIC_PATH.'package'.DS.'mipcms_v_3_6_0.sql');
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
                
                
                if(is_array($dbconfig)){
                    $conf = file_get_contents(PUBLIC_PATH.'package'.DS.'database.php');
                    foreach ($dbconfig as $key => $value) {
                        $conf = str_replace("#{$key}#", $value, $conf);
                    }
                    $install = CONF_PATH;
                    if(!is_writable($install)){
                        return jsonError('路径：'.$install.'没有写入权限');
                    }
                    try {
                        $fileStatus = is_file(CONF_PATH. '/database.php');
                        if ($fileStatus) {
                             unlink(CONF_PATH. '/database.php');
                        }
                        file_put_contents(CONF_PATH. '/database.php', $conf);
                        return jsonSuccess('配置文件写入成功',1);
                    } catch (Exception $e) {
                        return jsonError('database.php文件写入失败，请检查system/config 文件夹是否可写入');
                    }
                    
                }
            }
        }

    }

    public function installPostOne(Request $request)
    {
        if (!is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
            if (Request::instance()->isPost()) {
                $dbconfig['type']="mysql";
                $installAddress = input('post.installAddress');
                $dbconfig['hostname']=input('post.dbhost');
                $dbconfig['username']=input('post.dbuser');
                $dbconfig['password']=input('post.dbpw');
                $dbconfig['hostport']=input('post.dbport');
                $dbname = input('post.dbname');
                
                $username = input('post.username');
                $password = input('post.password');
                $rpassword = input('post.rpassword');
                if (!$username) {
                    return jsonError('请输入用户名');
                }
                if (!$password) {
                    return jsonError('请输入密码');
                }
                if (!$rpassword) {
                    return jsonError('请输入重复密码');
                }
                $dsn = "mysql:dbname={$dbname};host={$dbconfig['hostname']};port={$dbconfig['hostport']};charset=utf8";
                try {
                    $db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
                } catch (\PDOException $e) {
                    return jsonError('错误代码:'.$e->getMessage());
                }
                
                
                if(is_file(CONF_PATH. '/database.php')) {
                    $install = ROOT_PATH.'public/install';
                    if(!is_writable($install)){
                        return jsonError('路径：/public/install没有写入权限');
                    }
                    try {
                        touch(PUBLIC_PATH.'install'.DS.'install.lock');
                    } catch (Exception $e) {
                        return jsonError('install.lock文件写入失败，请检查public/install 文件夹是否可写入');
                    }
                    if ($installAddress) {
                        db('settings')->where('key','articleDomain')->update(array(
                            'val' => $installAddress,
                        ));
                    }
             
                         $salt = create_salt(8);
                       db('users')->insert(array(
                            'uid' => uuid(),
                            'username' => $username,
                            'password' => create_md5($password,$salt),
                            'salt' => $salt,
                            'reg_time' => time(),
                            'group_id' => 1,
                            'rank' => 1,
                            'terminal' => 'pc',
                        ));
                     return jsonSuccess('安装完成','');
                } else {
                    return jsonError('配置文件写入失败');
                }
                    
                    
                    
            }
        }
    }

}
