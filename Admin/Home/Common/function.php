<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/23
 * Time: 11:43
 */

function getMenuIcons(){
    return array(
        'list'=>array(
            'fa fa-edit',
            'fa fa-desktop',
            'fa fa-table',
            'fa fa-bar-chart-o',
            'fa fa-clone',
            'fa fa-windows',
            'fa fa-sitemap',
            'fa fa-laptop',
            'fa fa-paper-plane',
        ),
        'default'=>'fa fa-desktop',
    );
}
//格式化页面搜索的时候
function formatSearchDate($date){
    $dateArr = explode(' - ', $date);
    $beginDate = explode('/', $dateArr[0]);
    $beginDate = $beginDate[2] . "-" . $beginDate[0] . "-" . $beginDate[1];
    $endDate = explode('/', $dateArr[1]);
    $endDate = $endDate[2] . "-" . $endDate[0] . "-" . $endDate[1];
    return array("beginDate" => $beginDate, "endDate" => $endDate);
}
//处理页面搜索要展示的时间
function returnSearchDate($days){
    $return = [];
    $return['endTime'] = strtotime(date('Y-m-d'))+3600*24;
    $return['beginTime'] = $return['endTime']-3600*24*$days;
    $return['endTime']--;
    $return['showDate'] = date('m/d/Y',$return['beginTime']).' - '.date('m/d/Y',$return['endTime']);
    return $return;
}


