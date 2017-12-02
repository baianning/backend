<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Public/Admin/css/font.css">
    <link rel="stylesheet" href="/Public/Admin/css/xadmin.css">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/Public/Admin/js/xadmin.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .user-head-img{
            width: 120px;
            margin-left:100px;
        }
    </style>
</head>

<body>
<div class="x-body">
    <form class="layui-form">
        <!--<form class="layui-form" onsubmit="return checkadd()">-->
        <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>登录名
            </label>
            <div class="layui-input-inline">
                <input type="text" id="username" name="username" required="" lay-verify="required"
                       autocomplete="off" class="layui-input" value="<?php echo ($data["username"]); ?>">
            </div>
            <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>将会成为您唯一的登入名
            </div>
        </div>
        <div class="layui-form-item">
            <label for="phone" class="layui-form-label">
                <span class="x-red">*</span>手机
            </label>
            <div class="layui-input-inline">
                <input type="text" id="phone" name="phone" required="" lay-verify="phone"
                       autocomplete="off" class="layui-input" value="<?php echo ($data["phone"]); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="head_pic" class="layui-form-label">
                <span class="x-red">*</span>头像
            </label>
            <div class="layui-input-inline" id="upBtn">
                <input type="file" id="head_pic" name="head_pic" class="upfile head_input">
            </div>
            <img src="<?php echo ($data["head_pic"]); ?>" alt="" class="user-head-img">
        </div>

        <div class="layui-form-item">
            <label for="introduce" class="layui-form-label">
                <span class="x-red">*</span>简介
            </label>
            <div class="layui-input-inline">
                    <textarea rows="3" cols="20" id="introduce" name="introduce" class="layui-textarea"><?php echo ($data["introduce"]); ?>
                    </textarea>
                <!--<input type="text" id="introduce" name="introduce" class="layui-input">-->
            </div>
        </div>

        <div class="layui-form-item">
            <label for="L_email" class="layui-form-label">
                <span class="x-red">*</span>邮箱
            </label>
            <div class="layui-input-inline">
                <input type="text" id="L_email" name="email" required="" lay-verify="email"
                       autocomplete="off" class="layui-input" value="<?php echo ($data["email"]); ?>">
            </div>
            <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>角色</label>
            <div class="layui-input-block">
                <input type="radio" name="grade" lay-skin="primary" title="超级管理员" value="1" <?php if($data["grade"] == 1): ?>checked<?php endif; ?> >
                <input type="radio" name="grade" lay-skin="primary" title="编辑人员" value="2" <?php if($data["grade"] == 2): ?>checked<?php endif; ?> >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_pass" class="layui-form-label">
                <span class="x-red">*</span>密码
            </label>
            <div class="layui-input-inline">
                <input type="password" id="L_pass" name="password" required="" lay-verify="pass"
                       autocomplete="off" class="layui-input" value="<?php echo ($data["password"]); ?>">
            </div>
            <div class="layui-form-mid layui-word-aux">
                6到16个字符
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
                <span class="x-red">*</span>确认密码
            </label>
            <div class="layui-input-inline">
                <input type="password" id="L_repass" name="repass" required="" lay-verify="repass"
                       autocomplete="off" class="layui-input" value="<?php echo ($data["password"]); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>
            <button  class="layui-btn" lay-filter="add" lay-submit="">
                修改
            </button>
        </div>
    </form>
</div>
<script>

    $(function(){
        $(".head_input").on("change", function(){
            var fil = $(this).prop('files');
            for (var i = 0; i < fil.length; i++) {
                reads(fil[i]);
            }
            function reads(fil) {
                var reader = new FileReader();
                reader.readAsDataURL(fil);
                if (!/image\/\w+/.test(fil.type)) {
                    alert('上传的不是图片');
                    return false;
                }
                if(fil.size > 2 * 1024 * 1024){
                    alert("上传的头像不能超过2M");
                }
                else {
                    reader.onload = function() {
                        $('.user-head-img')
                            .prop("src", reader.result)
                    };
                }
            }
        })
    })

    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer;
        //自定义验证规则
        form.verify({
            nikename: function(value){
                if(value.length < 5){
                    return '昵称至少得5个字符啊';
                }
            }
            ,pass: [/(.+){6,12}$/, '密码必须6到12位']
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }
        });

        //监听提交
        form.on('submit(add)', function(data){
            //发异步，把数据提交给php
            var id = $.trim( $("input[name='id']").val() );
            var username = $.trim( $("input[name='username']").val() );
            var phone = $.trim( $("input[name='phone']").val() );
            var grade = $.trim( $("input[name='grade']:checked").val() );
            var introduce = $.trim( $("textarea[name='introduce']").val() );
            var email = $.trim( $("input[name='email']").val() );
            var password = $.trim( $("input[name='password']").val() );
            var repass = $.trim( $("input[name='repass']").val() );
            var head_pic = $(".user-head-img").attr("src");

            $.ajax({
                type: 'post',
                url: "<?php echo U('Admin/admin_do_edit');?>",
                data:{"id":id,"username":username,"phone":phone,"grade":grade,"introduce":introduce,"email":email,"password":password,"repass":repass,"head_pic":head_pic},
                dataType : "json",
                success: function(data) {
                    if(data.errno==0){
                        layer.alert("修改成功", {icon: 6},function () {
                            // 获得frame索引
                            var index = parent.layer.getFrameIndex(window.name);
                            //关闭当前frame
                            parent.layer.close(index);
                            window.location.href="admin_list";
                        });
                    }else{
                        layer.alert(data.errmeg, {icon: 6});
                    }
                }
            });
            return false;
        });


    });
</script>
<script>var _hmt = _hmt || []; (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();</script>
</body>

</html>