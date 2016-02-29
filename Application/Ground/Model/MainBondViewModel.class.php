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

class MainBondViewModel extends ViewModel {
	public $viewFields = array(
		'MainBond'   => array(
            'id',
            'orders',
            'uid',
            'money',
            'created_time',
            'status',
            'remark',
            'types'
        ),
		'MainNeeds' => array('id'=>'nid','own_funds','with_funds','scale','time_limit','interest_rate','make_bids', '_on' => 'MainBond.orders=MainNeeds.orders'),
        'UcenterMember' => array('username','realname', 'mobile', '_on' => 'MainBond.uid=UcenterMember.id'),
	);
}
