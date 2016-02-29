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

class NeedsAccountViewModel extends ViewModel {
	public $viewFields = array(
		'NeedsAccount'   => array(
            'id',
            'nid',
            'aid',
            'status'
        ),
		'StockAccount' => array('broker', 'account','downurl', 'icon','promoter','remark','created_time', '_on' => 'NeedsAccount.aid=StockAccount.id'),
        'MainNeeds' => array('uid','orders', 'own_funds','with_funds', 'scale','time_limit', 'interest_rate','profit', 'begin_trading','end_trading', 'status'=>'ns','make_bids', 'created_time'=>'nt', '_on' => 'NeedsAccount.nid=MainNeeds.id'),
	);
}
