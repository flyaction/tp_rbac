<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $this->display();
    }

    public function loginout(){
        session_unset();
        session_destroy();
        $this->redirect(MODULE_NAME.'/Login/index');
    }

    //修改用户
    public function editUser(){
        if(IS_POST){
            $data = array(
                'id'=>I('post.id'),
                'username'=>I('post.username'),
                'realname'=>I('post.realname'),
                'roleid'=>I('post.roleid'),
                'status'=>I('post.status'),
            );
            if($userpass=I('post.userpass')){
                $data['userpass'] = md5($userpass);
            }
            if(M('admin')->save($data)){
                $this->success('修改成功',U(MODULE_NAME.'/Rbac/user'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = session(C('USER_AUTH_KEY'));;
            if(!$id || !$data = M('admin')->where('id='.$id)->find()){
                $this->error('非法请求!');
            }
            $role = M('role')->field('id,name')->select();
            $this->assign('role',$role);
            $this->assign('data',$data);
            $this->display('/Rbac_editUser');
        }
    }

}