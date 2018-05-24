<?php
namespace Home\Controller;
use Think\Controller;
class LogsController extends CommonController {
    public function index(){

        if(IS_AJAX){
            $data = I('get.');

            //获取Datatables发送的参数 必要
            $draw = $data['draw'];    //这个值直接返回给前台

            $time = $data['time'];
            $title = $data['title'];

            $search = array(
                'time'=>$time,
                'title'=>$title
            );
            $date = formatSearchDate($time);
            $startDate = $date['beginDate'].' 00:00:00';
            $endDate = $date['endDate'].' 23:59:59';
            $where = 'log.addtime>="'.$startDate.'" and log.addtime <="'.$endDate.'"';
            if($title){
                $where .= ' and log.title like "%'.$title.'%"';
            }

            //定义查询数据总记录数sql
            $recordsTotal = M('admin_log')->count();
            //定义过滤条件查询过滤后的记录数sql
            $recordsFiltered = M('admin_log')->alias('log')->where($where)->count();
            //排序条件
            $orderArr = [1=>'id', 7=>'addtime'];
            //获取要排序的字段

            if($orderArr[$data['order']['0']['column']]){
                $orderField =  $orderArr[$data['order']['0']['column']];
                $order = $orderField.' '.$data['order']['0']['dir'];
            }else{
                $order = 'id desc';
            }
            //按条件过滤找出记录
            $result = [];
            //备注:$data['start']起始条数    $data['length']查询长度
            $db_prefix = C('DB_PREFIX');
            $result = M('admin_log')->alias('log')
                ->field('log.*,admin.username,admin.realname')
                ->join('left join '.$db_prefix.'admin as admin on admin.id=log.admin_id')
                ->where($where)
                ->order($order)
                ->limit(intval($data['start']), intval($data['length']))
                ->select();
            //处理数据
//        if(!empty($result)) {
//            foreach ($result as $key => $value) {
//                $result[$key]['recordsFiltered'] = $recordsFiltered;
//            }
//        }
            //拼接要返回的数据
            $list = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($recordsTotal),
                "recordsFiltered"=>intval($recordsFiltered),
                "data" => $result,
                'search'=>$search,
            );

            $this->ajaxReturn($list);
        }else{
            $search = [];
            $searchTime = returnSearchDate(7);
            $startDate = date('Y-m-d H:i:s', $searchTime['beginTime']);
            $endDate = date('Y-m-d H:i:s',$searchTime['endTime']);
            $search['time'] = $searchTime['showDate'];

            $this->assign('search',$search);
            $this->display();
        }


    }

    public function show(){

        echo I('get.id');

    }
}