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
 * 锁定管理控制器
 * @author waco <etoupcom@126.com>
 */

class LocksController extends AdminController {

    public function index(){
        list(
            $items,
            $block,
            $ordersdate,
            $p,
            $soso
            ) = array(
            I('items', 0,'int'),
            I('block', 0,'int'),
            I('ordersdate', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $items && $map['items_id']           = intval($items);
        $block && $map['bid']       = intval($block);
        if($ordersdate){
            $dateArr = explode(' - ',$ordersdate);
            if(trim($dateArr[0]) == trim($dateArr[1]))
                $map['nodes'] = $dateArr[0];
            else
                $map['created_time'] = ['between', [strtotime($dateArr[0]),strtotime($dateArr[1])]];
        }
        $map['vid'] = VID;
        $this->list = $this->lists('LocksView',$map,'id',true,1);
        $this->items        = $items;
        $this->block        = $block;
        $this->ordersdate   = $ordersdate;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->active = 'Locks';
        $this->meta_title = '锁定管理列表';
        $this->display();
    }

    public function seclists(){
        $lid = I('get.lid',0,'int');
        $map['log_id'] = $lid;
        $this->list = $this->lists('LocksView',$map,'id',true,1);
        $this->meta_title = '锁定管理列表';
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


}
