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

    //修改密码
    public function editPass(){
        $id = session(C('USER_AUTH_KEY'));
        if(IS_POST){
            $uid =  I('post.id');
            $userpass = I('post.userpass');
            $newuserpass = I('post.newuserpass');

            if(!$uid || $uid != $id){
                $this->error('非法请求');
            }
            $info = M('admin')->field('userpass')->where('id='.$uid)->find();

            if(md5($userpass) != $info['userpass']){
                $this->error('原密码不正确!');
            }
            $data = array(
                'id'=>$uid,
                'userpass'=>md5($newuserpass),
            );
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