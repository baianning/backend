<?php
return array(
    // 默认操作名称

    'DB_TYPE'   => 'mysql',// 数据库类型

    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'item', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
    'DB_PORT'   => 3306, // 端口

    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
    'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
    'TMPL_PARSE_STRING'=>array(
        '__ADMINJS__'=>'/Public/Admin/js',//增加后台JS路径
        '__ADMINCSS__'=>'/Public/Admin/css',//增加后台CSS路径
        '__ADMINIMAGE__'=>'/Public/Admin/images',//增加后台IMAGES路径
        '__ADMINLIB__'=>'/Public/Admin/lib',//增加后台LIB路径
        '__ADMINFONTS__'=>'/Public/Admin/fonts',//增加后台FONTS路径
        '__ADMINUED__'=>'/Public/Ueditor',//增加后台Ueditor路径

        "__PUBLIC__"=>"/Public",
    ),
);