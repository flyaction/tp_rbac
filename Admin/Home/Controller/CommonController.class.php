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
        //获取页面栏目导航
        if(CONTROLLER_NAME != 'Index'){
            $pageNav = $this->getPageNav();
            $this->assign('pageNav',$pageNav);
        }

        //记录日志操作
        if(C('ACTION_OPEN_LOG') == 1){
            $this->addLog();
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

    private function getPageNav(){
        $check = array(
            'controller'=>CONTROLLER_NAME,
            'action'=>ACTION_NAME,
            'level'=>2
        );
        $pageNav = array();
        $current = M('node')->field('id,pid,title')->where($check)->find();
        if($current){
            $parent = M('node')->field('id,pid,title')->where('id='.$current['pid'])->find();
            if($parent){
                $pageNav = array(
                    'parent' => $parent['title'],
                    'current' => $current['title']
                );
            }
        }
        return $pageNav;
    }

    private function addLog(){
        $data = array(
            'route'=>CONTROLLER_NAME.'/'.ACTION_NAME,
            'url'=>'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"],
            'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
            'admin_id'=>session(C('USER_AUTH_KEY')),
            'ip'=>get_client_ip(),
            'addtime'=>date('Y-m-d H:i:s'),
        );
        $check = array(
            'controller'=>CONTROLLER_NAME,
            'action'=>ACTION_NAME,
            'level'=>2,
        );
        $node = M('node')->field('id as node_id,title')->where($check)->find();
        if($node){
            $data = array_merge($data,$node);
            return M('admin_log')->add($data);
        }

    }
}