<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Uc\Model;
use Think\Model\ViewModel;

class MainTenderViewModel extends ViewModel {
	public $viewFields = array(
		'MainTender' => array('id', 'uid', 'bid', 'orders', 'money', 'username', 'total_revenue', 'created_time', 'created_ip'),
		'MainBids' => array('nid', 'profit', 'own_funds','with_funds', 'interest_sum', 'time_start', 'time_end', 'deadline', '_on' => 'MainTender.bid=MainBids.id'),
        'MainNeeds' => array('end_trading','time_limit','operate_time','status','types','num','interest_rate','uid'=>'peiziUid',  '_on' => 'MainBids.orders=MainNeeds.orders'),
        'UcenterMember' => array('username', 'realname', 'mobile', '_on' => 'MainTender.uid=UcenterMember.id'),
	);
}
