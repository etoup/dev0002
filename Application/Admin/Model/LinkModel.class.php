<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model\RelationModel;

/**
 * 友情链接模型
 * @author waco <etoupcom@126.com>
 */

class LinkModel extends RelationModel {
	protected $_link = array(
		'LinkType'              => array(
			'mapping_type'         => self::MANY_TO_MANY,
			'class_name'           => 'LinkType',
			'mapping_name'         => 'type',
			'foreign_key'          => 'lid',
			'relation_foreign_key' => 'typeid',
			'relation_table'       => '__LINK_RELATIONS__'//此处应显式定义中间表名称，且不能使用C函数读取表前缀
		)
	);
}