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

class OrdersViewModel extends ViewModel {
	public $viewFields = array(
		'Orders'   => array('*'),
		'UcenterMember' => array('username', 'realname', 'mobile', 'email','_on' => 'Orders.uid=UcenterMember.id'),
        'Items' => array('name'=>'items_name','_on' => 'Orders.items_id=Items.id'),
        'Venue' => array('name','_on' => 'Orders.vid=Venue.id'),
        'VenueBlock' => array('name'=>'block_name','_on' => 'Orders.bid=VenueBlock.id')
	);
}
