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
    }
}