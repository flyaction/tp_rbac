<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }

    public function loginout(){
        session_unset();
        session_destroy();
        $this->redirect(MODULE_NAME.'/Login/index');
    }


}