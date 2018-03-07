MIPCMS 修改记录
--------------------
1、composer update
2、修改system\thinkphp中的base文件 21-34替换如下代码：
```
    defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
    defined('ROOT_PATH') or define('ROOT_PATH', dirname(realpath(APP_PATH)) . DS);
    defined('SYSTEM_PATH') or define('SYSTEM_PATH', dirname(THINK_PATH) . DS);
    defined('EXTEND_PATH') or define('EXTEND_PATH', ROOT_PATH . 'all' . DS);
    defined('VENDOR_PATH') or define('VENDOR_PATH', SYSTEM_PATH . 'vendor' . DS);
    defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'cache' . DS);
    defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
    defined('CACHE_PATH') or define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);
    defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);
    defined('CONF_PATH') or define('CONF_PATH', SYSTEM_PATH . 'config' . DS); // 配置文件目录
    defined('ALL_PATH') or define('ALL_PATH', ROOT_PATH . 'all' . DS); // 全局文件目录
    defined('PUBLIC_PATH') or define('PUBLIC_PATH', ROOT_PATH . 'public' . DS); // 公开文件目录
    defined('CONF_EXT') or define('CONF_EXT', EXT); // 配置文件后缀
    defined('ENV_PREFIX') or define('ENV_PREFIX', 'PHP_'); // 环境变量的配置前缀
```
3、system\thinkphp\library\think\App 283行 添加如下代码
```
    //自定义转移common目录 
    $config_path = ALL_PATH . $module;
    if (is_file($config_path . 'common' . EXT)) {
        include $config_path . 'common' . EXT;
    }
```
```
    628行替换 备注说明：将默认的config中路由文件转移到all文件中
    foreach ($files as $file) {
        if (is_file(ALL_PATH . $file . CONF_EXT)) {
            // 导入路由配置
            $rules = include ALL_PATH . $file . CONF_EXT;
            is_array($rules) && Route::import($rules);
        }
    }
    ```
4、system\thinkphp\library\think\App 1074行 修改
```
//$path = isset($module) ? APP_PATH . $module . DS . basename($this->config['view_path']) . DS : $this->config['view_path'];
    $path = isset($module) ? APP_PATH . $module .  DS . 'view' . DS . $module . DS : $this->config['view_path'] . Config::get('view_name') . DS;
```
    1079行 加入以下代码

    ```
    if('css'==pathinfo($template, PATHINFO_EXTENSION)){
            if (strpos($template, '@')) {
                list($module, $template) = explode('@', $template);
                $template =  APP_PATH . $module .  DS . 'view' . DS . $module . DS . $template ;
            } else {
                $template =  $this->config['view_path'] . $this->config['view_name'] . DS .$template ;
            }
        }

        if('js'==pathinfo($template, PATHINFO_EXTENSION)){
             if (strpos($template, '@')) {
                list($module, $template) = explode('@', $template);
                $template =  APP_PATH . $module .  DS . 'view' . DS . $module . DS .$template ;
            } else {
                $template =  $this->config['view_path'] . $this->config['view_name'] . DS .$template ;
            }
        }
    ```

