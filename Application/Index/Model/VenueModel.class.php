<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Index\Model;
use Think\Model\RelationModel;

class VenueModel extends RelationModel {
	protected $_link = array(
		'OrdersTemp'           => array(
			'mapping_type'         => self::HAS_MANY,
			'class_name'           => 'OrdersTemp',
			'mapping_name'         => 'OrdersTemp',
			'foreign_key'          => 'vid'
        )
	);
}
