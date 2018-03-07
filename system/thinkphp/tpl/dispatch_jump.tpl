{__NOLAYOUT__}<!DOCTYPE html>
<html mip>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        <link rel="stylesheet" type="text/css" href="https://mipcache.bdstatic.com/static/v1/mip.css">
        <title>页面不存在</title>
        <link rel="canonical" href="https://m.baidu.com/">
        <style mip-custom>
        .page {
            width: 100%;
            margin: 0 auto;
            text-align: center;
            padding-top: 5vw;
        }
        .page div{
            font-size: 2vw;
            text-align: center;
            color: #FF4949;
            padding-bottom: 2vw;
            position: relative;
        }
        .success {
            color: #00AAEE;
        }
        .page img{
            margin: 0 auto;
            display: block;

        }
        .page p{
            margin-bottom: 15px;
            font-size: 14px;
            color: #aaa;
        }
        .page a{
            color: #aaa;
        }
        </style>
    </head>
    <body>
    <div class="page">
        <?php switch ($code) {?>
            <?php case 1:?>
            <div>
              
               <div class="success">
                    <?php echo(strip_tags($msg));?>
               </div>
                <p><a href="<?php echo($domain.$url);?>">点击跳转</a></p>
            <?php break;?>
            <?php case 0:?>
            <div>
               <div>
                    <?php echo(strip_tags($msg));?>
               </div>
            </div>
            <p><a href="<?php echo($domain.$url);?>">点击跳转</a></p>
            <?php break;?>
        <?php } ?>
        <p>Copyright &copy; Powered By <a href="http://www.mipcms.com" title="MIPCMS内容管理系统" target="_blank">MIPCMS内容管理系统</a></p>
    </div>
        <script src="https://mipcache.bdstatic.com/static/v1/mip.js"></script>
        <script src="https://mipcache.bdstatic.com/static/v1/mip-stats-baidu/mip-stats-baidu.js"></script>
    </body>
</html>