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

class MainRepaymentViewModel extends ViewModel {
	public $viewFields = array(
		'MainRepayment'   => array(
            'id',
            'orders',
            'uid',
            'money',
            'remind',
            'time_repayment',
            'time_do_repaymen',
            'created_time',
            'status',
            'types'
        ),
		//'MainNeeds' => array('own_funds','with_funds','scale','time_limit','interest_rate','make_bids', '_on' => 'MainRepayment.orders=MainNeeds.orders'),
        'UcenterMember' => array('username','realname', 'mobile', '_on' => 'MainRepayment.uid=UcenterMember.id'),
	);
}
