<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <title>新闻编辑</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/Public/Admin/css/font.css">
    <link rel="stylesheet" href="/Public/Admin/css/xadmin.css">
      <script type="text/javascript" charset="utf-8" src="/Public/Ueditor/ueditor.config.js"></script>
      <script type="text/javascript" charset="utf-8" src="/Public/Ueditor/ueditor.all.min.js"> </script>
      <script type="text/javascript" charset="utf-8" src="/Public/Ueditor/lang/zh-cn/zh-cn.js"></script>

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
        <form class="layui-form" onsubmit="return checkadd()">
            <input type="hidden" name="news_id" value="<?php echo ($news_detail["news_id"]); ?>">
          <div class="layui-form-item">
              <label for="title" class="layui-form-label">
                  <span class="x-red">*</span>文章标题
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="title" name="title" required="" lay-verify="title"
                  autocomplete="off" class="layui-input" value="<?php echo ($news_detail["title"]); ?>">
              </div>
          </div>
            <div class="layui-form-item">
                <label for="cover_img" class="layui-form-label">
                    <span class="x-red">*</span>封面图
                </label>
                <div class="layui-input-inline" id="upBtn">
                    <input type="file" id="cover_img" name="cover_img" class="upfile head_input">
                </div>
                <img src="<?php echo ($news_detail["cover_img"]); ?>" alt="" class="user-head-img">
            </div>
            <div class="layui-form-item">
                <label for="profile" class="layui-form-label">
                    <span class="x-red">*</span>简介
                </label>
                <div class="layui-input-inline">
                    <input type="profile" id="profile" name="profile" required="" lay-verify="profile"
                           autocomplete="off" class="layui-input" value="<?php echo ($news_detail["profile"]); ?>" >
                </div>
            </div>
          <div class="layui-form-item">
              <label class="layui-form-label">
                  <span class="x-red">*</span>内容
              </label>
              <div class="layui-input-inline">
                  <script id="editor" type="text/plain" style="width:1024px;height:500px;"><?php echo ($news_detail["content"]); ?></script>
<!--                  <input type="text" id="content" name="content" required="" lay-verify="content"
                  autocomplete="off" class="layui-input">-->
              </div>
          </div>

          <div class="layui-form-item">
              <label for="source" class="layui-form-label">
                  <span class="x-red">*</span>来源
              </label>
              <div class="layui-input-inline">
                  <input type="source" id="source" name="source" required="" lay-verify="source"
                  autocomplete="off" class="layui-input" value="<?php echo ($news_detail["source"]); ?>" >
              </div>
          </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>文章等级</label>
                    <div class="layui-input-block">
                        <input type="radio" name="grade" lay-skin="primary" title="三级" value="3" <?php if($news_detail["grade"] == 3): ?>checked<?php endif; ?> >
                        <input type="radio" name="grade" lay-skin="primary" title="二级" value="2" <?php if($news_detail["grade"] == 2): ?>checked<?php endif; ?> >
                        <input type="radio" name="grade" lay-skin="primary" title="一级" value="1" <?php if($news_detail["grade"] == 1): ?>checked<?php endif; ?> >
                    </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>文章分类</label>
                <div class="layui-input-block">
                    <select name="category_id" lay-verify="required">
                        <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($vo['id'] == $news_detail['cate_id']): ?>selected<?php endif; ?> ><?php echo ($vo["category"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>启用</label>
                <div class="layui-input-block">
                    <input type="radio" name="status" lay-skin="primary" title="是" checked="" value="1">
                    <input type="radio" name="status" lay-skin="primary" title="否" value="0">
                </div>
            </div>
          <div class="layui-form-item">
              <label  class="layui-form-label">
              </label>
              <input type="submit" class="layui-btn" lay-filter="add" lay-submit="" value="修改">
<!--              <button  class="layui-btn" lay-filter="add" lay-submit="">
                  增加
              </button>-->
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

        var ue = UE.getEditor('editor');
        function checkadd() {
            var news_id = $("input[name='news_id']").val();
            var title = $("input[name='title']").val();
            var profile = $("input[name='profile']").val();
            var grade = $("input[name='grade']:checked").val();
            var content = UE.getEditor('editor').getContent();
            var source = $("input[name='source']").val();
            var status = $("input[name='status']").val();
            var category_id = $("select[name='category_id']").val();
            var cover_img = $(".user-head-img").attr("src");

            //添加信息
            $.ajax({
                type: 'post',
                url: "<?php echo U('News/news_do_edit');?>",
                data:{"news_id":news_id,"title":title,"profile":profile,"grade":grade,"content":content,"source":source,"status":status,"category_id":category_id,"cover_img":cover_img},
                success: function(data) {
                    data = eval("("+data+")");
                    if(data.errno==0){
                        alert("操作成功");
                        window.location.href="news_list";
                    }else{
                        alert(data.errmeg);
                    }
                }
            });
            return false;
        }
/*        layui.use(['form','layer'], function(){
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
            console.log(data);
            //发异步，把数据提交给php
            layer.alert("增加成功", {icon: 6},function () {
                // 获得frame索引
                var index = parent.layer.getFrameIndex(window.name);
                //关闭当前frame
                parent.layer.close(index);
            });
            return false;
          });


        });*/
    </script>
  </body>

</html>