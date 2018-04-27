<?php
if (!version_compare(PHP_VERSION,'5.4.0','ge')) {
    echo '您当前使用的PHP版本为：' . PHP_VERSION . '系统最低要求PHP5.4 建议使用PHP7.0版本！';
}
define('MIP_HOST',true);
defined('MIP_ROOT') or define('MIP_ROOT', __DIR__ . '/');
// 定义应用目录
define('APP_PATH', __DIR__ . '/app/');
define('PUBLIC_PATH', __DIR__ . '/public/');
define('VENDOR_PATH',__DIR__ . '/system/vendor/');
// 加载框架引导文件
require __DIR__ . '/system/thinkphp/start.php';