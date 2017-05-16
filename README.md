# MIPCMS内容管理系统 介绍
> #### 官网地址
> http://www.mipcms.com
> #### 使用框架：
> MIP、ThinkPHP5.0+ 、Vue2 、element ui、mui
 #### 系统简介：
MIPCMS是一套免费开源的基于百度移动加速器MIP引擎基础上而开发的文章、资讯、内容管理系统，同时该系统也为互联网站长、创业者等群体打造的SEO优化后的建站系统。MIPCMS适合个人博客、新闻、门户、垂直领域、社群等类型需求。
 #### 功能描述：
    * 会员注册登录
    * 文章定时发布
    * 文章评论回复
    * 模块修改标识名称
    * M站 MIP标准模板
    * MIP img图片处理、a标签处理、style内联样式处理等等功能
    * slim富文本编辑器
    * UUID通用唯一识别码 网址
    * 蜘蛛抓取分析趋势图统计
#### 环境要求：
    * PHP >= 5.4.0 （完美支持PHP7）
    * PDO PHP Extension
    * MBstring PHP Extension
    * CURL PHP Extension
#### 注意事项：
    - 初始化账号密码 admin admin 请在后台修改 
    - 安装时请允许upload目录有可写权限
    - 安装时请允许cache目录有可写权限
    - 安装时请允许system\config目录有可写权限
    - 安装时请允许system\config\install目录有可写权限
    - 推荐使用 PHP5.5.6 、PHP7.0

#### 安装教程：
    - 下载源码拷贝到网站根目录
    - 解析绑定域名（主机允许外网访问）
    - 输入数据库的信息 进行安装
    - 安装时 如遇见 '系统错误' 解决方案
        1、请检查PHP版本 必须>=5.4;
        2、Nginx环境请配置伪静态
        3、Apache伪静态规则是否开启
        4、system\config\install目录是否有可写权限
    - 登录后 修改密码
    - 默认网站布局blog模式 如需要cms模式，请在后台切换
    - M站开启方式，后台配置m域名即可
    - 后台统计代码 请输入mip统计代码 （禁止输入非mip支持统计代码）
    - 
#### 手动安装：
    1、 system\config\install  新建 install.lock 文件（不是文件夹）
    2、 在phpMyAdmin 新建数据库，将package\下的 mipcms_v_1_1_0.sql 文件导入
    3、复制\package\database.php文件到system\config\目录下，修改如下标识
        '#hostname#'      修改为 'localhost' 或者 '127.0.0.1' 
        '#database#'      修改为 'mipcms' （你的数据库名称）
        '#username#'      修改为 'root' （你的数据库用户）
        '#hostport#'      修改为 '*******' （你的数据库密码）
        '#prefix#'        修改为 'mip_'
 #### 联系我们：
 > QQ群：576199348