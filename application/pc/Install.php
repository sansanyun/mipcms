<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\pc;
use think\Controller;
use think\Request;
class Install extends Controller
{
    public function index()
    {
        if (is_file(CONF_PATH.'db'.DS.'install.lock')) {
            header('Location: ' . url('@/'));
            exit();
        }
        if (!defined('__ROOT__')) {
            $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
            define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
        }
        return $this->fetch('default/install/install');
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
                    return jsonError('数据库连接失败');
                }
                $dbconfig['database'] = $dbname;
                $dbconfig['prefix']=trim(input('dbprefix'));
                $tablepre = input("dbprefix");
                $sql = file_get_contents(CONF_PATH.'db'.DS.'mipcms_v_1_0_0.sql');
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
                
                @touch(CONF_PATH.'db'.DS.'install.lock');
                
                if(is_array($dbconfig)){
                    $conf = file_get_contents(CONF_PATH.'db'.DS.'temp.php');
                    foreach ($dbconfig as $key => $value) {
                        $conf = str_replace("#{$key}#", $value, $conf);
                    }
                    $re = file_put_contents(CONF_PATH. '/database.php', $conf);
                    if(file_put_contents(CONF_PATH. '/database.php', $conf)){
                        return jsonSuccess('配置文件写入成功');
                    } else {
                        return jsonError('配置文件写入失败');
                    }
                }
                
        }
    
    }
}
