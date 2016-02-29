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

class MainTenderViewModel extends ViewModel {
	public $viewFields = array(
		'MainTender'   => array(
            'id',
            'uid',
            'bid',
            'orders',
            'money',
            'username',
            'total_revenue',
            'created_time'=>'time',
            'created_ip'
        ),
		'MainBids' => array('*', '_on' => 'MainTender.bid=MainBids.id'),
        'UcenterMember' => array('realname', 'mobile', '_on' => 'MainTender.uid=UcenterMember.id'),
	);
}
