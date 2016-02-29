<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model\RelationModel;

class TagModel extends RelationModel {
	protected $_link = array(
		'TagCategory'           => array(
			'mapping_type'         => self::MANY_TO_MANY,
			'class_name'           => 'TagCategory',
			'mapping_name'         => 'category',
			'foreign_key'          => 'tag_id',
			'relation_foreign_key' => 'category_id',
			'relation_table'       => '__TAG_CATEGORY_RELATION__'//此处应显式定义中间表名称，且不能使用C函数读取表前缀
		)
	);
}
