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

class VenueItemsViewModel extends ViewModel {
	public $viewFields = array(
		'VenueItems'   => array('*'),
		'Items' => array('name', 'icon', '_on' => 'VenueItems.items_id=Items.id'),
	);
}
