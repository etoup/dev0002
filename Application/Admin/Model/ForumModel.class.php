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
 * 版块模型
 * @author waco <etoupcom@126.com>
 */

class ForumModel extends Model {
	protected $_validate = array(
		array('title', 'require', '版块标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('post_count', 'number', '帖子数量只能为数字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('icon', 'number', '图标ID只能为数字', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
		array('status', 'number', '状态只能为数字', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
		array('sort', 'number', '排序只能为数字', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
		array('parentid', 'number', '上级版块只能为数字', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array(
		array('create_time', 'strtotime', self::MODEL_UPDATE, 'function'),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		array('allow_user_group', 'arr2str', self::MODEL_UPDATE, 'function'),
	);

	/**
	 * 根据状态获得列表
	 *
	 * @param string $status 状态 -1不显示,0禁用,1显示,2显示0,1状态
	 * @return array
	 */
	public function getForumByStatus($status = 1) {
		switch ($status) {
			case 2:
				$where['status'] = array('in', '0,1');
				break;

			default:
				$where['status'] = $status;
				break;
		}

		$data = $this->where($where)->order('rootid,parentid,sort')->select();
		return $data;
	}
}
