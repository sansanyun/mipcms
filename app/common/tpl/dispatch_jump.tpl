{__NOLAYOUT__}<!DOCTYPE html>
<html mip>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        <title>跳转提示</title>
        <link rel="canonical" href="https://m.baidu.com/">
        <style mip-custom>
        
        .page {
            width: 100%;
            margin: 0 auto;
            text-align: center;
            padding-top: 3rem;
        }
        .page .success,.page .error{
            font-size: 2rem;
            text-align: center;
            position: relative;
            padding: 1rem;
        }
        .success {
            color: #00AAEE;
        }
        .error {
            color: #FF4949;
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
        .bottom {
        	margin-top: 2rem;
        }
        </style>
    </head>
    <body>
    <div class="page">
        <?php switch ($code) {?>
            <?php case 1:?>
               <div class="success">
                    <?php echo(strip_tags($msg));?>
               </div>
            	<?php break;?>
            <?php case 0:?>
               <div class="error">
                    <?php echo(strip_tags($msg));?>
               </div>
            <?php break;?>
        <?php } ?>
        	
      	<p class="jump">页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b></p>
        <p class="bottom">Copyright &copy; Powered By <a href="http://www.mipcms.com" title="MIPCMS内容管理系统" target="_blank">MIPCMS内容管理系统</a></p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
    </body>
</html>