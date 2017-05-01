MIPCMS 修改记录
===============

>app 460行
```
$path = APP_PATH . $module;

//自定义转移common目录
$config_path = CONF_PATH. $module;

// 加载模块配置

495行

// 加载公共文件
if (is_file($path . 'common' . EXT)) {
    include $path . 'common' . EXT;
}

//自定义转移common目录
if (is_file($config_path . 'common' . EXT)) {
    include $config_path . 'common' . EXT;
}

// 加载当前模块语言包
if ($module) {
    Lang::load($path . 'lang' . DS . Request::instance()->langset() . EXT);
}

```

>loader.php  538
```

if($layer=='model'){
    return App::$namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $path . $class;
}else{
    return App::$namespace . '\\' . ($module ? $module . '\\' : '') . $path . $class;
}
```

>template 1036
```
if (0 === strpos($templateName, '$')) {
    //支持加载变量文件名
    $templateName = $this->get(substr($templateName, 1));
}
```
```$xslt
    新增
     'view_name'          => '',
     1087
      $path = isset($module) ? APP_PATH . $module . DS . basename($this->config['view_path']) . DS : $this->config['view_path'] . $this->config['view_name'] . DS;
```

```$xslt
1091 新增
if('css'==pathinfo($template, PATHINFO_EXTENSION)){
    $template =  $this->config['view_path'] . $this->config['view_name'] . DS .$template ;
}
```
>controller 139 新增加  $this->config调用
```
    protected function config($name, $value = '')
    {
        $this->view->config($name, $value);
    }
```
