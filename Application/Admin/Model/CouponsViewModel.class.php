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

class CouponsViewModel extends ViewModel {
	public $viewFields = array(
		'Coupons'   => array('*'),
		'UcenterMember' => array('username', 'realname', 'mobile', 'email','_on' => 'Coupons.uid=UcenterMember.id'),
        'Items' => array('name'=>'items_name','_on' => 'Coupons.items_id=Items.id'),
        'Venue' => array('name','_on' => 'Coupons.vid=Venue.id')
	);
}
