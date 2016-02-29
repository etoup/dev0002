<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Open\Model;
use Think\Model\ViewModel;

class MainBidsViewModel extends ViewModel {
	public $viewFields = array(
		'MainBids'      => array('id', 'uid', 'nid', 'profit', 'own_funds', 'with_funds', 'time_limit', 'time_end', 'time_start','created_time','status','orders'),
		'MainNeeds' => array('types', 'num', '_on' => 'MainBids.nid=MainNeeds.id'),
	);
}
