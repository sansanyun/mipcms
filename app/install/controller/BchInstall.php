<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\install\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Loader;

use mip\Mip;
class BchInstall extends Controller
{
    public function index()
    {
        if (is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
            header('Location: ' . url('@/'));
            exit();
        }
        if (!is_file(ROOT_PATH. 'public' . DS . 'install' . DS .'install.lock')) {
            $dbconfig['type']="mysql";
            $dbconfig['hostname'] = input('param.dbHost');
            $dbconfig['username'] = input('param.dbUsername');
            $dbconfig['password'] = input('param.dbPassword');
            $dbconfig['hostport'] = input('param.dbPort');
            $dbconfig['database'] = input('param.dbName');
            $dbconfig['prefix'] = 'mip_';
            $ftpPassword = input('param.ftpPassword');
            
            $dsn = "mysql:dbname={$dbconfig['database']};host={$dbconfig['hostname']};port={$dbconfig['hostport']};charset=utf8";
            try {
                $db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
            } catch (\PDOException $e) {
                return json_encode(array('status' => 'fail','result' => '错误代码:'.$e->getMessage()));
            }
            $tablepre = $dbconfig['prefix'];
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
                       return json_encode(array('status' => 'fail','result' => '安装失败'));
                    }
                } else {
                    $db->exec($item);
                }
            }
                
            if(is_array($dbconfig)) {
                $conf = file_get_contents(PUBLIC_PATH.'package'.DS.'database.php');
                foreach ($dbconfig as $key => $value) {
                    $conf = str_replace("#{$key}#", $value, $conf);
                }
                if(!is_writable(CONF_PATH)) {
                    return json_encode(array('status' => 'fail','result' => '路径：'.CONF_PATH.'没有写入权限'));
                }
                try {
                    $fileStatus = is_file(CONF_PATH. '/database.php');
                    if ($fileStatus) {
                         unlink(CONF_PATH. '/database.php');
                    }
                    file_put_contents(CONF_PATH. '/database.php', $conf);
                    
                    $db2 = Db::connect([
                        'type'        => 'mysql',
                        'dsn'         => '',
                        'hostname'    => $dbconfig['hostname'],
                        'database'    => $dbconfig['database'],
                        'username'    => $dbconfig['username'],
                        'password'    => $dbconfig['password'],
                        'hostport'    => '',
                        'params'      => [],
                        'charset'     => 'utf8',
                        'prefix'      => $dbconfig['prefix'],
                    ]);
                   $username = 'admin';
                   $password = md5($ftpPassword);
                   $salt = create_salt(8);
                   $db2->name('Users')->insert(array(
                        'uid' => uuid(),
                        'username' => $username,
                        'password' => create_md5($password,$salt),
                        'salt' => $salt,
                        'reg_time' => time(),
                        'group_id' => 1,
                        'rank' => 1,
                        'terminal' => 'pc',
                    ));
//                  
                    if (!is_writable(ROOT_PATH.'public/install')) {
                        return json_encode(array('status' => 'fail','result' => "路径：/public/install没有写入权限"));
                    }
                    try {
                        touch(PUBLIC_PATH.'install'.DS.'install.lock');
                    } catch (Exception $e) {
                        return json_encode(array('status' => 'fail','result' => "install.lock文件写入失败，请检查public/install 文件夹是否可写入"));
                    }
                    $request = Request::instance();
                    $domain = $request->domain();
                    
                    return json_encode(array(
                        'status' => 'succeed',
                        'result' => json_encode(array([
                            "key" => "管理地址",
                            "value" => $domain . '/admin/'
                        ],[
                            "key" => "用户名",
                            "value" => $username,
                        ],[
                            "key" => "密码",
                            "value" => $ftpPassword,
                        ]))));
                    
                } catch (Exception $e) {
                    return json_encode(array('status' => 'fail','result' => "database写入失败"));
                }
                
            }
        }
        
        
        return $this->fetch('install@/install');
    }
 
 

}
