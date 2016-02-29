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

class TraderChartViewModel extends ViewModel {
	public $viewFields = array(
		'TraderChart'   => array(
            'id',
            'nid',
            'aid',
            'total_assets',
            'pre_value',
            'operators',
            'node',
            'remark',
            'status',
            'created_time'
        ),
		'MainNeeds' => array('orders','own_funds','with_funds','scale','time_limit','interest_rate','make_bids', '_on' => 'TraderChart.nid=MainNeeds.id'),
        'StockAccount' => array('broker','account', 'password', '_on' => 'TraderChart.aid=StockAccount.id'),
        'UcenterMember' => array('username','realname', 'mobile', '_on' => 'TraderChart.uid=UcenterMember.id'),
	);
}
