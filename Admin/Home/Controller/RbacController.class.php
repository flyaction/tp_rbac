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
            $role = M('role')->where('status=1')->select();
            $this->assign('role',$role);
            $this->display();
        }
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
            $id = I('get.id',0,'intval');
            if(!$id || !$data = M('admin')->where('id='.$id)->find()){
                $this->error('非法请求!');
            }
            $role = M('role')->field('id,name')->select();
            $this->assign('role',$role);
            $this->assign('data',$data);
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
            $data = array(
                'name'=>I('post.name'),
                'remark'=>I('post.remark'),
                'status'=>I('post.status'),
            );
            if(M('role')->add($data)){
                $this->success('添加成功',U(MODULE_NAME.'/Rbac/role'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->display();
        }
    }
    //修改角色
    public function editRole(){
        if(IS_POST){
            $data = array(
                'id'=>I('post.id'),
                'name'=>I('post.name'),
                'remark'=>I('post.remark'),
                'status'=>I('post.status'),
            );
            if(M('role')->save($data)){
                $this->success('修改成功',U(MODULE_NAME.'/Rbac/role'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = I('get.id',0,'intval');
            if(!$id || !$data = M('role')->where('id='.$id)->find()){
                $this->error('非法请求!');
            }
            $this->assign('data',$data);
            $this->display();
        }
    }

    //角色分配
    public function assignRole(){
        if(IS_POST){
            $id = I('post.id');
            $access = I('post.access');
            $data = [];
            foreach ($access as $v){
                $tmp = explode("_",$v);
                $data[] = array(
                    'role_id'=>$id,
                    'node_id'=>$tmp[0],
                    'level'=>$tmp[1]
                );
            }
            if(!$data){
                $this->error('配置不能为空!');
            }

            $handleDel = true;//清空角色之前配置的权限操作
            $access = M('access');
            if($access->where(array('role_id'=>$id))->find()){
                $handleDel = $access ->where(array('role_id'=>$id))->delete();
            }

            if($handleDel && $access->addAll($data)){
                $this->success('配置成功!',U(MODULE_NAME.'/Rbac/role'));
            }else {
                $this->error('配置失败!');
            }

        }else{
            $id = I('get.id',0,'intval');
            if(!$id || !$data = M('role')->where('id='.$id)->find()){
                $this->error('非法请求!');
            }
            $node = M('node')->order('sort')->select();
            $menu = [];
            foreach($node as $k=>$v){
                if($v['pid'] == 0){
                    if(isset($menu[$v['id']])){
                        $menu[$v['id']] = array_merge($v,$menu[$v['id']]);;
                    }else{
                        $menu[$v['id']] = $v;
                    }
                }else{
                    $menu[$v['pid']]['list'][$v['id']] = $v;
                }
            }

            $access = M('access')->field('node_id')->where('role_id='.$id)->select();
            if($access){
                $access = array_column($access,'node_id');
            }
            $this->assign('menu',$menu);
            $this->assign('data',$data);
            $this->assign('access',$access);

            $this->display();
        }
    }

    //删除角色
    public function delRole(){
        if(IS_POST){
            $id = I('post.id');
            $data = M('role')->where('id='.$id)->find();
            $return = [];//预设返回
            $return['status'] = 0;
            if(!$data){
                $return['errmsg'] = '数据不存在!';
                $this->ajaxReturn($return);
            }
            $access = true;
            if($sdata = M('access')->where('role_id='.$id)->find()){
                if(M('access')->where('role_id='.$id)->delete() == false){
                    $access = false;
                }
            }
            if($access && M('role')->where('id='.$id)->delete()){
                $return['status'] = 1;
                $this->ajaxReturn($return);
            }else{
                $return['errmsg'] = '删除失败!';
                $this->ajaxReturn($return);
            }
        }
    }

    //菜单列表
    public function menu(){
        $node = M('node')->order('sort')->select();
        $menu = [];
        foreach($node as $k=>$v){
            if($v['pid'] == 0){
                if(isset($menu[$v['id']])){
                    $menu[$v['id']] = array_merge($v,$menu[$v['id']]);;
                }else{
                    $menu[$v['id']] = $v;
                }
            }else{
                $menu[$v['pid']]['list'][$v['id']] = $v;
            }
        }
        $this->assign('menu',$menu);
        $this->display();
    }

    //添加菜单
    public function addMenu(){
        if(IS_POST){
            $data = array(
                'title'=>I('post.title'),
                'pid'=>I('post.pid'),
                'controller'=>I('post.controller'),
                'action'=>I('post.action'),
                'sort'=>I('post.sort'),
                'remark'=>I('post.remark'),
                'show'=>I('post.show'),
            );
            $data['level'] = $data['pid'] ? 2 : 1;
            if(M('node')->add($data)){
                $this->success('添加成功',U(MODULE_NAME.'/Rbac/menu'));
            }else{
                $this->error('添加失败');
            }

        }else{
            $node = M('node')->field('id,controller,action,title')->order('sort')->where(array('pid'=>0))->select();
            $this->assign('node',$node);
            $this->display();
        }
    }

    //删除菜单节点
    public function delMenu(){
        if(IS_POST){
            $id = I('post.id');
            $data = M('node')->where('id='.$id)->find();
            $return = [];//预设返回
            $return['status'] = 0;
            if(!$data){
                $return['errmsg'] = '数据不存在!';
                $this->ajaxReturn($return);
            }
            if($data['level'] == 1){
                $sdata = M('node')->where('pid='.$id)->find();
                if($sdata){
                    $return['errmsg'] = '请先将下级菜单删除!';
                    $this->ajaxReturn($return);
                }
            }
            if(M('node')->where('id='.$id)->delete()){
                $return['status'] = 1;
                $this->ajaxReturn($return);
            }else{
                $return['errmsg'] = '删除失败!';
                $this->ajaxReturn($return);
            }
        }
    }

    //修改菜单
    public function editMenu(){
        if(IS_POST){
            $data = array(
                'id'=>I('post.id'),
                'title'=>I('post.title'),
                'pid'=>I('post.pid'),
                'controller'=>I('post.controller'),
                'action'=>I('post.action'),
                'sort'=>I('post.sort'),
                'remark'=>I('post.remark'),
                'show'=>I('post.show'),
            );
            $data['level'] = $data['pid'] ? 2 : 1;
            if(M('node')->save($data)){
                $this->success('修改成功',U(MODULE_NAME.'/Rbac/menu'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = I('get.id',0,'intval');
            if(!$id || !$data = M('node')->where('id='.$id)->find()){
                $this->error('非法请求!');
            }
            $node = M('node')->field('id,pid,controller,action,title')->order('sort')->where(array('pid'=>0))->select();
            $this->assign('node',$node);
            $this->assign('data',$data);
            $this->display();
        }
    }



}