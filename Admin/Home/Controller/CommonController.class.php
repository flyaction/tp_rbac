<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class CommonController extends Controller {
    Public function _initialize(){
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->redirect(MODULE_NAME.'/Login/index');
        }
        if(C('USER_AUTH_ON')){
        	RBAC::AccessDecision() || $this->error('没有权限');
        }
        if(!session('menuList')){
            $menuList = $this->getMenulist();
            session('menuList',$menuList);
        }

    }

    private function getMenulist(){
        $prefix = C('DB_PREFIX');
        if(session(C('ADMIN_AUTH_KEY'))){
            $node = M('node')->alias('node')->order('sort')->where('node.show=1')->select();
        }else{
            $node = M('access')->alias('access')->field('node.*')->join('left join '.$prefix.'node as node on access.node_id = node.id')->where('access.role_id='.$_SESSION['admin_roleid'].' and node.show=1')->order('node.sort')->select();
        }
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
        return $menu;
    }

}