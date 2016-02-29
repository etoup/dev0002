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

class VenueBlockViewModel extends ViewModel {
	public $viewFields = array(
		'VenueBlock'   => array('*'),
		'Items' => array('name'=>'items_name', '_on' => 'VenueBlock.items_id=Items.id'),
	);
}
