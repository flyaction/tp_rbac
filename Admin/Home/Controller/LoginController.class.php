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
		if(I('code')!= $_SESSION['ckverify']){
    		
    		$this->error("验证码错误");
		}
		
    	$username = I('post.username');
    	$password = I('post.password','','md5');
    	$user = M('admin');
    	$user = $user->where(array('admin_name'=>$username))->find();

    	if(!$user){$this->error("用户不存在");}
        if($user['admin_pass'] != $password){$this->error("密码不正确");}
        if($user['admin_status']!=1){$this->error("用户被锁定，不能登录！");}

        $data = array(
            'id' => $user['id'],
            'admin_time'=> date('Y-m-d H:i:s',time()),
            'admin_ip' => get_client_ip()
        );

        //print_r($data);die;


        M('admin')->save($data);

        //echo C('RBAC_SUPERADMIN');die;
       // echo C('USER_AUTH_KEY');die;

       	session(C('USER_AUTH_KEY'),$user['id']);
        //session('uid',$user['id']);
       	session('admin_name',$user['admin_name']);
       	session('admin_time',$user['admin_time']);
       	session('admin_ip',$user['admin_ip']); 
        
        if($user['admin_name'] == C('RBAC_SUPERADMIN')){
            session(C('ADMIN_AUTH_KEY'), true);
        }

        //import('ORG.Util.RBAC::saveAccessList()');
        
        Rbac::saveAccessList();
        //echo C('USER_AUTH_KEY');die;
        //p($_SESSION);die;
        
    	$this->success('登录成功',U(MODULE_NAME.'/Index/index'));
    }

    public function verify(){
                
         	
		header("content-type:image/png");  	  //设置创建图像的格式
		$image_width=100;                      //设置图像宽度
		$image_height=30;                     //设置图像高度
		srand(microtime()*100000);         	  //设置随机数的种子
		for($i=0;$i<4;$i++){                  //循环输出一个4位的随机数
		   $new_number.=dechex(rand(0,15));
		}
		$_SESSION['ckverify']=$new_number;    //将获取的随机数验证码写入到SESSION变量中     
		
		$num_image=imagecreate($image_width,$image_height);  //创建一个画布
		imagecolorallocate($num_image,255,255,255);     	 //设置画布的颜色
		for($i=0;$i<strlen($_SESSION[ckverify]);$i++){  //循环读取SESSION变量中的验证码
		   $font=mt_rand(3,5);                            	//设置随机的字体
		   $x=mt_rand(1,8)+$image_width*$i/4;               //设置随机字符所在位置的X坐标
		   $y=mt_rand(1,$image_height/4);                   //设置随机字符所在位置的Y坐标
		   $color=imagecolorallocate($num_image,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200));  	 //设置字符的颜色
		   imagestring($num_image,$font,$x,$y,$_SESSION[ckverify][$i],$color);				     //水平输出字符
		}
		imagepng($num_image);      			//生成PNG格式的图像
		imagedestroy($num_image);  			//释放图像资源
	
	
    }
	public function checkLogin(){
		
		if(!IS_POST){$this->error('非法请求');}

		$username = I('post.username');
    	$userpass = I('post.userpass','','md5');
    	$code = I('post.code');

    	if($code != $_SESSION['ckverify']){
    		$data['stat'] = 4; //验证码错误
		}else{

	    	$user = M('admin');
	    	$user = $user->where(array('admin_name'=>$username))->find();
			
			if(!$user || ($user['admin_pass'] != $userpass)){
				$data['stat'] = 2; //用户名或密码错误
			}elseif($user['admin_status']!=1){
				$data['stat'] = 3; //该用户被停用
			}else{
				$data['stat'] = 1; //登录成功
			}
		}
        
		
		$this->ajaxReturn($data);
	
	}

    public function ceshi(){

        $this->display();
    }

    public function _empty() {  
        R('Empty/_empty');  
    }  
}