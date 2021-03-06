<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Index\Model;
use Think\Model;

/**
 * 配资需求模型
 */

class NavModel extends Model {

	//获取头部导航
	public function get_nav($top) {
		$nav = $this->getTree($top);
		return $nav;
	}

	/**
	 * 获取分类详细信息
	 * @param  milit   $id 分类ID或标识
	 * @param  boolean $field 查询字段
	 * @return array     分类信息
	 * @author waco <etoupcom@126.com>
	 */
	public function info($id, $field = true) {
		/* 获取分类信息 */
		$map = array();
		if (is_numeric($id)) {//通过ID查询
			$map['id'] = $id;
		} else {//通过标识查询
			$map['name'] = $id;
		}
		return $this->field($field)->where($map)->find();
	}

	/**
	 * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
	 * @param  integer $id    分类ID
	 * @param  boolean $field 查询字段
	 * @param  boolean $top 是否主导航
	 * @return array          分类树
	 * @author waco <etoupcom@126.com>
	 */
	public function getTree($top = true, $id = 0, $field = true) {
		/* 获取当前分类信息 */
		if ($id) {
			$info = $this->info($id);
			$id   = $info['id'];
		}

		/* 获取所有分类 */
		$map['isshow'] = 1;
		$map['type']   = $top?0:1;
		$list          = $this->field($field)->where($map)->order('orderid')->select();
		$list          = list_to_tree($list, $pk = 'id', $pid = 'parentid', $child = '_', $root = $id);

		/* 获取返回数据 */
		if (isset($info)) {//指定分类则返回当前分类极其子分类
			$info['_'] = $list;
		} else {//否则返回所有分类
			$info = $list;
		}

		return $info;
	}

	/**
	 * 获取指定分类的同级分类
	 * @param  integer $id    分类ID
	 * @param  boolean $field 查询字段
	 * @return array
	 * @author waco <etoupcom@126.com>
	 */
	public function getSameLevel($id, $field = true) {
		$info = $this->info($id, 'parentid');
		$map  = array('parentid' => $info['parentid'], 'isshow' => 1);
		return $this->field($field)->where($map)->order('orderid')->select();
	}
}
