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

class NeedsAccountViewModel extends ViewModel {
	public $viewFields = array(
		'NeedsAccount'      => array('id', 'nid', 'aid', 'status', 'remark'),
		'StockAccount' => array('broker', 'account', 'password','downurl','icon', '_on' => 'NeedsAccount.aid=StockAccount.id'),
	);
}
