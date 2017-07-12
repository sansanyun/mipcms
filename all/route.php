<?php
namespace app\route;
use think\Route;
use think\DB;
if (!is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
    Route::rule('/','pc/Install/index');
    Route::rule('/install','pc/Install/index');
    Route::rule('/install/installPost','pc/Install/installPost');
} else {
    $settings = db('Settings')->select();
    foreach ($settings as $k => $v) {
        $mipInfo[$v['key']] = $v['val'];
    }

    require 'route_admin.php';
    if ($mipInfo['mipDomain']) {
        require 'route_m.php';
    }
    require 'route_pc.php';

    $tpl_path = ROOT_PATH . 'template';
    foreach (fetch_file_lists($tpl_path) as $key => $file) {
        if (strstr($file,'route.php')) {
            require_once $file;
        }
    }
}