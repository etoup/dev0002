<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model\ViewModel;

class MainNeedsViewModel extends ViewModel {
	public $viewFields = array(
		'MainNeeds'   => array(
            'id',
            'uid',
            'orders',
            'own_funds',
            'with_funds',
            'scale',
            'time_limit',
            'interest_rate',
            'end_trading',
            'pay_type',
            'types',
            'num',
            'status',
            'created_time'
        ),
		'UcenterMember' => array('username', 'realname', 'mobile','custom_service', '_on' => 'MainNeeds.uid=UcenterMember.id'),
	);
}
