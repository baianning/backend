<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>跳转提示</title>
    <link rel="stylesheet" href="/Public/Admin/css/content_common.css"/>
    <link rel="stylesheet" href="/Public/Admin/css/loginHint.css"/>
</head>
<body>

<div id="loginHint">
    <?php if(isset($message)) {?>
    <div class="top l">
        <i><img src="/Public/Admin/images/loginHintSucc.png"/></i>
    </div>
    <?php } else{?>
    <div class="top l">
        <i><img src="/Public/Admin/images/loginHintErr.png"/></i>
    </div>
    <?php } ?>


    <div class="bottom l">
        <?php if(isset($message)) {?>
        <h2> <?php echo($message); ?></h2>
        <?php } else{ ?>
        <h2> <?php echo($error); ?></h2>
        <?php } ?>
        <span>页面自动<a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <i id="wait"><?php echo($waitSecond); ?></i></span>
    </div>

</div>


<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait'), href = document.getElementById('href').href;
        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            if (time <= 0) {
                location.href = href;
                clearInterval(interval);
            }
            ;
        }, 1000);
    })();
</script>
</body>
</html>