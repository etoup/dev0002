<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Index\Model;
use Think\Model\ViewModel;

class MainBidsViewModel extends ViewModel {
	public $viewFields = array(
		'MainBids'   => array(
            'id',
            'uid',
            'nid',
            'orders',
            'profit',
            'own_funds',
            'with_funds',
            'interest_sum',
            'time_limit',
            'time_start',
            'time_end',
            'created_time',
            'deadline',
            'status',
            'not_active',
            'update_time'
        ),
		'MainNeeds' => array('types', 'num',  '_on' => 'MainBids.nid=MainNeeds.id'),
	);
}
