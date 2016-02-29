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

class MainNeedsdataViewModel extends ViewModel {
	public $viewFields = array(
		'MainNeedsdata'      => array('id','uid', 'nid', 'cid', 'types', 'delay_time', 'balance', 'money', 'status', 'created_time', 'update_time', 'remark'),
		'MainNeeds' => array('orders','own_funds','with_funds','scale','time_limit','interest_rate', 'benchmark', 'profit','deadline','begin_trading','end_trading','types','num','make_bids','created_time'=>'nct','created_ip','update_time','remark','status'=>'ns',  '_on' => 'MainNeedsdata.nid=MainNeeds.id'),
        'UcenterMember' => array('username','realname', 'mobile',  '_on' => 'MainNeedsdata.uid=UcenterMember.id'),
	);
}
