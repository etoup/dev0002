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
 * 导航控制器
 * @author waco <etoupcom@126.com>
 */

class NavController extends AdminController {

	/**
	 * 导航列表
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		$this->navType = $this->_getNavType();
		$data          = D('Nav')->getNavByType($this->navType, 1);
		$this->list    = $this->_arrayValueSort($data);
		$this->types   = C('NAVTYPES')?C('NAVTYPES'):0;
		$this->display();
	}

	/**
	 * 导航编辑
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			list(
				$id,
				$type,
				$parentid,
				$name,
				$link,
                $tags,
				$image,
				$fontColor,
				$fontBold,
				$fontItalic,
				$fontUnderline,
				$alt, $target,
				$orderid,
				$isshow
			) = array(
				I('post.id', 0, 'int'),
				I('post.type', 0, 'int'),
				I('post.parentid', 0, 'int'),
				I('post.name', ''),
				I('post.link', ''),
                I('post.tags', ''),
				I('post.image', ''),
				I('post.fontColor', ''),
				I('post.fontBold', 0, 'int'),
				I('post.fontItalic', 0, 'int'),
				I('post.fontUnderline', 0, 'int'),
				I('post.alt', ''),
				I('post.target', 0, 'int'),
				I('post.orderid', 0, 'int'),
				I('post.isshow', 0, 'int')
			);
			$id?$map['id']                            = $id:ajaxMsg('请选择操作项',false);
			$name?$map['name']                        = $name:ajaxMsg('请填写导航名称',false);
			$link?$map['link']                        = $link:ajaxMsg('请填写导航链接地址',false);
            $tags?$map['tags']                        = $tags:ajaxMsg('请填写图标标识',false);
			$map['type']                              = $type?$type:0;
			$map['parentid']                          = $parentid?$parentid:0;
			$map['target']                            = $target?$target:0;
			$map['orderid']                           = $orderid?$orderid:0;
			$map['isshow']                            = $isshow?$isshow:0;
			$image && $map['image']                   = $image;
			$alt && $map['alt']                       = $alt;
			$fontColor && $style['fontColor']         = $fontColor;
			$fontBold && $style['fontBold']           = $fontBold;
			$fontItalic && $style['fontItalic']       = $fontItalic;
			$fontUnderline && $style['fontUnderline'] = $fontUnderline;
			$map['style']                             = empty($style)?'':serialize($style);
			$map['rootid']                            = $parentid?$parentid:$id;
			if (D('Nav')->save($map)) {
				ajaxMsg('操作成功');
			} else {
				ajaxMsg('操作失败', false);
			}
		} else {
			$id = I('get.id', 0, 'int');
			$id || $this->error('请选择操作项');
			$type    = I('get.type', 0);
			$navInfo = M('Nav')->find($id);
			if ($navInfo['parentid'] > 0) {
				$parentids = M('Nav')->where(array('type' => $type, 'parentid' => array('eq', 0)))->field('id,name')->select();
			}

			$this->navInfo = $navInfo;
			$style         = $navInfo['style']?unserialize($navInfo['style']):array();
			$this->style   = $style;
			$style['fontColor'] && $css .= "color:".$style['fontColor'].";";

			$style['fontBold'] && $css .= "font-weight:bold;";

			$style['fontItalic'] && $css .= "font-style:italic;";

			$style['fontUnderline'] && $css .= "text-decoration:underline;";

			$this->css       = $css;
			$this->parentids = $parentids;
			$this->display();
		}

	}

	/**
	 * 导航删除
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$id = I('get.id', 0, 'int');
		$id || $this->error('请选择操作项');
		$rid = M('Nav')->delete($id);
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 导航批量修改处理器
	 * @author waco <etoupcom@126.com>
	 */
	public function dorun() {
		if (IS_POST) {
			$homeUrl = '';
			$dms     = $newDms     = $datas     = $newdatas     = array();
			list(
				$posts,
				$newposts,
				$navtype
			) = array(
				I('post.data'),
				I('post.newdata'),
				I('post.navtype')
			);
			$homeid = I('post.home', '');

			foreach ($posts as $post) {
				if (!$post['name'] || !$post['link']) {
					continue;
				}

				if ($navtype == 2) {
					$router = $post['sign'];
				} else {
					$router = $post['link'];
				}
				$map = array(
					'id'      => $post['id'],
					'name'    => $post['name'],
					'link'    => $post['link'],
					'sign'    => $router,
					'orderid' => $post['orderid'],
					'type'    => $navtype,
					'isshow'  => $post['isshow'],
				);
				$resource                             = M('Nav')->save($map);
				$dms[]                                = $dm;
				if ($post['id'] == $homeid) {$homeUrl = $post['link'];
				}
			}

			if ($newposts) {
				foreach ($newposts as $k => $newpost) {
					if (!$newpost['name'] || !$newpost['link']) {
						continue;
					}

					if ($navtype == 2) {
						$router = $newpost['sign'];
					} else {
						$router = $newpost['link'];
					}
					list($isroot, $id) = explode('_', $k);
					if ($isroot == 'root') {
						$parentid = 0;
					} elseif ($isroot == 'child') {
						if (is_numeric($newpost['parentid'])) {
							$parentid = intval($newpost['parentid']);
						} else {
							$parentid = (int) $resource;
						}
					}

					$map = array(
						'name'     => $newpost['name'],
						'link'     => $newpost['link'],
						'sign'     => $router,
						'orderid'  => $newpost['orderid'],
						'type'     => $navtype,
						'parentid' => $parentid,
						'isshow'   => $newpost['isshow'],
					);
					$resource      = M('Nav')->add($map);
					$edt['rootid'] = $parentid?$parentid:$resource;
					M('Nav')->where(array('id' => $resource))->save($edt);
					if ($homeid == 'home_'.$k) {$homeUrl = $newpost['link'];
					}
				}
			}

			ajaxMsg('操作成功');
		} else {
			$this->error('非法操作');
		}

	}

	/**
	 * 导航类型
	 * @author waco <etoupcom@126.com>
	 */
	private function _getNavType() {
		$navType                    = I('get.type', 0, 'int');
		empty($navType) && $navType = 0;
		//p($navType);
		return $navType;
	}

	/**
	 * 对导航信息进行分组和排序
	 * @author waco <etoupcom@126.com>
	 */
	private function _arrayValueSort($array) {
		if (!is_array($array)) {
			return array();
		}

		$_array = array();
		$_key   = 0;
		foreach ($array as $key => $value) {
			if ($value['parentid'] == '0') {
				$_key                  = $key;
				$_array[$_key]         = $value['orderid'];
				$array[$_key]['child'] = array();
			} else {
				$array[$_key]['child'][] = $array[$key];
			}
		}
		asort($_array, SORT_NUMERIC);
		foreach ($_array as $_key => $_value) {
			$_array[$_key] = $array[$_key];
		}
		return $_array;
	}
}
