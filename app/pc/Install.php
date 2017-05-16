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
        if (is_file(CONF_PATH.'install'.DS.'install.lock')) {
            header('Location: ' . url('@/'));
            exit();
        }
        if (!defined('__ROOT__')) {
            $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
            define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
        }
        return '<!DOCTYPE html>
                <html>
                <head>
                  <meta charset="UTF-8">
                  <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
                  <style type="text/css">
                    body {
                        padding: 0;
                        margin: 0;
                    }
                  </style>
                </head>
                <body>
                  <div id="app">
                    <el-row :gutter="24" style="margin: 0;">
                      <el-col :span="6">&nbsp;</el-col>
                      <el-col :span="12">
                          <h2 style="text-align: center;">欢迎使用MIPCMS内容管理系统</h2>
                          <p style="text-align: center;">交流QQ群576199348</p>
                          <p style="text-align: center;"><a href="http://demo.mipcms.com/article/c0de4338a4625a748932f2e8.html">安装不成功？点击查看原因</a></p>
                          <el-form ref="form" :rules="rules" :model="form" label-width="120px">
                              <el-form-item label="数据库地址" prop="dbhost">
                                <el-input v-model="form.dbhost"></el-input>
                              </el-form-item>
                              <el-form-item label="数据库端口" prop="dbport">
                                <el-input v-model="form.dbport"></el-input>
                              </el-form-item>
                              <el-form-item label="数据库用户名" prop="dbuser">
                                <el-input v-model="form.dbuser"></el-input>
                              </el-form-item>
                              <el-form-item label="数据库密码" prop="dbpw">
                                <el-input type="password" v-model="form.dbpw"></el-input>
                              </el-form-item>
                              <el-form-item label="数据库名称" prop="dbname">
                                <el-input v-model="form.dbname"></el-input>
                              </el-form-item>
                              <el-form-item label="数据表前缀" prop="dbprefix">
                                <el-input v-model="form.dbprefix"></el-input>
                              </el-form-item>
                              <el-form-item>
                                <el-button type="primary" @click="submitForm(\'form\')">立即创建</el-button>
                              </el-form-item>
                            </el-form>
                      </el-col>
                    </el-row>
                    
                  </div>
                </body>
                </html>
                    <script src="https://unpkg.com/vue/dist/vue.js"></script>
                    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
                    <script src="/assets/js/axios.min.js"></script>
                    <script src="/assets/js/utils.js"></script>
                    <script src="/assets/js/install.js"></script>
                    <script>
                    var _hmt = _hmt || [];
                    (function() {
                      var hm = document.createElement("script");
                      hm.src = "https://hm.baidu.com/hm.js?176a0355c10aafbb44f1f5838bb6275d";
                      var s = document.getElementsByTagName("script")[0]; 
                      s.parentNode.insertBefore(hm, s);
                    })();
                    </script>';
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
                $sql = file_get_contents(ROOT_PATH.'package'.DS.'mipcms_v_1_1_0.sql');
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
                    touch(CONF_PATH.'install'.DS.'install.lock');
                } catch (\PDOException $e) {
                    return jsonError('install.lock文件写入失败，请检查system/config/install 文件夹是否可写入');
                }
                if(is_array($dbconfig)){
                    $conf = file_get_contents(ROOT_PATH.'package'.DS.'database.php');
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
