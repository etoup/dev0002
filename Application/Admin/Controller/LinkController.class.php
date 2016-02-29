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
 * 友情链接控制器
 * @author waco <etoupcom@126.com>
 */
class LinkController extends AdminController {
	/**
	 * 友情链接
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			$vieworder = I('post.vieworder', array());
			if (is_array($vieworder) && !empty($vieworder)) {
				foreach ($vieworder as $key => $value) {
					$data = array('lid'        => $key, 'vieworder'        => $value);
					M('Link')->save($data);
				}
			}
			ajaxMsg('操作成功');
		} else {
			$typeid                  = I('get.typeid', 0, 'int');
			$typeid && $this->typeid = $typeid;
			$this->typesList         = M('LinkType')->order('vieworder')->select();
			$mod                     = D('Link');
			$this->list              = $mod->relation(true)->order('vieworder')->select();
			$this->display();
		}

	}

	/**
	 * 添加友情链接
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function add() {
		if (IS_POST) {
			list($name, $url, $logo, $vieworder, $contact, $typeids,$icon) = array(
				I('post.name', ''),
				I('post.url', ''),
				I('post.logo', ''),
				I('post.vieworder', 0, 'int'),
				I('post.contact', ''),
				I('post.typeids', array()),
                I('post.icon', 0, 'int')
			);
			$name?$data['name']              = $name:ajaxMsg('请填写站点名称', false);
			$url?$data['url']                = $url:ajaxMsg('请填写站点地址', false);
			$logo && $data['logo']           = $logo;
            $icon && $data['pid']            = $icon;
			$vieworder && $data['vieworder'] = $vieworder;
			$contact && $data['contact']     = $contact;
			$rid                             = M('Link')->add($data);
			if (is_array($typeids) && !empty($typeids)) {
				foreach ($typeids as $key            => $value) {
					M('LinkRelations')->add(array('lid' => $rid, 'typeid' => $value));
				}
			}
			ajaxMsg('操作成功');
		} else {
			$this->typesList = M('LinkType')->order('vieworder')->select();
			$this->display();
		}

	}

	/**
	 * 编辑友情链接
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			list($lid, $name, $url, $logo, $vieworder, $contact, $typeids,$icon) = array(
				I('post.lid', 0, 'int'),
				I('post.name', ''),
				I('post.url', ''),
				I('post.logo', ''),
				I('post.vieworder', 0, 'int'),
				I('post.contact', ''),
				I('post.typeids', array()),
                I('post.icon', 0, 'int')
			);
			$lid?$data['lid']                = $lid:ajaxMsg('请选择操作项', false);
			$name?$data['name']              = $name:ajaxMsg('请填写站点名称', false);
			$url?$data['url']                = $url:ajaxMsg('请填写站点地址', false);
			$logo && $data['logo']           = $logo;
            $icon && $data['pid']            = $icon;
			$vieworder && $data['vieworder'] = $vieworder;
			$contact && $data['contact']     = $contact;
			M('Link')->save($data);
			if (is_array($typeids) && !empty($typeids)) {
				M('LinkRelations')->where(array('lid' => $lid))->delete();
				foreach ($typeids as $key             => $value) {
					M('LinkRelations')->add(array('lid'  => $lid, 'typeid'  => $value));
				}
			}
			ajaxMsg('操作成功');
		} else {
			$lid = I('get.lid', 0, 'int');
			$lid || ajaxMsg('请选择操作项', false);
			$this->link      = M('Link')->find($lid);
			$this->types     = M('LinkRelations')->where(array('lid' => $lid))->getField('typeid', true);
			$this->typesList = M('LinkType')->order('vieworder')->select();
			$this->display();
		}

	}

	/**
	 * 删除友情链接
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$lid = I('request.lid');
		if (is_array($lid) && !empty($lid)) {
			$lid = implode(',', $lid);
			M('Link')->delete($lid);
			M('LinkRelations')->where(array('lid' => array('in', $lid)))->delete();
			ajaxMsg('操作成功');
		} else {
			M('Link')->delete($lid);
			M('LinkRelations')->where(array('lid' => $lid))->delete();
			ajaxMsg('操作成功');
		}
	}

	/**
	 * 友情链接分类
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function types() {
		if (IS_POST) {
			$data = I('post.data', array());
			if (is_array($data) && !empty($data)) {
				foreach ($data as $key => $value) {
					M('LinkType')->save($value);
				}
			}
			$newdata = I('post.newdata', array());
			if (is_array($newdata) && !empty($newdata)) {
				foreach ($newdata as $k => $v) {
					M('LinkType')->add($v);
				}
			}
			ajaxMsg('操作成功');
		} else {
			$typesList = M('LinkType')->order('vieworder')->select();
			if (is_array($typesList) && !empty($typesList)) {
				foreach ($typesList as $key => $value) {
					$typesList[$key]['linknum'] = M('LinkRelations')->where(array('typeid' => $value['typeid']))->count('typeid');
				}
			}
			$this->typesList = $typesList;
			$this->display();
		}

	}

	/**
	 * 删除友情链接分类
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function delTypes() {
		$typeid = I('get.typeid', 0, 'int');
		$typeid || ajaxMsg('请选择操作项', false);
		M('LinkType')->delete($typeid);
		M('LinkRelations')->where(array('typeid' => $typeid))->delete();
		ajaxMsg('操作成功');
	}
}