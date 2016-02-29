<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;

/**
 * 插件模型
 * @author yangweijie <yangweijiester@gmail.com>
 */

class MenuModel extends Model {

	protected $_validate = array(
		array('title', 'require', '标题必须填写'),
		array('url', 'require', '链接必须填写'),
	);

	/* 自动完成规则 */
	protected $_auto = array(
		array('title', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
		array('status', '1', self::MODEL_INSERT),
	);

	/**
	 * 获取层级列表
	 *
	 * @return array
	 */
	public function getMap() {
		if (!isset($_map)) {
			$list = $this->getList();
			foreach ($list as $key => $value) {
				$_map[$value['pid']][] = $value;
			}
		}
		return $_map;
	}

	/**
	 * 获取节点列表
	 *
	 * @return array
	 */
	public function getList() {
		$list = $this->field('id,pid,title,url,tip,hide,group,sort,type,issub,hassub,fup,fupname')->order('sort,id')->select();
		return $list;
	}

	/**
	 * 根据层级列表，递归获取链级列表
	 *
	 * @param int $parentid 获取该版的所属
	 * @param array $map 版块层级列表
	 * @return array
	 */
	public function getNodeByLevel($parentid, $map) {
		if (!isset($map[$parentid])) {return array();
		}

		$length = count($map[$parentid]);
		$array  = array();
		foreach ($map[$parentid] as $key => $value) {
			if ($key == $length-1) {$value['isEnd'] = 1;
			}

			$array[] = $value;
			$array   = array_merge($array, $this->getNodeByLevel($value['id'], $map));
		}
		return $array;
	}
}