<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Open\Controller;
use Think\Controller\RestController;
Class BookedController extends RestController {

    Public function index() {
        switch ($this->_method){
            case 'post': // post请求处理代码
                $vid = $_POST['vid'];
                $data = [
                    'status'=>true,
                    'info'=>[
                        'items'=>$this->items($vid)
                    ]
                ];
                $this->response($data,'json');
                break;
        }
    }

    //获取日期列表
    public function items($items_id){
        for($i=1;$i<7;$i++){
            $week = date('N',strtotime('+'.$i.'days'));
            $date = date('y/m/d',strtotime('+'.$i.'days'));
            $d = date('Y-m-d',strtotime('+'.$i.'days'));
            $items[$i] = [
                'week' => $this->getWeek($week),
                'date' => $date,
                'd'    => $d
            ];
        }
        $today = [
            'week' => '今天',
            'date' => date('y/m/d'),
            'd'    => date('Y-m-d')
        ];
        array_unshift($items,$today);
        return count($items)?$items:[];
    }

    //获取场馆场地
    public function blocks(){
        switch ($this->_method){
            case 'get':
                $vid = $_GET['vid'];
                $items_id = $_GET['items_id'];
                $d = $_GET['d'];
                p($this->getBlocks($vid,$items_id,$d));
                break;
            case 'post': // post请求处理代码
                $vid = $_POST['vid'];
                $items_id = $_POST['items_id'];
                $d = $_POST['d'];
                $data = [
                    'status'=>true,
                    'info'=>[
                        'lists'=>$this->getBlocks($vid,$items_id,$d)
                    ]
                ];
                $this->response($data,'json');
                break;
        }
    }

    public function getBlocks($vid,$items_id=0,$d=''){
        if($items_id){
            $map = ['vid'=>$vid,'items_id'=>$items_id,'status'=>1];
        }else{
            $map = ['vid'=>$vid,'status'=>1];
        }
        $venue_items = M('VenueItems')->where($map)->find();
        $orders_date = $d;
        if(!empty($venue_items)){
            if($orders_date){
                $date = $orders_date;
                $week = date('D',strtotime($orders_date));
            }else{
                $date = date('Y-m-d');
                $week = date('D');
            }
            $getweeks = $this->getWeeks();

            //时间
            $venue_items['date_week'] = $date.' '.$getweeks[$week];
            //获取时间节点
            $venue_items['nodes'] = $this->getTimeNode($venue_items['start'],$venue_items['end'],$venue_items['duration']);
            //获取场地信息
            $blocks = M('VenueBlock')->where(['vid'=>$vid,'items_id'=>$venue_items['items_id']])->select();
            $venue_items['blocks'] = $blocks;
            if(!empty($blocks)){
                foreach($blocks as $key=>$val){
                    //获取价格
                    $prices = M('OrdersTemp')->where(['vid'=>$vid,'items_id'=>$venue_items['items_id'],'bid'=>$val['id'],'week'=>$week])->select();
                    if(!empty($prices)){
                        foreach($prices as $kp=>$vp){
                            if($info = M('Orders')->where(['tid'=>$vp['id'],'nodes'=>$date,'status'=>['in',[1,2]]])->find()){
                                $prices[$kp]['has_order'] = true;
                                $prices[$kp]['order_id'] = $info['id'];
                            }
                            if($lockinfo = M('Locks')->where(['tid'=>$vp['id'],'nodes'=>$date,'status'=>1])->find()){
                                $prices[$kp]['status'] = 0;
                            }
                        }
                    }
                    $venue_items['blocks'][$key]['prices'] = $prices;
                }
            }
        }
        $list = $venue_items;
        return $list ? $list : [];
    }

    private function getWeek($num){
        switch($num){
            case 1:
                $week = '周一';
                break;
            case 2:
                $week = '周二';
                break;
            case 3:
                $week = '周三';
                break;
            case 4:
                $week = '周四';
                break;
            case 5:
                $week = '周五';
                break;
            case 6:
                $week = '周六';
                break;
            case 7:
                $week = '周日';
                break;
            default:
                $week = '无效';
        }
        return $week;
    }

    private function getWeeks(){
        return [
            'Mon'=>'星期一',
            'Tue'=>'星期二',
            'Wed'=>'星期三',
            'Thu'=>'星期四',
            'Fri'=>'星期五',
            'Sat'=>'星期六',
            'Sun'=>'星期日'
        ];
    }

    private function getTimeNode($start,$end,$duration){
        $node = ceil((strtotime($end)-strtotime($start))/($duration*60));
        for($i=0;$i<$node;$i++){
            $date_time_start = strtotime('+'.$duration*$i.' minutes',strtotime($start));
            $date_time_end = strtotime('+'.$duration*($i+1).' minutes',strtotime($start));
            $time_node[] = [
                'start'=>date('H:i',$date_time_start),
                'end'=>date('H:i',$date_time_end)
            ];
        }
        return $time_node;
    }
}