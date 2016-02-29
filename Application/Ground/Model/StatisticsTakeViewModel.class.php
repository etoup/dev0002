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

class StatisticsTakeViewModel extends ViewModel {
	public $viewFields = array(
		'StatisticsTake'   => array(
            'id',
            'uid',
            'aid',
            'orders',
            'with_funds',
            'need_username',
            'need_realname',
            'statistics_time',
            'deal_ratio',
            'cose_ratio',
            'interest',
            'money',
            'status',
            'created_time'
        ),
		'MainNeeds' => array('own_funds','with_funds','scale','num','time_limit','interest_rate','make_bids', '_on' => 'StatisticsTake.orders=MainNeeds.orders'),
        'UcenterMember' => array('username','realname', 'mobile', '_on' => 'StatisticsTake.uid=UcenterMember.id'),
        'StockAccount' => array('broker','account', 'promoter', '_on' => 'StatisticsTake.aid=StockAccount.id'),
	);
}
