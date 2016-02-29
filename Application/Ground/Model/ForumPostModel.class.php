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
 * 帖子模型
 * @author waco <etoupcom@126.com>
 */

class ForumPostModel extends Model {

	/**
	 * 根据状态获得列表
	 *
	 * @param string $status 状态 -1不显示,0禁用,1显示,2显示0,1状态
	 * @return array
	 */
	public function getPostByStatus($status = 1) {
		switch ($status) {
			case 2:
				$where['status'] = array('in', '0,1');
				break;

			default:
				$where['status'] = $status;
				break;
		}

		$data = $this->where($where)->order('create_time')->select();
		return $data;
	}
}