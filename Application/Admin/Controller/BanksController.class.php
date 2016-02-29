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
 * 银行控制器
 * 主要用于管理银行列表
 */
class BanksController extends AdminController {

	//银行列表
	public function index() {
		$data = array();
		list(
			$keyword,
			$p,
			$soso
		) = array(
			I('keyword', ''),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
		$keyword && $data['name'] = array('like', '%'.$keyword.'%');
		$list = $this->lists('Banks', $data, 'id,sort');
		if (is_array($list) && !empty($list)) {
			foreach ($list as $key => $val) {
				if ($val['icon']) {
					$list[$key]['path'] = M('Picture')->where(array('id' => $val['icon']))->getField('path');
				}
			}
		}
		$this->list    = $list;
		$this->p       = $p;
		$this->soso    = $soso?true:false;
		$this->keyword = $keyword?$keyword:'';
		$this->display();
	}

	//添加银行
	public function add() {
		if (IS_POST) {
			list(
				$name,
				$code,
				$sort,
				$icon
			) = array(
				I('post.name', ''),
				I('post.code', 0, 'int'),
				I('post.sort', 0, 'int'),
				I('post.icon', 0, 'int')
			);
			$name || ajaxMsg('请填写银行名称', false);
			$code || ajaxMsg('请填写银行代号', false);
			$data = array(
				'name' => $name,
				'code' => $code,
				'sort' => $sort,
				'icon' => $icon,
			);
			if (M('Banks')->add($data)) {
				ajaxMsg('操作成功');
			} else {
				ajaxMsg('操作失败', false);
			}
		} else {
			$this->display();
		}

	}

	//编辑银行信息
	public function edit() {
		if (IS_POST) {
			list(
				$id,
				$name,
				$code,
				$sort,
				$icon
			) = array(
				I('post.id', 0, 'int'),
				I('post.name', ''),
				I('post.code', 0, 'int'),
				I('post.sort', 0, 'int'),
				I('post.icon', 0, 'int')
			);
			$id || ajaxMsg('请选择操作项', false);
			$name || ajaxMsg('请填写银行名称', false);
			$code || ajaxMsg('请填写银行代号', false);
			$data = array(
				'id'   => $id,
				'name' => $name,
				'code' => $code,
				'sort' => $sort,
				'icon' => $icon,
			);
			if (M('Banks')->save($data)) {
				ajaxMsg('操作成功');
			} else {
				ajaxMsg('操作失败', false);
			}
		} else {
			$id = I('get.id', 0, 'int');
			$id || $this->error('请选择操作项');
			$this->info = M('Banks')->find($id);
			$this->display();
		}

	}

	//删除银行信息
	public function del() {
		$id = I('request.id');
		if (is_array($id)) {
			$back = M('Banks')->where(array('id' => array('in', $id)))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('Banks')->delete($id);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	//启用
	public function enable() {
		$id = I('post.id', array());
		empty($id) && ajaxMsg('请选择操作项', false);
		$back = M('Banks')->where(array('id' => array('in', $id)))->save(array('status' => 0));
		$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	//禁用
	public function disable() {
		$id = I('post.id', array());
		empty($id) && ajaxMsg('请选择操作项', false);
		$back = M('Banks')->where(array('id' => array('in', $id)))->save(array('status' => 1));
		$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}
}
