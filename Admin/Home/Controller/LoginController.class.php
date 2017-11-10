<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class LoginController extends Controller {
    public function index(){
    	$this->display();

    }
    public function login(){
    	if(!IS_POST){$this->error("页面不存在");};

    	$username = I('post.username');
    	$userpass = I('post.userpass','','md5');
        $code = I('post.code');
        if(!$this->checkVerify($code)){
            $this->error('验证码不正确!');
        }
    	$user = M('admin')->where(array('username'=>$username))->find();

    	if(!$user){$this->error("用户不存在");}
        if($user['userpass'] != $userpass){$this->error("密码不正确");}
        if($user['status']!=1){$this->error("用户状态异常，不能登录!");}

        $data = array(
            'id' => $user['id'],
            'logintime'=> date('Y-m-d H:i:s',time())
        );

        M('admin')->save($data); //记录最新登录时间

       	session(C('USER_AUTH_KEY'),$user['id']);

       	session('admin_username',$user['username']);
        session('admin_realname',$user['realname']);
        session('admin_roleid',$user['roleid']);

        if($user['username'] == C('RBAC_SUPERADMIN')){
            session(C('ADMIN_AUTH_KEY'), true);
        }

        Rbac::saveAccessList();

    	$this->success('登录成功',U(MODULE_NAME.'/Index/index'));
    }

    public function getVerify(){
        $Verify = new \Think\Verify();
        $Verify->length   = 4;
        $Verify->entry();
    }

    private function checkVerify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }
}