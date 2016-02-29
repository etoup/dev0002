<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Admin\Model\AuthRuleModel;

/**
 * 权限管理控制器
 * Class AuthManagerController
 * @author waco <etoupcom@126.com>
 */

class AuthManagerController extends AdminController {
	/**
	 * 权限管理首页
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			$map['module']            = 'admin';
			$keyword                  = I('post.keyword')?I('post.keyword'):'';
			$keyword && $map['title'] = array('like', '%'.(string) $keyword.'%');
			$list                     = $this->lists('AuthGroup', $map, 'id asc');
			$list                     = int_to_string($list);
			$this->assign('_list', $list);
			$this->assign('_use_tip', true);
			$this->meta_title = '权限管理';
			$this->soso       = true;
			$this->keyword    = $keyword;
			$this->display();
		} else {
			$list = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
			$list = int_to_string($list);
			$this->assign('_list', $list);
			$this->assign('_use_tip', true);
			$this->soso    = false;
			$this->keyword = '';
			$this->display();

		}
	}

	/**
	 * 创建角色
	 * @author waco <etoupcom@126.com>
	 */
	public function createGroup() {
		if (empty($this->auth_group)) {
			$this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null, ));//排除notice信息
		}
		$this->display();
	}

	/**
	 * 角色数据写入/更新
	 * @author waco <etoupcom@126.com>
	 */
	public function writeGroup() {
		if (isset($_POST['rules'])) {
			sort($_POST['rules']);
			$_POST['rules'] = implode(',', array_unique($_POST['rules']));
		}
		$_POST['module'] = 'admin';
		$_POST['type']   = AuthGroupModel::TYPE_ADMIN;
		$AuthGroup       = D('AuthGroup');
		$data            = $AuthGroup->create();
		if ($data) {
			if (empty($data['id'])) {
				$r = $AuthGroup->add();
			} else {
				$r = $AuthGroup->save();
			}
			if ($r === false) {
				ajaxMsg('操作失败', false);
			} else {
				ajaxMsg('操作成功');
			}
		} else {
			ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 编辑角色
	 * @author waco <etoupcom@126.com>
	 */
	public function editGroup() {
		$auth_group = M('AuthGroup')->where(array('module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
			->find((int) $_GET['id']);
		$this->assign('auth_group', $auth_group);
		$this->display('createGroup');
	}

	/**
	 * 禁用角色
	 * @author waco <etoupcom@126.com>
	 */
	public function disableGroup() {
		$id = I('get.id', 0, 'int');
		$id || $this->error('请选择要操作的数据');
		$map = array(
			'id'     => $id,
			'status' => 0,
		);
		$rid = M('AuthGroup')->save($map);
		if ($rid) {
			$this->success('成功禁用角色');
		} else {

			$this->error('操作失败');
		}
	}

	/**
	 * 启用角色
	 * @author waco <etoupcom@126.com>
	 */
	public function enableGroup() {
		$id = I('get.id', 0, 'int');
		$id || $this->error('请选择要操作的数据');
		$map = array(
			'id'     => $id,
			'status' => 1,
		);
		$rid = M('AuthGroup')->save($map);
		if ($rid) {
			$this->success('成功启用角色');
		} else {

			$this->error('操作失败');
		}
	}

	/**
	 * 删除角色
	 * @author waco <etoupcom@126.com>
	 */
	public function deleteGroup() {
		$id = I('get.id', '', 'int')?I('get.id', '', 'int'):0;
		$id || $this->error('请选择操作项');
		$del = M('AuthGroup')->delete($id);
		if ($del) {
			ajaxMsg('删除成功');
		} else {

			$this->error('删除失败');
		}
	}

	/**
	 * 访问授权页面
	 * @author waco <etoupcom@126.com>
	 */
	public function access() {
		$group_name       = I('get.group_name');
		$this->group_name = $group_name;

		$this->updateRules();
		$auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
			->getfield('id,id,title,rules');
		$node_list   = $this->returnNodes();
		$map         = array('module' => 'admin', 'type' => AuthRuleModel::RULE_MAIN, 'status' => 1);
		$main_rules  = M('AuthRule')->where($map)->getField('name,id');
		$map         = array('module' => 'admin', 'type' => AuthRuleModel::RULE_URL, 'status' => 1);
		$child_rules = M('AuthRule')->where($map)->getField('name,id');

		$this->assign('main_rules', $main_rules);
		$this->assign('auth_rules', $child_rules);
		$this->assign('node_list', $node_list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->display('managerGroup');
	}

	/**
	 * 后台节点配置的url作为规则存入auth_rule
	 * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
	 * @author waco <etoupcom@126.com>
	 */
	public function updateRules() {
		//需要新增的节点必然位于$nodes
		$nodes = $this->returnNodes(false);

		$AuthRule = M('AuthRule');
		$map      = array('module' => 'admin', 'type' => array('in', '1,2'));//status全部取出,以进行更新
		//需要更新和删除的节点必然位于$rules
		$rules = $AuthRule->where($map)->order('name')->select();

		//构建insert数据
		$data = array();//保存需要插入和更新的新节点
		foreach ($nodes as $value) {
			$temp['name']   = $value['url'];
			$temp['title']  = $value['title'];
			$temp['module'] = 'admin';
			if ($value['pid'] > 0) {
				$temp['type'] = AuthRuleModel::RULE_URL;
			} else {
				$temp['type'] = AuthRuleModel::RULE_MAIN;
			}
			$temp['status']                                                = 1;
			$data[strtolower($temp['name'].$temp['module'].$temp['type'])] = $temp;//去除重复项
		}

		$update = array();//保存需要更新的节点
		$ids    = array();//保存需要删除的节点的id
		foreach ($rules as $index => $rule) {
			$key = strtolower($rule['name'].$rule['module'].$rule['type']);
			if (isset($data[$key])) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
				$data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
				$update[]         = $data[$key];
				unset($data[$key]);
				unset($rules[$index]);
				unset($rule['condition']);
				$diff[$rule['id']] = $rule;
			} elseif ($rule['status'] == 1) {
				$ids[] = $rule['id'];
			}
		}
		if (count($update)) {
			foreach ($update as $k => $row) {
				if ($row != $diff[$row['id']]) {
					$AuthRule->where(array('id' => $row['id']))->save($row);
				}
			}
		}
		if (count($ids)) {
			$AuthRule->where(array('id' => array('IN', implode(',', $ids))))->save(array('status' => -1));
			//删除规则是否需要从每个用户组的访问授权表中移除该规则?
		}
		if (count($data)) {
			$AuthRule->addAll(array_values($data));
		}
		if ($AuthRule->getDbError()) {
			trace('['.__METHOD__ .']:'.$AuthRule->getDbError());
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 用户组授权用户列表
	 * @author waco <etoupcom@126.com>
	 */
	public function user($group_id) {
		if (empty($group_id)) {
			$this->error('参数错误');
		}
		$keyword    = I('keyword', '');
		$soso       = I('soso', false);
		$auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
			->getfield('id,id,title,rules');
		$prefix   = C('DB_PREFIX');
		$l_table  = $prefix.(AuthGroupModel::MEMBER);
		$r_table  = $prefix.(AuthGroupModel::AUTH_GROUP_ACCESS);
		$model    = M()->table($l_table.' m')->join($r_table.' a ON m.uid=a.uid');
		$_REQUEST = array();
		$list     = $this->lists($model, array('a.group_id' => $group_id, 'm.status' => array('egt', 0), 'm.nickname' => array('like', '%'.$keyword.'%')), 'm.uid asc', null, 'm.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status');
		int_to_string($list);
		$this->assign('_list', $list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->soso    = $soso;
		$this->keyword = $keyword?$keyword:'';
		$this->display();
	}

	/**
	 * 添加成员
	 * @author waco <etoupcom@126.com>
	 */
	public function addUser() {
		$gid = I('get.gid');
		$gid || $this->error('请选择操作项');
		$this->gid = $gid;
		$this->display();
	}

	/**
	 * 将用户添加到用户组,入参uid,group_id
	 * @author waco <etoupcom@126.com>
	 */
	public function addToGroup() {
		$uid = explode("\n", I('uid'));
		$uid = array_unique($uid);
		$gid = I('gid');
		if (empty($uid)) {
			ajaxMsg('参数有误', false);
		}
		$AuthGroup = D('AuthGroup');
		if (is_numeric($uid)) {
			if (is_administrator($uid)) {
				ajaxMsg('该用户为超级管理员', false);
			}
			if (!M('Member')->where(array('uid' => $uid))->find()) {
				ajaxMsg('管理员用户不存在', false);
			}
		}

		if ($gid && !$AuthGroup->checkGroupId($gid)) {
			ajaxMsg($AuthGroup->error, false);
		}
		if ($AuthGroup->addToGroup($uid, $gid)) {
			ajaxMsg('操作成功');
		} else {
			ajaxMsg($AuthGroup->getError(), false);
		}
	}

	/**
	 * 将用户从用户组中移除  入参:uid,group_id
	 * @author waco <etoupcom@126.com>
	 */
	public function removeFromGroup() {
		$uid = I('get.uid');
		$gid = I('get.group_id');
		if ($uid == UID) {
			ajaxMsg('不允许解除自身授权', false);
		}
		if (empty($uid) || empty($gid)) {
			ajaxMsg('参数有误', false);
		}
		$AuthGroup = D('AuthGroup');
		if (!$AuthGroup->find($gid)) {
			ajaxMsg('用户组不存在', false);
		}
		if ($AuthGroup->removeFromGroup($uid, $gid)) {
			ajaxMsg('操作成功');
		} else {
			ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 将分类添加到用户组  入参:cid,group_id
	 * @author waco <etoupcom@126.com>
	 * 分类控制 -- 扩展
	 */
	public function addToCategory() {
		$cid = I('cid');
		$gid = I('group_id');
		if (empty($gid)) {
			$this->error('参数有误');
		}
		$AuthGroup = D('AuthGroup');
		if (!$AuthGroup->find($gid)) {
			$this->error('用户组不存在');
		}
		if ($cid && !$AuthGroup->checkCategoryId($cid)) {
			$this->error($AuthGroup->error);
		}
		if ($AuthGroup->addToCategory($gid, $cid)) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	/**
	 * 将模型添加到用户组  入参:mid,group_id
	 * @author waco <xcoolcc@gmail.com>
	 * 模型控制 -- 扩展
	 */
	public function addToModel() {
		$mid = I('id');
		$gid = I('get.group_id');
		if (empty($gid)) {
			$this->error('参数有误');
		}
		$AuthGroup = D('AuthGroup');
		if (!$AuthGroup->find($gid)) {
			$this->error('用户组不存在');
		}
		if ($mid && !$AuthGroup->checkModelId($mid)) {
			$this->error($AuthGroup->error);
		}
		if ($AuthGroup->addToModel($gid, $mid)) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	/**
	 * 权限节点管理
	 * @author waco <etoupcom@126.com>
	 */
	public function nodeManage() {
		$map    = D('Menu')->getMap();
		$catedb = $map[0];
		foreach ($catedb as $key => $value) {
			$list[$value['id']] = D('Menu')->getNodeByLevel($value['id'], $map);
		}
		$this->catedb = $catedb;
		$this->list   = $list;
		$this->display();
	}

	/**
	 * 添加节点、修改节点排序、修改节点等操作
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function dorunNode() {
		if (IS_AJAX) {
			//修改节点资料
			list($sort, $url, $group, $tip, $hide) = array(
				I('post.sort'),
				I('post.url'),
				I('post.group'),
				I('post.tip'),
				I('post.hide')
			);
			if (is_array($sort) && !empty($sort)) {
				foreach ($sort as $key => $value) {
					$map = array(
						'id'    => $key,
						'sort'  => $value,
						'url'   => $url[$key],
						'group' => $group[$key],
						'tip'   => $tip[$key],
						'hide'  => $hide[$key]?$hide[$key]:0,
					);
					M('Menu')->save($map);
				}
			}
			//在真实节点下，添加子节点
			list($new_sort, $new_title, $new_url, $new_group, $new_tip, $new_hide, $tempid) = array(
				I('post.new_sort'),
				I('post.new_title'),
				I('post.new_url'),
				I('post.new_group'),
				I('post.new_tip'),
				I('post.new_hide'),
				I('post.tempid')
			);
			$new_level                       = I('post.level')?I('post.level'):array();
			$new_hide                        = empty($new_hide)?array():$new_hide;
			is_array($new_sort) || $new_sort = array();
			foreach ($new_sort as $parentid => $value) {
				foreach ($value as $key        => $v) {
					if ($tempid[$parentid][$key] && $new_title[$parentid][$key] && $new_url[$parentid][$key] && $new_group[$parentid][$key]) {
						$newMap = array(
							'sort'  => $v,
							'pid'   => $parentid,
							'title' => $new_title[$parentid][$key],
							'url'   => $new_url[$parentid][$key],
							'group' => $new_group[$parentid][$key],
							'tip'   => $new_tip[$parentid][$key],
							'type'  => $this->getType($new_level[$parentid][$key])
						);
						$newMap['hide']                     = $new_hide[$parentid][$key]?$new_hide[$parentid][$key]:0;
						$rid                                = M('Menu')->add($newMap);
						$newArray[$tempid[$parentid][$key]] = $rid;
					}
				}
			}
			//在虚拟节点下，添加子节点
			list($temp_sort, $temp_title, $temp_url, $temp_group, $temp_tip, $temp_hide, $level) = array(
				I('post.temp_sort'),
				I('post.temp_title'),
				I('post.temp_url'),
				I('post.temp_group'),
				I('post.temp_tip'),
				I('post.temp_hide'),
				I('post.level')
			);
			is_array($temp_sort) || $temp_sort = array();
			ksort($temp_sort);
			foreach ($temp_sort as $key => $value) {
				if (!isset($newArray[$key])) {continue;
				}

				foreach ($value as $k => $v) {
					if ($tempid[$key][$k] && $temp_title[$key][$k] && $temp_url[$key][$k] && $temp_group[$key][$k]) {
						$tempMap = array(
							'sort'  => $v,
							'pid'   => $newArray[$key],
							'title' => $temp_title[$key][$k],
							'url'   => $temp_url[$key][$k],
							'group' => $temp_group[$key][$k],
							'tip'   => $temp_tip[$key][$k],
							'hide'  => $temp_hide[$key][$k],
							'type'  => $this->getType($level[$key][$k])
						);
						$rid                         = M('Menu')->add($tempMap);
						$newArray[$tempid[$key][$k]] = $rid;
					}
				}
			}
			ajaxMsg('操作成功');
		} else {
			$this->error('非法操作');
		}
	}

	/**
	 * 搜索权限节点
	 * @author waco <etoupcom@126.com>
	 */
	public function searchNode() {
		if (IS_AJAX) {
			$keyword = I('post.keyword', '');
			if ($keyword) {
				$where = array(
					'title' => array('like', '%'.$keyword.'%')
				);
				$list = M('Menu')->where($where)->select();
				if (!$list || !is_array($list)) {
					ajaxMsg('没有符合条件的内容', false);
				} else {
					$this->data = $list;
					$map        = array(
						'refresh' => false,
						'state'   => 'success',
						'data'    => $list,
					);
					echo json_encode($map);
				}
			}
		} else {
			ajaxMsg('非法操作', false);
		}
	}

	/**
	 * 编辑权限节点
	 * @author waco <etoupcom@126.com>
	 */
	public function editNode() {
		if (IS_POST) {
			$Menu = D('Menu');
			$data = $Menu->create();
			if (empty($data)) {
				ajaxMsg($Menu->getError(), false);
			} else {
				$Menu->save();
				ajaxMsg('操作成功');
			}
		} else {
			$id = I('get.id', 0, 'int');
			$id || $this->error('请选择操作项');
			$this->info = M('Menu')->find($id);
			$this->display();
		}
	}

	/**
	 * 编辑权限节点名称
	 * @author waco <etoupcom@126.com>
	 */
	public function editTitle() {
		if (IS_AJAX) {
			$id = I('post.id', 0, 'int');
			$id || ajaxMsg('请选择操作项', false);
			$title = I('post.title', '');
			$title || ajaxMsg('请填写节点名称', false);
			$map = array('id' => $id, 'title' => $title);
			M('Menu')->save($map);
			ajaxMsg('操作成功');
		} else {
			ajaxMsg('非法操作', false);
		}
	}

	/**
	 * 删除权限节点
	 * @author waco <etoupcom@126.com>
	 */
	public function delNode() {
		$id = I('get.id', 0, 'int');
		$id || ajaxMsg('请选择操作项', false);
		$info = M('Menu')->where(array('pid' => $id))->find();
		empty($info) || ajaxMsg('请先删除子节点后再操作', false);
		if (M('Menu')->delete($id)) {
			// S('DB_CONFIG_DATA',null);
			//记录行为
			action_log('update_menu', 'Menu', $id, UID);
			ajaxMsg('删除成功');
		} else {
			ajaxMsg('删除失败', false);
		}
	}

	/**
	 * 获取节点类型
	 * @author waco <etoupcom@126.com>
	 */
	public function getType($level) {
		switch ($level) {
			case 2:
				$type = 'forum';
				break;
			case 3:
				$type = 'sub';
				break;
			default:
				$type = 'category';
				break;
		}
		return $type;
	}

	/**
	 * 后台管理员管理
	 * @author waco <etoupcom@126.com>
	 */
	public function manage() {
		$config  = $this->_getConfig();
		$admini  = $config['USER_MANAGER']?explode(',', $config['USER_MANAGER']):array();
		$manages = array_merge((array) C('USER_ADMINISTRATOR'), $admini);
		if (is_array($manages)) {
			$where = array(
				'uid' => array('in', $manages)
			);
			$list = M('Member')->field('uid,nickname')->where($where)->select();
		}
		$this->list = $list?$list:array();
		$this->display();
	}

	/**
	 * 添加后台管理员
	 * @author waco <etoupcom@126.com>
	 */
	public function addManage() {
		$nickname = I('post.nickname', '');
		$nickname || $this->error('请填写用户名');
		$uid = M('Member')->where(array('nickname' => $nickname))->getField('uid');
		if ($uid) {
			$mod  = D('Config');
			$data = array(
				'name'   => 'USER_MANAGER',
				'title'  => '后台管理员',
				'remark' => '后台管理员列表',
				'type'   => 1,
				'group'  => 1,
			);
			$config = $this->_getConfig();
			if (isset($config['USER_MANAGER'])) {
				$id         = $mod->where(array('name' => 'USER_MANAGER'))->getField('id');
				$data['id'] = $id;
				$admini     = $config['USER_MANAGER']?explode(',', $config['USER_MANAGER']):array();
				in_array($uid, $admini) && $this->error('已经存在该管理员');
				array_push($admini, $uid);
				$data['value'] = implode(',', $admini);
				$mod->create($data);
				if ($mod->save()) {
					S('DB_CONFIG_DATA', null);
					//记录行为
					action_log('update_config', 'config', $data['id'], UID);
					$this->success('操作成功');
				} else {

					$this->error('操作失败');
				}
			} else {
				$data['value'] = $uid;
				$mod->create($data);
				if ($mod->add()) {
					S('DB_CONFIG_DATA', null);
					$this->success('操作成功');
				} else {

					$this->error('操作失败');
				}
			}
		} else {
			$this->error('没有该用户');
		}
	}

	/**
	 * 取消后台管理员
	 * @author waco <etoupcom@126.com>
	 */
	public function delManage() {
		$uid = I('get.uid', 0, 'int');
		$uid || ajaxMsg('请选择操作项', false);
		$config = $this->_getConfig();
		$admini = $config['USER_MANAGER']?explode(',', $config['USER_MANAGER']):array();
		in_array($uid, $admini) || ajaxMsg('无法删除该管理员', false);
		$user_admin = C('USER_ADMINISTRATOR');
		if (count(C('USER_ADMINISTRATOR')) == 1 && empty($admini)) {
			ajaxMsg('删除失败', false);
		}

		if (count($admini) == 1 && empty($user_admin)) {
			ajaxMsg('删除失败', false);
		}

		foreach ($admini as $key => $value) {
			if ($uid == $value) {
				unset($admini[$key]);
			}
		}

		$mod  = D('Config');
		$data = array(
			'name'   => 'USER_MANAGER',
			'title'  => '后台管理员',
			'remark' => '后台管理员列表',
			'type'   => 1,
			'group'  => 1,
		);
		$id            = $mod->where(array('name' => 'USER_MANAGER'))->getField('id');
		$data['id']    = $id;
		$data['value'] = implode(',', $admini);
		$mod->create($data);
		if ($mod->save()) {
			S('DB_CONFIG_DATA', null);
			//记录行为
			action_log('update_config', 'config', $data['id'], UID);
			ajaxMsg('操作成功');
		} else {

			ajaxMsg($mod->getError(), false);
		}
	}

	/**
	 * 获取配置项
	 * @author waco <etoupcom@126.com>
	 */
	private function _getConfig() {
		S('DB_CONFIG_DATA', null);
		$config = S('DB_CONFIG_DATA');
		if (!$config) {
			$config = api('Config/lists');
			S('DB_CONFIG_DATA', $config);
		}
		return $config;
	}
}
