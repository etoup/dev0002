<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 用户组模型
 * @author waco <etoupcom@126.com>
 */

class MemberGroupsModel extends Model {
	/**
	 * 根据状态获得列表
	 *
	 * @param string $type 类型
	 * @return array
	 */
	public function getAllGroups() {
		$data = array();
		$list = $this->getGroupsByType();

		$types = $this->_getTypes();
		if (is_array($types) && !empty($types)) {
			foreach ($types as $key => $value) {
				$data[$key]['title'] = $value;
				if (is_array($list) && !empty($list)) {
					foreach ($list as $k => $v) {
						if ($v['type'] == $key) {
							$data[$key]['val'][] = $v;
						}
					}
				}
			}
		}
		return $data;
	}

	/**
	 * 根据状态获得列表
	 *
	 * @param string $type 类型
	 * @return array
	 */
	public function getGroupsByType($type) {
		$type && $where['type'] = $type;
		$data                   = $this->where($where)->order('gid')->select();
		return $data;
	}

	private function _getTypes() {
		return array(
			'member'  => '用户组',
			'special' => '特殊组',
			'system'  => '管理组',
			'default' => '默认组',
		);
	}
}
