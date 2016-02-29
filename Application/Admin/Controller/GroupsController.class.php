<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 用户组控制器
 * @author waco <etoupcom@126.com>
 */
class GroupsController extends AdminController {
	/**
	 * 用户组管理
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			# code...
		} else {
			$this->imageFiles = $this->_getLevel();
			$groupType        = I('get.type', '')?I('get.type'):'member';
			$groups           = M('MemberGroups')->where(array('type' => $groupType))->order('points')->select();
			if ('member' == $groupType) {
				$points = array();
				$last   = '';
				foreach ($groups as $gid => $_item) {
					$points[] = $_item['points'];
					$last     = $_item['points'];
				}
				$points[] = 99999999;
			}
			$this->groups    = $groups;
			$this->points    = $points;
			$this->groupType = $groupType;
			$this->navtabs   = $this->navtabs();
			$this->display();
		}
	}

	/**
	 * 特殊组管理
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function special() {
		if (IS_POST) {
			# code...
		} else {
			$this->imageFiles = $this->_getLevel();
			$groupType        = I('get.type', '')?I('get.type'):'special';
			$groups           = M('MemberGroups')->where(array('type' => $groupType))->select();
			$this->groups     = $groups;
			$this->groupType  = $groupType;
			$this->navtabs    = $this->navtabs();
			$this->display('index');
		}
	}

	/**
	 * 管理组管理
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function system() {
		if (IS_POST) {
			# code...
		} else {
			$this->imageFiles = $this->_getLevel();
			$groupType        = I('get.type', '')?I('get.type'):'system';
			$groups           = M('MemberGroups')->where(array('type' => $groupType))->select();
			$this->groups     = $groups;
			$this->groupType  = $groupType;
			$this->navtabs    = $this->navtabs();
			$this->display('index');
		}
	}

	/**
	 * 默认组管理
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function defaults() {
		if (IS_POST) {
			# code...
		} else {
			$this->imageFiles = $this->_getLevel();
			$groupType        = I('get.type', '')?I('get.type'):'default';
			$groups           = M('MemberGroups')->where(array('type' => $groupType))->select();
			$this->groups     = $groups;
			$this->groupType  = $groupType;
			$this->navtabs    = $this->navtabs();
			$this->display('index');
		}
	}

	/**
	 * 获取图标
	 *
	 * @author waco <etoupcom@126.com>
	 */
	private function _getLevel() {
		$imageFiles = traverse('./Public/Static/image/level');
		natcasesort($imageFiles);
		return $imageFiles;
	}

	/**
	 * 用户组操作
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function doSave() {
		if (IS_POST) {
			list($groupType, $groupName, $groupPoints, $groupImage, $newGroupName, $newGroupPoints, $newGroupImage) = array(
				I('post.grouptype', ''),
				I('post.groupname', array()),
				I('post.grouppoints', array()),
				I('post.groupimage', array()),
				I('post.newgroupname', array()),
				I('post.newgrouppoints', array()),
				I('post.newgroupimage', array())
			);
			if ('member' == $groupType) {
				$_allPointTmp = array_merge($groupPoints, (array) $newGroupPoints);
				if (count($_allPointTmp) != count(array_unique($_allPointTmp))) {
					ajaxMsg('数据重复', false);
				}
			}
			if ($groupName) {
				foreach ($groupName as $key => $value) {
					$data = array(
						'gid'    => $key,
						'name'   => $groupName[$key],
						'type'   => $groupType,
						'image'  => $groupImage[$key],
						'points' => $groupPoints[$key],
					);
					M('MemberGroups')->save($data);
				}
			}
			if ($newGroupName) {
				foreach ($newGroupName as $k => $v) {
					if (!$v) {
						continue;
					}
					$map = array(
						'name'   => $newGroupName[$k],
						'type'   => $groupType,
						'image'  => $newGroupImage[$k],
						'points' => $newGroupPoints[$k]?$newGroupPoints[$k]:0,
					);
					M('MemberGroups')->add($map);
				}
			}
			ajaxMsg('操作成功');
		} else {
			$this->error('非法操作');
		}

	}

	/**
	 * 删除用户组
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$gid = I('get.gid', 0, 'int');
		$gid || ajaxMsg('请选择操作项', false);
		M('MemberGroups')->delete($gid);
		ajaxMsg('操作成功');
	}

	/**
	 * 导航列表
	 *
	 * @author waco <etoupcom@126.com>
	 */
	private function navtabs() {
		$tabs = array(
			array(
				'title' => '用户组',
				'tag'   => 'index',
				'url'   => U('index')
			),
			array(
				'title' => '特殊组',
				'tag'   => 'special',
				'url'   => U('special')
			),
			array(
				'title' => '管理组',
				'tag'   => 'system',
				'url'   => U('system')
			),
			array(
				'title' => '默认组',
				'tag'   => 'defaults',
				'url'   => U('defaults')
			)
		);
		foreach ($tabs as $key => $value) {
			if ($value['tag'] == ACTION_NAME) {
				$tabs[$key]['current'] = 'current';
			}
		}

		return $tabs;
	}
}