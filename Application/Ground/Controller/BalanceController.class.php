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
 * 结算管理控制器
 * @author waco <etoupcom@126.com>
 */

class BalanceController extends AdminController {

	/**
	 * 结算管理首页
	 * @author waco <etoupcom@126.com>
	 */

    public function index(){
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
        $map['status'] = 8;
        $map['types'] = 0;
        $this->list = $this->lists('OrdersView',$map,'',true,1);
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
        $this->meta_title = '结算管理列表';
        $this->display();
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
