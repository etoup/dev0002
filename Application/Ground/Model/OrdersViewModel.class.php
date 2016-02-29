<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Ground\Model;
use Think\Model\ViewModel;

class OrdersViewModel extends ViewModel {
	public $viewFields = array(
		'Orders'   => array('*'),
		'Items' => array('name','_on' => 'Orders.items_id=Items.id'),
        'VenueBlock' => array('name'=>'block_name','_on' => 'Orders.bid=VenueBlock.id'),
        'UcenterMember' => array('username', 'realname', 'mobile', 'email','_on' => 'Orders.reserve_id=UcenterMember.id')
	);
}
