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
  </head>
  
  <body>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">首页</a>
        <a href="">演示</a>
        <a>
          <cite>导航元素</cite></a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" action="admin_list" method="post">

          <input class="layui-input" placeholder="开始日" name="start" id="start" value="<?php echo ($param['start'] ? $param['start'] : ''); ?>">
          <input class="layui-input" placeholder="截止日" name="end" id="end"  value="<?php echo ($param['end'] ? $param['end'] : ''); ?>" >
          <input type="text" name="username"  placeholder="请输入登录名" autocomplete="off" class="layui-input" value="<?php echo ($param['username']); ?>">
          <input type="submit" class="layui-btn"  lay-submit="" lay-filter="sreach" value="查询">
          <select class="layui-select" name="grade" lay-search>
            <option value="">全部</option>
            <option value="1" <?php if($param['grade'] == 1 ): ?>selected<?php endif; ?> >管理员</option>
            <option value="2" <?php if($param['grade'] == 2 ): ?>selected<?php endif; ?> >编辑人员</option>
          </select>
          <!--<button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>-->

        </form>
      </div>
      <xblock>
        <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
        <button class="layui-btn" onclick="x_admin_show('添加用户','admin_add')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:40px">共有数据：<?php echo ($pageary['total']); ?> 条</span>
      </xblock>
      <table class="layui-table">
        <thead>
          <tr>
            <th>
              <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>ID</th>
            <th>登录名</th>
            <th>手机</th>
            <th>邮箱</th>
            <th>角色</th>
            <th>介绍</th>
            <th>加入时间</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>

        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td>
              <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id='<?php echo ($vo["id"]); ?>'><i class="layui-icon">&#xe605;</i></div>
            </td>
            <td><?php echo ($vo["id"]); ?></td>
            <td><?php echo ($vo["username"]); ?></td>
            <td><?php echo ($vo["phone"]); ?></td>
            <td><?php echo ($vo["email"]); ?></td>
            <td><?php if($vo["grade"] == 1 ): ?>超级管理员<?php else: ?>编辑人员<?php endif; ?></td>
            <td><?php echo ($vo["introduce"]); ?></td>
            <td><?php echo (date("Y-m-d H:i:s",$vo['ctime'])); ?></td>
            <td class="td-status">
              <span class="layui-btn layui-btn-normal layui-btn-mini"><?php if($vo["status"] == 1 ): ?>已启用<?php else: ?>已停用<?php endif; ?></span></td>
            <td class="td-manage">
              <a onclick="member_stop(this,'10001')" href="javascript:;"  title="启用">
                <i class="layui-icon">&#xe601;</i>
              </a>
              <a title="编辑"  onclick="x_admin_show('编辑','admin_edit?admin_id=<?php echo ($vo["id"]); ?>')" href="javascript:;">
                <i class="layui-icon">&#xe642;</i>
              </a>
              <a title="删除" onclick="member_del(this,<?php echo ($vo["id"]); ?>)" href="javascript:;">
                <i class="layui-icon">&#xe640;</i>
              </a>
            </td>
          </tr><?php endforeach; endif; else: echo "" ;endif; ?>

        </tbody>
      </table>
      <div class="page">
        <?php echo ($pageary['html']); ?>
      </div>

    </div>
    <script>
      layui.use('laydate', function(){
        var laydate = layui.laydate;
        
        //执行一个laydate实例
        laydate.render({
          elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
          elem: '#end' //指定元素
        });
      });

       /*用户-停用*/
      function member_stop(obj,id){
          layer.confirm('确认要停用吗？',function(index){

              if($(obj).attr('title')=='启用'){

                //发异步把用户状态进行更改
                $(obj).attr('title','停用')
                $(obj).find('i').html('&#xe62f;');

                $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已停用');
                layer.msg('已停用!',{icon: 5,time:1000});

              }else{
                $(obj).attr('title','启用')
                $(obj).find('i').html('&#xe601;');

                $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                layer.msg('已启用!',{icon: 5,time:1000});
              }
              
          });
      }

      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(index){
              $.ajax({
                  type: 'post',
                  url: "<?php echo U('Admin/admin_del');?>",
                  data:{"ids":id},
                  success: function(data) {
                      data = eval("("+data+")");
                      if(data.errno==0){
                          //发异步删除数据
                          $(obj).parents("tr").remove();
                          layer.msg('已删除!',{icon:1,time:1000});
                      }else{
                          layer.msg('删除失败', {icon: 1});
                      }
                  }
              });
          });
      }



      function delAll (argument) {

          var datas = tableCheck.getData();
          layer.confirm('确认要删除吗？'+datas,function(index){
              //捉到所有被选中的，发异步进行删除
//            return false;
              $.ajax({
                  type: 'post',
                  url: "<?php echo U('Admin/admin_del');?>",
                  data:{"ids":datas},
                  success: function(data) {
                      data = eval("("+data+")");
                      if(data.errno==0){
                          layer.msg('删除成功', {icon: 1});
                          $(".layui-form-checked").not('.header').parents('tr').remove();
                      }else{
                          layer.msg('删除失败', {icon: 1});
                      }
                  }
              });


          });
      }
    </script>
    <script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
      })();</script>
  </body>

</html>