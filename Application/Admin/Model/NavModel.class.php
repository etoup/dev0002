<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 导航模型
 * @author waco <etoupcom@126.com>
 */

class NavModel extends Model {
	protected $_validate = array(
		array('name', 'require', '导航名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('link', 'require', '链接地址不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		array('isshow', '1', self::MODEL_BOTH),
	);

	/**
	 * 根据导航类型获得列表
	 *
	 * @param string $type 导航类型
	 * @param int  	 $isshow 0不显示,1显示,2全部
	 * @return array
	 */
	public function getNavByType($type = 0, $isShow = 1) {
		$where['type'] = $type;
		if ($isShow < 2) {
			$where['isshow'] = $isShow;
		}

		$data = $this->where($where)->order('rootid,parentid,orderid')->select();
		return $data;
	}
}
