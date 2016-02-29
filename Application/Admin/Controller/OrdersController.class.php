<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi;

/**
 * 管理控制器
 * @author waco <etoupcom@126.com>
 */

class OrdersController extends AdminController {

	/**
	 * 管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		list(
			$keyword,
			$items_id,
			$status,
			$time_start,
			$time_end,
			$p,
			$soso
		) = array(
			I('keyword', ''),
			I('items_id', 0,'int'),
			I('status', ''),
			I('time_start', ''),
			I('time_end', ''),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
        $keyword && $map['name']      = array('like', '%'.(string) $keyword.'%');
		$items_id && $map['items_id']     = $items_id;
        if($status){
            $status_info = $this->getStatus($status);
            $map['status']       = $status_info['val'];
        }
		if ($time_start && $time_end) {
			$map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
		}
		if ($time_start && !$time_end) {
			$map['created_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['created_time'] = array('lt', strtotime($time_end));
		}
		$list = $this->lists('OrdersView', $map,'created_time desc',true,1);
		int_to_string($list);
//        p($list,0);
		$this->assign('_list', $list);
		$this->keyword   = $keyword;
		$this->items_id     = $items_id;
		$this->status      = $status;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->p          = $p;
		$this->soso       = $soso?true:false;

        $this->items = M('Items')->where(['status'=>1])->select();
        $this->orders_status = $this->getStatus();
		$this->display();
	}

    private function getStatus($tags=''){

        $status = [
            'pass'=>[
                'title'=>'已过期',
                'val'=>-1
            ],
            'nopay'=>[
                'title'=>'待付款',
                'val'=>0
            ],
            'pay'=>[
                'title'=>'已付款',
                'val'=>1
            ],
            'use'=>[
                'title'=>'已使用',
                'val'=>2
            ],
            'last'=>[
                'title'=>'已结算',
                'val'=>8
            ]
        ];
        if($tags){
            return $status[$tags];
        }
        return $status;
    }
}