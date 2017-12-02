<?php
return array(
    'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump', //成功页面
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',  //失败页面
);
return array_merge(include './Conf/config.php',$conf);