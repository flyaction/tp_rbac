<?php
namespace Home\Controller;
use Think\Controller;
class LogController extends CommonController {
    public function index(){
        $db_prefix = C('DB_PREFIX');
        $search = [];
        if(IS_POST){
            $title = I('post.title','','trim');
            $time = I('post.time');
            $search['time'] = $time;

            $date = formatSearchDate($time);
            $startDate = $date['beginDate'].' 00:00:00';
            $endDate = $date['endDate'].' 23:59:59';
            $where = 'log.addtime>="'.$startDate.'" and log.addtime <="'.$endDate.'"';
            if($title){
                $where .= ' and log.title like "%'.$title.'%"';
                $search['title'] = $title;
            }
        }else{
            $searchTime = returnSearchDate(7);
            $startDate = date('Y-m-d H:i:s', $searchTime['beginTime']);
            $endDate = date('Y-m-d H:i:s',$searchTime['endTime']);
            $search['time'] = $searchTime['showDate'];
            $where = 'log.addtime>="'.$startDate.'" and log.addtime <="'.$endDate.'"';
        }
        $log = M('admin_log')->alias('log')->field('log.*,admin.username,admin.realname')->join('left join '.$db_prefix.'admin as admin on admin.id=log.admin_id')->where($where)->select();

        $this->assign('search',$search);
        $this->assign('log',$log);
        $this->display();

    }
}