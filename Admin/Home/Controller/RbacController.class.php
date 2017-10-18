<?php
namespace Home\Controller;
use Think\Controller;
class RbacController extends CommonController {
    public function user(){
        $db_prefix = C('DB_PREFIX');
        $user = M('admin')->alias('user')->field('user.id,user.username,user.realname,user.logintime,user.status,role.name as rolename')->join('left join '.$db_prefix.'role as role on role.id=user.roleid')->select();
        $this->assign('user',$user);
        $this->display();
    }

    //添加用户
    public function addUser(){
        if(IS_POST){
            $user = array(
                'username'=>I('post.username'),
                'userpass'=>md5(I('post.userpass')),
                'realname'=>I('post.realname'),
                'roleid'=>I('post.roleid'),
                'addtime'=>date('Y-m-d H:i:s'),
                'status'=>I('post.status'),
            );
            $checkUser = M('admin')->where(array('username'=>$user['username']))->find();
            if($checkUser){
                $this->error("该用户名已经存在");
            }
            if(M('admin')->add($user)){
                $this->success('添加成功',U(MODULE_NAME.'/Rbac/user'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $role = M('role')->select();
            $this->assign('role',$role);
            $this->display();
        }
    }

    //角色列表
    public function role(){
        $role = M('role')->select();
        $this->assign('role',$role);
        $this->display();
    }

    //添加角色
    public function addRole(){
        if(IS_POST){
            $role = array(
                'name'=>I('post.name'),
                'remark'=>md5(I('post.remark')),
                'status'=>I('post.status'),
            );
            if(M('role')->add($role)){
                $this->success('添加成功',U(MODULE_NAME.'/Rbac/role'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->display();
        }
    }

    //菜单列表
    public function menu(){
        $field = array('id','name','title','pid','status');
        $menu = M('node')->field($field)->order('sort')->select();
        //$node = node_merge($node);
        $this->assign('menu',$menu);
        $this->display();
    }

   //修改用户角色
   public function updateUser(){
        $id = I('get.id','','intval');
        //echo $id;die;
        $admin = D('AdminRelation')->relation(true)->where('id='.$id)->select();

        //p($admin);die;
        $this->user = $admin;

        $role = M('role')->select();
        $this->role = $role;  
        $this->display();

   }

   //修改用户角色表单
   public function updateUserHandle(){
      
        $user_id = I('post.id','','intval');
        $role_id = I('post.role_id','','intval');
      
        M('role_user')->where(array('user_id'=>$user_id))->delete();

        $role = array();
        foreach ($_POST['role_id'] as $v){
            $role[] = array(
                'role_id'=>$v,
                'user_id'=>$user_id
            );               
        }

        //p($role);die;
        if(M('role_user')->addAll($role)){

            $this->success('修改成功',U(MODULE_NAME.'/Rbac/index'));
         }else{
            $this->error('修改失败');
            
         } 

        
   }

    //删除用户
    public function deleteUser(){
    
        $id = I('get.id','','intval');
        if($id!=1){
            M('admin')->delete($id);
            $role_user = M('role_user')->where('user_id='.$id)->select();
            if($role_user){
                M('role_user')->where('user_id='.$id)->delete();

            }

            $this->success('删除成功',U(MODULE_NAME.'/Rbac/index'));
        }else{

          $this->error("非法操作");  
        }
        
    }


    //给用户分配角色   
    public function addUserHandle(){
        //p(I('post.'));die;   

        $user = array(
            'admin_name'=>I('username'),
            'admin_pass'=>md5(I('password')),
            'admin_time'=>date('Y-m-d H:i:s',time()),
            'admin_ip'=>get_client_ip() 
        );
        
        
        $a = M('admin')->where(array('admin_name'=>$user['admin_name']))->find();
        if($a){
            $this->error("用户已经存在");
        }
        
                   
        $role = array();
        if($uid = M('admin')->add($user)){
            foreach ($_POST['role_id'] as $v){
                $role[] = array(
                    'role_id'=>$v,
                    'user_id'=>$uid
                );
                    
            }
                
            M('role_user')->addAll($role);
            $this->success('添加成功',U(MODULE_NAME.'/Rbac/index'));
         }else{
            $this->error('添加失败');
            
         }
                    
    }

    //修改角色
     public function updateRole(){
      $role_id = I('rid','','intval');
      $role = M('role')->where('id='.$role_id)->select();

      $this->role = $role;
      $this->display();

   }

   //修改角色
     public function updateRoleHandle(){
        $data = array(
            'id'=>I('rid','','intval'),
            'name'=>I('name'),
            'remark'=>I('remark'),
            'status'=>I('status')
        );

        //p($data);die;
        $role= M('role');
        if($role->save($data)){

            //$this->success('修改成功',U(MODULE_NAME.'/MsgManage/update',array('id'=>$data[id])));
            $this->success('修改成功',U(MODULE_NAME.'/Rbac/role'));
        
        }else{
            $this->error('修改失败');

        }

   }

    //配置权限
    public function access(){
            
            $rid = I('rid',0,'intval');
            $field = array('id','name','title','pid');
            
            $node = M('node')->order('sort')->field($field)->select();

            $access = M('access');
            $access = $access->where(array('role_id'=>$rid))->getField('node_id',true); 
            $node = node_merge($node,$access);
            
            //p($access);
            //p($node);die;
            
            $this->node = $node;
            $this->rid = $rid;

            $this->display();
            
            
    }

    //添加权限给角色
    public function setAccess(){
            $rid = I('rid',0,'intval');
            $access = M('access');
            $access ->where(array('role_id'=>$rid))->delete();
            $data = array();
            foreach ($_POST['access'] as $v){
                $tmp = explode("_",$v);
                $data[] = array(
                    'role_id'=>$rid,
                    'node_id'=>$tmp[0],
                    'level'=>$tmp[1]
                );
                
            }
            
            if($access->addAll($data)){
                $this->success('修改成功',U(MODULE_NAME.'/Rbac/role'));
               
            }else{
               $this->error('修改失败');
               
            }
            
            
            
    }
        


    //节点列表
    public function node(){
        $field = array('id','name','title','pid','status');
        $node = M('node')->field($field)->order('sort')->select();
        //p($node);
        $node = node_merge($node);  
        
        $this->node= $node;
        //p($this->node);
        $this->display();
             
    }

   	//添加节点
   	public function addNode(){
        //p(I('get.'));die;  
   		
        $this->pid = I('pid',0,'intval');
        $this->level = I('level',1,'intval');
            switch($this->level){
                case 1:
                    $this->type = "应用";
                    break;
                case 2:
                    $this->type = "控制器";
                    break;
                case 3:
                    $this->type = "动作方法";
                    break;
    		}
        $this->display();
	}

    //删除节点
    public function deleteNode(){
        $uid = I('uid','','intval');
        $level = I('level','','intval');
        if($level==3){

            M('node')->where('id='.$uid)->delete();
            $access_id = M('access')->where('node_id='.$uid)->find();
            if($access){
                M('access')->where('node_id='.$uid)->delete();
            }
            $this->success('删除成功',U(MODULE_NAME.'/Rbac/node'));
        }elseif($level==2){
            $node_id = M('node')->where('pid='.$uid)->select();
            if($node_id){
               $this->error('请先将下面的方法删除'); 

            }else{

                M('node')->where('id='.$uid)->delete();
                $access_id = M('access')->where('node_id='.$uid)->find();
                if($access){
                    M('access')->where('node_id='.$uid)->delete();
                }
                $this->success('删除成功',U(MODULE_NAME.'/Rbac/node'));

            }

        }elseif($level==1){

            $node_id = M('node')->where('pid='.$uid)->select();
            if($node_id){
               $this->error('请先将下面的控制器删除'); 

            }else{

                M('node')->where('id='.$uid)->delete();
                $access_id = M('access')->where('node_id='.$uid)->find();
                if($access){
                    M('access')->where('node_id='.$uid)->delete();
                }
                $this->success('删除成功',U(MODULE_NAME.'/Rbac/node'));


            }

        }else{

            $this->error('错误的请求！！');  
        }

    }

    //修改节点
    public function updateNode(){
        //p(I('get.'));die;  
        $uid = I('uid','','intval');
        $level = I('level','','intval');
        switch($level){
                case 1:
                    $this->type = "应用";
                    break;
                case 2:
                    $this->type = "控制器";
                    break;
                case 3:
                    $this->type = "动作方法";
                    break;
        }

        $node = M('node')->where('id='.$uid)->select();
        //p($node);
        $this->node = $node;
        $this->display();
    }

    //修改节点
    public function updateNodeHandle(){
        $data = array(
            'id'=>I('post.uid','','intval'),
            'name'=>I('post.name'),
            'title'=>I('post.title'),
            'status'=>I('post.status'),
            'sort'=>I('post.sort'),
        );

        $node = M('node');
        if($node->save($data)){
            $this->success('修改成功',U(MODULE_NAME.'/Rbac/node'));
        
        }else{
            $this->error('修改失败');

        }
    }

	//添加节点表单
	public function addNodeHandle(){
        //p($_POST);die;  
             
        if(M('node')->add($_POST)){
            $this->success('添加成功',U(MODULE_NAME.'/Rbac/node','',''));
               
        }else{
            $this->error('添加失败');
               
        }
     
    }	


}