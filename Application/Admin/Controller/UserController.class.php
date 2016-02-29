<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Model\AuthGroupModel;
use User\Api\UserApi;

/**
 * 后台用户控制器
 * @author waco <etoupcom@126.com>
 */

class UserController extends AdminController {

	/**
	 * 用户管理首页
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		$map['status'] = array('egt', 0);
		list(
			$gid,
			$username,
			$uid,
			$email,
			$time_start,
			$time_end,
			$p,
			$soso
		) = array(
			I('gid', 0, 'int'),
			I('username', ''),
			I('uid', ''),
			I('email', ''),
			I('time_start', ''),
			I('time_end', ''),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
        if($username){
            $where['username'] = array('like', '%'.(string) $username.'%');
            $where['realname'] = array('like', '%'.(string) $username.'%');
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
		//$username && $map['nickname'] = array('like', '%'.(string) $username.'%');
		$uid && $map['uid']           = intval($uid);
		$gid && $map['groupid']       = intval($gid);
		$email && $map['email']       = array('like', '%'.(string) $email.'%');
		if ($time_start && $time_end) {
			$map['reg_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
		}
		if ($time_start && !$time_end) {
			$map['reg_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['reg_time'] = array('lt', strtotime($time_end));
		}
		$list = $this->lists('MemberView', $map,'',true,1);
		int_to_string($list);
		$this->assign('_list', $list);
		$this->groups     = M('MemberGroups')->where(array('type' => array('neq', 'member')))->select();
		$this->username   = $username;
		$this->uid        = $uid;
		$this->gid        = $gid;
		$this->email      = $email;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->p          = $p;
		$this->soso       = $soso?true:false;
		$this->display();
	}

	/**
	 * 会员添加
	 * @author waco <etoupcom@126.com>
	 */
	public function add($username = '', $password = '', $repassword = '', $email = '', $gid = 0) {
		if (IS_POST) {
			/* 检测密码 */
			if ($password != $repassword) {
				ajaxMsg('密码和重复密码不一致', false);
			}
			/* 调用注册接口注册用户 */
			$User = new UserApi;
			$uid  = $User->register($username, $password, $email);
			if (0 < $uid) {//注册成功
				$user = array('uid' => $uid, 'nickname' => $username, 'status' => 1, 'gid' => $gid);
				M('Member')->add($user);
				M('MemberBelong')->add(array('uid' => $uid, 'gid' => $gid));
				ajaxMsg('用户添加成功');

			} else {//注册失败，显示错误信息
				ajaxMsg($this->showRegError($uid), false);
			}
		} else {
			$this->groups = M('MemberGroups')->where(array('type' => array('in', 'special,system')))->select();
			$this->display();
		}
	}

	/**
	 * 编辑会员
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			list($uid, $password, $repassword,$qq,$mobile,$rate,$maxscale) = array(I('post.uid'), I('post.password'), I('post.repassword'), I('post.qq'), I('post.mobile'), I('post.rate'), I('post.maxscale'));
			$uid || ajaxMsg('请选择操作项！', false);
			if ($password) {
				$password != $repassword && ajaxMsg('新密码和确认密码不一致！', false);
				$data['password'] = $password;
                $Api = new UserApi();
                $res = $Api->updateInfo($uid, $password, $data, false);
			}
            if($rate){
                if($rate>2){
                    ajaxMsg('操作失败，原因：利息点位不大于2.00',false);
                }
                $ucdata['rate'] = $rate;
                if($maxscale>10){
                    ajaxMsg('操作失败，原因：最大配资比例不大于10',false);
                }
                $ucdata['maxscale'] = $maxscale;
                $Api = new UserApi();
                $Api->updateFields($uid,$ucdata);
            }
            $map['qq'] = $qq;
            $map['mobile'] = $mobile;
            M('Member')->where(array('uid'=>$uid))->save($map);

			ajaxMsg('操作成功！');

		} else {
			$uid = I('get.uid', 0, 'int');
			$uid || $this->error('请选择操作项！');
			$avatar       = M('avatar')->where(array('uid' => $uid))->find();
			$Model        = D("MemberView");
			$info         = $Model->find($uid);
			$info['path'] = $avatar['path']?$avatar['path']:'./Uploads/Avatar/avatar_middle.jpg';
            $info['card_number'] = M('Userdata')->where(['uid'=>$uid])->getField('card_number');
			$this->info   = $info;
			$this->display();
		}

	}

	/**
	 * 编辑会员积分
	 * @author waco <etoupcom@126.com>
	 */
	public function editScore() {
		if (IS_POST) {
			list($id, $score) = array(I('post.id', 0, 'int'), I('post.score', 0, 'int'));
			$id or ajaxMsg('请选择操作项', false);
			M('UcenterMember')->save(array('id' => $id, 'score' => $score));
			ajaxMsg('操作成功');
		} else {
			$uid = I('get.uid', 0, 'int');
			$uid or $this->error('请选择操作项！');
			$this->info = M('UcenterMember')->field('id,score,username')->find($uid);
			$this->display();
		}

	}

    /**
     * 编辑会员余额
     * @author waco <etoupcom@126.com>
     */
    public function editBalance() {
        if (IS_POST) {
            list($id, $money, $remark) = array(I('post.id', 0, 'int'), I('post.money'), I('post.remark'));
            $id or ajaxMsg('请选择操作项', false);
            $money or ajaxMsg('请填写操作金额', false);
            $remark or ajaxMsg('请填写备注信息', false);
            D('MainFunds')->saveData($money,0,$id,$remark);
            //站内信
            $title = '管理员直接对您余额进行了操作';
            $user_con = time_format().'管理员直接对您余额进行了操作，金额：'.$money.'；<a href="index.php/Uc/Funds/index">立即查看</a>';
            D('MessageNotices')->saveData($id,$title,$user_con);
            ajaxMsg('操作成功');
        } else {
            $uid = I('get.uid', 0, 'int');
            $uid or $this->error('请选择操作项！');
            $this->info = M('UcenterMember')->field('id,balance,username')->find($uid);
            $this->display();
        }

    }

	/**
	 * 编辑会员组
	 * @author waco <etoupcom@126.com>
	 */
	public function editGroup() {
		if (IS_POST) {
			$uid = I('get.uid', 0, 'int');
			$uid || ajaxMsg('请选择操作项', false);
			$info                             = M('Member')->field('uid,score,nickname,groupid,memberid')->find($uid);
			list($groupid, $groups, $endtime) = array(I('post.groupid', 0, 'int'), I('post.groups'), I('post.endtime'));
			$banGids                          = M('MemberGroups')->where(array('type' => 'default'))->getField('gid', true);
			$clearGids                        = array();
			//如果用户原先的用户组是在默认组中，则该用户组不允许被修改
			if (in_array($info['groupid'], $banGids) && $info['groupid'] != $groupid) {
				switch ($info['groupid']) {
					case 6:
						ajaxMsg('取消禁言用户请到用户禁言中执行解禁操作', false);
						break;
					case 7:
						ajaxMsg('取消未验证用户请到用户审核中执行通过操作', false);
						break;
					default:
						ajaxMsg('默认组中的用户组不能被设置或取消', false);
						break;
				}
			}
			//如果用户原先的用户组是不在默认组中，新设置的用户组在默认组中，则抛错
			if (!in_array($info['groupid'], $banGids) && in_array($groupid, $banGids) && $info['groupid'] != $groupid) {
				switch ($groupid) {
					case 6:
						ajaxMsg('设为禁言用户请到用户禁言中执行禁言操作', false);
						break;
					case 7:
						ajaxMsg("'未验证会员'不允许被设为当前用户组", false);
						break;
					default:
						ajaxMsg('默认组中的用户组不能被设置或取消', false);
						break;
				}
			}
			if (($if = in_array($groupid, $banGids)) || ($r = array_intersect($banGids, $groups))) {
				ajaxMsg('默认组中的用户组不能被设置或取消', false);
			} else {
				foreach ($groups as $value) {
					$clearGids[$value] = (isset($endtime[$value]) && $endtime[$value])?strtotime($endtime[$value]):0;
				}
				if ($groupid == 0) {
					list($groupid, $clearGids) = $this->_caculateUserGroupid($groupid, $clearGids);
				} elseif (!isset($clearGids[$groupid])) {
					$clearGids[$groupid] = 0;
				}
			}
			//$oldGid = explode(',', $info['MemberGroups']);
			//$info['groupid'] && array_push($oldGid, $info['groupid']);
			M('Member')->save(array('uid'        => $uid, 'groupid'        => $groupid));
            //处理冗余
            $user = new UserApi();
            $user->updateFields($uid,array('groups'=>$groupid));
			M('MemberBelong')->where(array('uid' => $uid))->delete();
			if (is_array($clearGids) && !empty($clearGids)) {
				foreach ($clearGids as $k           => $v) {
					M('MemberBelong')->add(array('uid' => $uid, 'gid' => $k, 'endtime' => $v));
				}
			}
			ajaxMsg('操作成功');
		} else {
			$uid = I('get.uid', 0, 'int');
			$uid || $this->error('请选择操作项！');
			$this->info   = M('Member')->field('uid,score,nickname,groupid,memberid')->find($uid);
			$systemGroups = $this->_getClassifiedGroups();
			$groups       = $allGroups       = array();
			foreach (array('system', 'special', 'default') as $k) {
				foreach ($systemGroups[$k] as $gid => $_item) {
					if (in_array($_item['gid'], array(1, 2))) {continue;
					}

					$groups[$gid] = $_item;
				}
			}
			foreach (array('system', 'special') as $k) {
				foreach ($systemGroups[$k] as $gid => $_item) {
					$allGroups[$gid] = $_item;
				}
			}
			$this->defaultGroups = M('MemberGroups')->where(array('type' => 'default'))->getField('gid', true);
			$ug                  = M('MemberBelong')->where(array('uid'  => $uid))->select();
			if (is_array($ug) && !empty($ug)) {
				foreach ($ug as $key => $value) {
					$userGroups[$value['gid']] = $value;
					$myGids[]                  = $value['gid'];
				}
			}
			$this->userGroups = $userGroups;
			$this->myGids     = $myGids;
			$this->allGroups  = $allGroups;
			$this->groups     = $groups;
			$this->display();
		}

	}

	/**
	 * 会员禁用
	 * @author waco <etoupcom@126.com>
	 */
	public function disable() {
		$id = I('request.uid');
		if (in_array(C('USER_ADMINISTRATOR'), $id)) {
			ajaxMsg("不允许对超级管理员执行该操作!", false);
		}

		if (empty($id)) {
			ajaxMsg('请选择要操作的数据!', false);
		}
		$map['uid'] = array('in', $id);
		if (is_array($id)) {
			$rid = M('Member')->where($map)->save(array('status' => 0));
			if ($rid) {
				ajaxMsg('禁用成功！');
			} else {

				ajaxMsg('禁用失败！', false);
			}
		} else {
			$this->forbid('Member', $map);
		}
	}

	/**
	 * 会员启用
	 * @author waco <etoupcom@126.com>
	 */
	public function enable() {
		$id = I('request.uid');
		if (in_array(C('USER_ADMINISTRATOR'), $id)) {
			ajaxMsg("不允许对超级管理员执行该操作!", false);
		}

		if (empty($id)) {
			ajaxMsg('请选择要操作的数据!', false);
		}
		$map['uid'] = array('in', $id);
		if (is_array($id)) {
			$rid = M('Member')->where($map)->save(array('status' => 1));
			if ($rid) {
				ajaxMsg('启用成功！');
			} else {

				ajaxMsg('启用失败！', false);
			}
		} else {
			$this->resume('Member', $map);
		}
	}

	/**
	 * 会员删除
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$id = I('request.uid');
		if (in_array(C('USER_ADMINISTRATOR'), $id)) {
			ajaxMsg("不允许对超级管理员执行该操作!", false);
		}

		if (empty($id)) {
			ajaxMsg('请选择要操作的数据!', false);
		}
		$map['uid'] = array('in', $id);

		$rid = M('Member')->where($map)->save(array('status' => -1));
		if ($rid) {
			ajaxMsg('删除成功！');
		} else {

			ajaxMsg('删除失败！', false);
		}
	}

	/**
	 * 会员授权
	 * @author waco <etoupcom@126.com>
	 */
	public function group() {
		if (IS_POST) {
			$uid = I('post.uid', '', 'int');
			$uid || ajaxMsg('请选择要操作的数据！', false);
			$gid       = I('post.userRoles');
			$ids       = I('post.ids');
			$AuthGroup = D('AuthGroup');
			if (is_numeric($uid)) {
				if (is_administrator($uid)) {
					ajaxMsg('该用户为超级管理员！', false);
				}
				if (!M('Member')->where(array('uid' => $uid))->find()) {
					ajaxMsg('管理员用户不存在！', false);
				}
			}
			if (empty($gid)) {
				AuthGroupModel::removeByUid($uid);
				ajaxMsg('成功清空该用户的所有权限！');
			} else {
				if ($gid && !$AuthGroup->checkGroupId($gid)) {
					ajaxMsg($AuthGroup->error, false);
				}
				if ($AuthGroup->saveToGroup($uid, $gid)) {
					ajaxMsg('操作成功！');
				} else {
					ajaxMsg($AuthGroup->getError(), false);
				}
			}
		} else {
			$uid            = I('get.uid');
			$this->uid      = $uid;
			$username       = I('get.username');
			$this->username = $username;
			$list           = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
			$list           = int_to_string($list);
			$this->assign('list', $list);
			$userGroups       = AuthGroupModel::getUserGroup($uid);
			$this->userGroups = $userGroups;
			$this->display();
		}
	}

	/**
	 * 根据用户拥有组信息获得该用户的当前使用的用户身份
	 * <pre>
	 * 用户拥有的组的使用顺序：
	 * <ol>
	 * <li>系统组 》荣誉组 》会员组</li>
	 * <li>然后按组内的id顺序进行变更。</li>
	 * </ol>
	 * </pre>
	 *
	 * @param int $groupid 用户当前用户组
	 * @param array $userGroups 用户拥有的组
	 * @return array (gid, groups)
	 */
	private function _caculateUserGroupid($groupid, $userGroups) {
		$banGids = array(1, 2, 6, 7);
		// 如果是游客/禁止发言/未验证用户组则不变动该用户组
		if (in_array($groupid, $banGids)) {return array($groupid, array());
		}

		//【当前用户组】 当前用户组设置顺序:系统组/特殊组/普通组
		$groupQue = array('system', 'special');
		$temp     = array();
		$time     = time();
		foreach ($userGroups as $_gid => $_time) {
			if ($_time == 0 || $_time > $time) {
				$temp[$_gid] = $_time;
			}
		}
		$gid = 0;
		if ($temp) {
			$groups   = $this->_getClassifiedGroups();
			$userGids = array_keys($temp);
			foreach ($groupQue as $_tmpType) {
				/*如果用户拥有系统组或是特殊组，根据用户组ID从小到大排序的第一个用户组为用户当前显示的组*/
				$_tmp = array_intersect(array_keys($groups[$_tmpType]), $userGids);
				if ($_tmp) {
					ksort($_tmp);
					$gid = array_shift($_tmp);
					break;
				}
			}
		}
		//当前用户组过期
		if (!$temp[$groupid]) {
			$groupid = $gid;
		}
		return array($groupid, $temp);
	}

	/**
	 * 将包含有特殊组和管理组
	 * @author waco <etoupcom@126.com>
	 */
	private function _getClassifiedGroups() {
		$groups            = M('MemberGroups')->select();
		$groups || $groups = array();
		$data              = array();
		foreach ($groups as $key => $v) {
			$data[$v['type']][$key] = $v;
		}
		return $data;
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0) {
		switch ($code) {
			case -1:$error = '用户名长度必须在16个字符以内！';
				break;

			case -2:$error = '用户名被禁止注册！';
				break;

			case -3:$error = '用户名被占用！';
				break;

			case -4:$error = '密码长度必须在6-30个字符之间！';
				break;

			case -5:$error = '邮箱格式不正确！';
				break;

			case -6:$error = '邮箱长度必须在1-32个字符之间！';
				break;

			case -7:$error = '邮箱被禁止注册！';
				break;

			case -8:$error = '邮箱被占用！';
				break;

			case -9:$error = '手机格式不正确！';
				break;

			case -10:$error = '手机被禁止注册！';
				break;

			case -11:$error = '手机号被占用！';
				break;

			case -12:$error = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
				break;

			default:$error = '未知错误';
		}
		return $error;
	}
}