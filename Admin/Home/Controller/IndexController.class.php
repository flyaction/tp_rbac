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
        $id = session(C('USER_AUTH_KEY'));
        if(IS_POST){
            $data = array(
                'id'=>I('post.id'),
            );
            if(!$id || $id != $data['id']){
                $this->error('非法请求');
            }
            if($userpass=I('post.userpass')){
                $data['userpass'] = md5($userpass);
            }
            if(M('admin')->save($data)){
                $this->success('修改成功',U(MODULE_NAME.'/Index/index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            if(!$id || !$data = M('admin')->where('id='.$id)->find()){
                $this->error('非法请求!');
            }
            $this->assign('data',$data);
            $this->display();
        }
    }

}