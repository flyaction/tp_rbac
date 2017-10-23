<?php
return array(
    'TMPL_FILE_DEPR'=>'_', //模板路径
    'URL_HTML_SUFFIX'=>'',// URL伪静态后缀设置,去掉后缀.html
    'TMPL_PARSE_STRING'=>array(
        '__PUBLIC__'=>__ROOT__.'/'.APP_NAME.'/'.MODULE_NAME.'/View',
    ),

    //是否开启日志
    'ACTION_OPEN_LOG'=>1,//1 开启

    //RBAC
    'RBAC_SUPERADMIN' => 'admin',
    'ADMIN_AUTH_KEY' => 'superadmin',
    'USER_AUTH_ON' => true, //是否需要认证
    'USER_AUTH_TYPE' => 1,//认证类型,1:登陆验证2:时时验证
    'USER_AUTH_KEY' => 'uid',//认证识别号
    'NOT_AUTH_MODULE' => 'Index',//无需认证模块

    'RBAC_ROLE_TABLE' => C('DB_PREFIX').'role', //角色表名称
    'RBAC_USER_TABLE' => C('DB_PREFIX').'admin',//用户表名称
    'RBAC_ACCESS_TABLE' => C('DB_PREFIX').'access',//权限表名称
    'RBAC_NODE_TABLE' => C('DB_PREFIX').'node',//节点表名称

    'TMPL_ACTION_ERROR' => THINK_PATH . 'Tpl/action_jump.tpl',
    'TMPL_ACTION_SUCCESS' => THINK_PATH . 'Tpl/action_jump.tpl',
    'TMPL_EXCEPTION_FILE' => THINK_PATH.'Tpl/my_think_exception.tpl',

);