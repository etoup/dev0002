<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Ground\Controller;

/**
 * 订单管理控制器
 * @author waco <etoupcom@126.com>
 */

class OrdersController extends AdminController {

	/**
	 * 场馆管理首页
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
        $venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
        $this->venue_items = $venue_items;
        $orders_date = I('get.d','');
        if(!empty($venue_items)){
            if($orders_date){
                $date = $orders_date;
                $week = date('D',strtotime($orders_date));
            }else{
                $date = date('Y-m-d');
                $week = date('D');
            }
            $getweeks = $this->getWeeks();
            foreach($venue_items as $k => $v){
                //时间
                $venue_items[$k]['date_week'] = $date.' '.$getweeks[$week];
                //获取时间节点
                $venue_items[$k]['nodes'] = $this->getTimeNode($v['start'],$v['end'],$v['duration']);
                //获取场地信息
                $blocks = M('VenueBlock')->where(['vid'=>VID,'items_id'=>$v['items_id']])->select();
                $venue_items[$k]['blocks'] = $blocks;
                if(!empty($blocks)){
                    foreach($blocks as $key=>$val){
                        //获取价格
                        $prices = M('OrdersTemp')->where(['vid'=>VID,'items_id'=>$v['items_id'],'bid'=>$val['id'],'week'=>$week])->select();
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
                        $venue_items[$k]['blocks'][$key]['prices'] = $prices;
                    }
                }
            }
        }
        $this->list = $venue_items;
        $this->pre_date = date('Y-m-d', strtotime('-1 day',strtotime($date)));
        $this->next_date = date('Y-m-d', strtotime('+1 day',strtotime($date)));
        $this->nodes = $orders_date;
        //p($venue_items);
        $this->active = 'Orders';
		$this->meta_title = '订单管理';
		$this->display();
	}

    public function orders(){
        list(
            $keyword,
            $items,
            $block,
            $typestxt,
            $statustxt,
            $ordersdate,
            $p,
            $soso
            ) = array(
            I('keyword', ''),
            I('items', 0,'int'),
            I('block', 0,'int'),
            I('typestxt', ''),
            I('statustxt', ''),
            I('ordersdate', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $keyword and $map['num'] = ['like', '%'.(string) $keyword.'%'];
        $items && $map['items_id']           = intval($items);
        $block && $map['bid']       = intval($block);
        if($typestxt){
            switch($typestxt){
                case 'up':
                    $map['types']       = 0;
                    break;
                case 'down':
                    $map['types']       = 1;
                    break;
            }
        }
        if($statustxt){
            switch($statustxt){
                case 'nopay':
                    $map['status']       = 0;
                    break;
                case 'pay':
                    $map['status']       = 1;
                    break;
                case 'use':
                    $map['status']       = 2;
                    break;
                case 'suc':
                    $map['status']       = 8;
                    break;
            }
        }
        if($ordersdate){
            $dateArr = explode(' - ',$ordersdate);
            if(trim($dateArr[0]) == trim($dateArr[1]))
                $map['nodes'] = $dateArr[0];
            else
                $map['created_time'] = ['between', [strtotime($dateArr[0]),strtotime($dateArr[1])]];
        }
        $map['vid'] = VID;
        $this->list = $this->lists('OrdersView',$map,'id',true,1);
        $this->typestxtArr = $this->typestxt();
        $this->statustxtArr = $this->statustxt();
        //p($this->list);
        $this->keyword   = $keyword;
        $this->items        = $items;
        $this->block        = $block;
        $this->typestxt      = $typestxt;
        $this->statustxt = $statustxt;
        $this->ordersdate   = $ordersdate;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->active = 'Orders';
        $this->meta_title = '订单管理列表';
        $this->display();
    }

    public function ordersview(){
        $id = I('get.id',0,'int');
        $id or $this->error('请选择操作项');
        $this->info = D('OrdersView')->find($id);
        //p($this->info,0);
        $this->display();
    }

    public function reserves(){
        $id = I('post.id',[]);
        $id or $data_id = ['status'=>false,'msg'=>'请选择操作项'];
        if($data_id){
            $this->ajaxReturn($data_id);
        }
        $mobile = I('post.mobile','');
        $mobile or $data_mobile = ['status'=>false,'msg'=>'请填写预定人手机号码'];
        if(!check_mobile($mobile)){
            $data_mobile = ['status'=>false,'msg'=>'请正确填写预定人手机号码'];
        }
        if($data_mobile){
            $this->ajaxReturn($data_mobile);
        }
        $bid = M('OrdersTemp')->where(['id'=>['in',$id],'vid'=>VID])->select();
        if($bid){
            $date = I('post.nodes','')?I('post.nodes',''):date('Y-m-d');
            foreach($bid as $k=>$v){
                $mod = D('Orders');
                $date_time = $date.' '.$v['end'];
                $end_time = strtotime($date_time);
                $mod->addData([
                    'uid'=>UID,
                    'items_id'=>$v['items_id'],
                    'vid'=>$v['vid'],
                    'bid'=>$v['bid'],
                    'tid'=>$v['id'],
                    'nodes'=>$date,
                    'start'=>$v['start'],
                    'end'=>$v['end'],
                    'price'=>$v['price'],
                    'status'=>1,
                    'types'=>1,
                    'mobile'=>$mobile,
                    'end_time'=>$end_time
                ]);
            }
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
    }

    public function getItems(){
        $list = D('VenueItemsView')->where(['vid'=>VID])->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $date[] = [
                    'name'=>$v['name'],
                    'value'=>$v['items_id']
                ];
            }
        }
        $this->ajaxReturn($date);
    }
    public function getBlocks(){
        $items = I('get.items',0,'int');
        $list = M('VenueBlock')->where(['vid'=>VID,'items_id'=>$items])->select();
        $data['state'] = 'success';
        $data['data'] = [];
        if(!empty($list)){
            foreach($list as $k=>$v){
                $d[] = [
                    'name'=>$v['name'],
                    'value'=>$v['id']
                ];
            }
            $data['data'] = $d;
        }
        $this->ajaxReturn($data);
    }

    public function checkTimes(){
        $id = I('post.id',0,'int');
        $id or $this->ajaxReturn(['msg'=>'请选择操作项']);
        $info = M('Orders')->find($id);
        switch($info['status']){
            case 1:
                $items = M('Items')->find($info['items_id']);
                $times = strtotime("+".$items['hours']." hours");
                if($info['end_time'] < $times){
                    $this->ajaxReturn(['msg'=>'该订单使用时间将近或已过期，不符合撤单要求']);
                }
                break;
            default:
                $this->ajaxReturn(['msg'=>'状态不对，无法进行撤单操作']);
        }
    }

    public function ordersdel(){
        $id = I('post.id',0,'int');
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        $info = M('Orders')->find($id);
        $items = M('Items')->find($info['items_id']);
        $times = strtotime("+".$items['hours']." hours");
        if($info['end_time'] < $times){
            $this->ajaxReturn(['status'=>false,'msg'=>'该订单使用时间将近或已过期，不符合撤单要求']);
        }
        $bid = M('Orders')->save(['id'=>$id,'status'=>-1,'update_time'=>NOW_TIME]);
        if($bid){
            D('MainFunds')->saveData($info['price'],11,$info['reserve_id']);
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
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

    private function typestxt(){
        $data = [
            'up'=>[
                'title'=>'线上预定',
                'value'=>0
            ],
            'down'=>[
                'title'=>'现场预定',
                'value'=>1
            ]
        ];

        return $data;
    }

    private function statustxt(){
        return [
            'nopay'=>[
                'title'=>'未支付',
                'value'=>0
            ],
            'pay'=>[
                'title'=>'已支付',
                'value'=>1
            ],
            'use'=>[
                'title'=>'已使用',
                'value'=>2
            ],
            'suc'=>[
                'title'=>'已结算',
                'value'=>8
            ]
        ];
    }
}
