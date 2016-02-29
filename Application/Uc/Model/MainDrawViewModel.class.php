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

class MainDrawViewModel extends ViewModel {
	public $viewFields = array(
		'MainDraw'      => array('id', 'uid', 'username', 'cid', 'operation_amount', 'amount', 'status', 'types', 'do_types','remark','reason','created_time','created_ip'),
		'MyCard' => array('bank_name', 'card_number_format',  '_on' => 'MainDraw.cid=MyCard.id'),
	);
}
