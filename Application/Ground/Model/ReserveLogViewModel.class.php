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

class ReserveLogViewModel extends ViewModel {
	public $viewFields = array(
		'ReserveLog'   => array('*'),
		'Items' => array('name','_on' => 'ReserveLog.items_id=Items.id'),
        'VenueBlock' => array('name'=>'block_name','_on' => 'ReserveLog.bid=VenueBlock.id')
	);
}
