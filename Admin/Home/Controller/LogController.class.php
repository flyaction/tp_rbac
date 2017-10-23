<?php
namespace Home\Controller;
use Think\Controller;
class LogController extends CommonController {
    public function index(){
        $db_prefix = C('DB_PREFIX');
        $user = M('admin')->alias('user')->field('user.id,user.username,user.realname,user.logintime,user.status,role.name as rolename')->join('left join '.$db_prefix.'role as role on role.id=user.roleid')->select();
        $this->assign('user',$user);
        $this->display();
    }
}