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
 * 后台行为管理控制器
 * @author waco <etoupcom@126.com>
 */
class BehaviorController extends AdminController {

	/**
	 * 行为管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		$map                                      = array();
		list($keyword, $type, $status, $p, $soso) = array(
			I('keyword', ''),
			I('type', -1),
			I('status', -1),
			I('p', 1),
			I('soso', 0)
		);
		$keyword && $map['title']      = array('like', '%'.$keyword.'%');
		$type > 0 && $map['type']      = $type;
		$status >= 0 && $map['status'] = $status;
		//获取列表数据
		$list = $this->lists('Action', $map);
		int_to_string($list);
		$this->assign('_list', $list);
		$this->page    = I('get.page', '', 'int')?I('get.page', '', 'int'):1;
		$this->keyword = $keyword;
		$this->type    = $type;
		$this->status  = $status;
		$this->p       = $p;
		$this->soso    = $soso?true:false;
		$this->display();
	}

	/**
	 * 添加行为
	 * @author waco <etoupcom@126.com>
	 */
	public function add() {
		if (IS_POST) {
			$res = D('Action')->update();
			if (!$res) {
				ajaxMsg(D('Action')->getError(), false);
			} else {
				ajaxMsg($res['id']?'更新成功':'新增成功');
			}
		} else {
			$this->display();
		}

	}

	/**
	 * 编辑行为
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			$res = D('Action')->update();
			if (!$res) {
				ajaxMsg(D('Action')->getError(), false);
			} else {
				ajaxMsg($res['id']?'更新成功！':'新增成功！');
			}
		} else {
			$id = I('get.id', 0, 'int');
			$id || $this->error('请选择操作项');
			$this->data = M('Action')->field(true)->find($id);
			$this->display();
		}

	}

	/**
	 * 禁用行为
	 * @author waco <etoupcom@126.com>
	 */
	public function disable($value = '') {
		$id = array_unique((array) I('get.id', 0));
		$id = is_array($id)?implode(',', $id):$id;

		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['id'] = array('in', $id);
		$this->forbid('Action', $map);
	}

	/**
	 * 启用行为
	 * @author waco <etoupcom@126.com>
	 */
	public function enable($value = '') {
		$id = array_unique((array) I('get.id', 0));
		$id = is_array($id)?implode(',', $id):$id;

		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['id'] = array('in', $id);
		$this->resume('Action', $map);
	}

	/**
	 * 删除行为
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$id = I('get.id', 0, 'int');
		$id || ajaxMsg('请选择操作项', false);
		$mod            = D('Action');
		$data           = $mod->find($id);
		$data['status'] = -1;
		$mod->create($data);
		if ($mod->save()) {
			ajaxMsg('操作成功');
		} else {
			ajaxMsg($mod->getError(), false);
		}
	}

	/**
	 * 日志管理
	 * @author waco <etoupcom@126.com>
	 */
	public function log() {
		list($keyword, $p, $soso) = array(
			I('keyword', ''),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
		$keyword && $map['remark'] = array('like', '%'.$keyword.'%');
		//获取列表数据
		$list = $this->lists('ActionLog', $map);
		int_to_string($list);
		foreach ($list as $key => $value) {
			$model_id               = get_document_field($value['model'], "name", "id");
			$list[$key]['model_id'] = $model_id?$model_id:0;
		}
		$this->assign('_list', $list);
		$this->keyword = $keyword;
		$this->p       = $p;
		$this->soso    = $soso?true:false;
		$this->display();
	}

	/**
	 * 日志清理
	 * @author waco <etoupcom@126.com>
	 */
	public function clear() {
        IS_ROOT || ajaxMsg('无操作权限', false);
		$timeNode             = strtotime(date('Y-m-d'));
		$where['create_time'] = array('lt', $timeNode);
		if (M('ActionLog')->where($where)->delete()) {
			ajaxMsg('操作成功');
		} else {
			ajaxMsg('操作失败，没有删除任何数据', false);
		}
	}
}
