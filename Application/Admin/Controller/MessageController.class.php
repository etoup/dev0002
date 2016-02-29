<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台消息群发控制器
 * @author waco <etoupcom@126.com>
 */
class MessageController extends AdminController {
	protected $_allowGroupTypes = array('member', 'system', 'special', 'default');
	private $perpage            = 20;
	private $perstep            = 10;
	/**
	 * 消息群发
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			list($type, $content, $title, $step, $countStep) = array(
				I('post.type', 0, 'int'),
				I('post.content', ''),
				I('post.title', ''),
				I('post.step', ''),
				I('post.countStep', '')
			);
			$content || ajaxMsg('请填写内容', false);
			if ($step > $countStep) {
				ajaxMsg('操作成功');
			}
			$step = $step?$step:1;
			switch ($type) {
				case 1:// 根据用户组
					list($user_groups, $grouptype) = array(I('post.user_groups'), I('post.grouptype'));
					empty($user_groups) && ajaxMsg('请选择具体接收用户组', false);
					if ($grouptype == 'memberid') {
						$count = M('Member')->where(array('groupid' => array('in', $user_groups)))->count();
					} else {
						$count = M('Member')->where(array('groupid' => array('in', $user_groups)))->count();
					}
					$countStep = ceil($count/$this->perstep);
					if ($step <= $countStep) {
						list($start, $limit) = page2limit($step, $this->perstep);
						$userInfos           = M('Member')->where(array('groupid' => array('in', $user_groups)))->limit($start, $limit)->select();
					}
					break;
				case 2:// 根据用户名
					$touser = I('post.touser', '');
					!$touser && ajaxMsg('请选择具体接收用户组', false);
					$touser    = explode(' ', $touser);
					$count     = count($touser);
					$countStep = ceil($count/$this->perstep);
					if ($step <= $countStep) {
						list($start, $limit) = page2limit($step, $this->perstep);
						$userInfos           = M('Member')->where(array('nickname' => array('in', $touser)))->limit($start, $limit)->select();
					}
					break;
				case 3:// 根据在线用户(精确在线)
					$onlineUids = M('MemberOnline')->getField('uid', true);
					$count      = count($onlineUids);
					$countStep  = ceil($count/$this->perstep);
					if ($step <= $countStep) {
						list($start, $limit) = page2limit($step, $this->perstep);
						$userInfos           = M('Member')->where(array('uid' => array('in', $onlineUids)))->limit($start, $limit)->select();
					}
					break;

			}

			//消息入库
			if (is_array($userInfos) && !empty($userInfos)) {
				foreach ($userInfos as $key => $value) {
					$data = array(
						'uid'           => $value['uid'],
						'types'        => 0,//系统消息
						'title'         => $title,
						'created_time'  => NOW_TIME,
						'update_time' => NOW_TIME,
						'user_con' => $content
					);
					M('MessageNotices')->add($data);
					//更新消息提示和数量
					$nd = array(
						'uid'          => $value['uid'],
						'notices'      => intval($value['notices']+1)
					);
					M('Member')->save($nd);
				}
			}
			$haveBuild = $step*$this->perstep;
			$haveBuild = ($haveBuild > $count)?$count:$haveBuild;
			$step++;
			usleep(500);
			$data = array('step' => $step,
				'countStep'         => $countStep,
				'count'             => $count,
				'haveBuild'         => $haveBuild,
			);
			$this->ajaxReturn(array('data' => $data));
			exit;
		} else {
			$groups           = M('MemberGroups')->select();
			$groupTypes       = $this->_getGroupTypes();
			$memberGroupTypes = $groupGroupTypes = array();
			foreach ($groups as $key => $group) {
				if ($group['type'] == 'member') {
					$group['grouptype']               = 'memberid';
					$members[$key]                    = $group;
					$memberGroupTypes[$group['type']] = $groupTypes[$group['type']];
				} else {
					$group['grouptype'] = 'groupid';
					$othergroup[$key]   = $group;
					$groupGroupTypes    = array_diff_key($groupTypes, $memberGroupTypes);
				}
			}
			$this->members          = $members;
			$this->othergroup       = $othergroup;
			$this->memberGroupTypes = $memberGroupTypes;
			$this->groupGroupTypes  = $groupGroupTypes;
			$this->display();
		}
	}

	/**
	 * 获取用户组类型
	 *
	 * @author waco <etoupcom@126.com>
	 */
	private function _getGroupTypes() {
		return array_combine($this->_allowGroupTypes, array('会员组', '管理组', '特殊组', '默认组'));
	}
}
