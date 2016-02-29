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
 * 后台附件配置控制器
 * @author waco <etoupcom@126.com>
 */
class AttachmentController extends AdminController {

	/**
	 * 附件配置
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			list($pathsize, $attachnum, $extsize) = array(
				I('post.pathsize', 0, 'int'),
				I('post.attachnum', 0, 'int'),
				I('post.extsize', array())
			);
			$_extsize = array();
			foreach ($extsize as $key => $value) {
				if (!empty($value['ext'])) {$_extsize[$value['ext']] = abs(intval($value['size']));
				}
			}

			$Config = M('Config');
			$Config->where(array('name' => 'PATHSIZE'))->setField('value', abs($pathsize));
			$Config->where(array('name' => 'ATTACHNUM'))->setField('value', abs($attachnum));
			if ($_extsize && is_array($_extsize)) {
				$map   = array('name' => 'EXTSIZE');
				$value = serialize($_extsize);
				$Config->where($map)->setField('value', $value);
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			!($post_max_size = ini_get('post_max_size')) && $post_max_size                   = '2M';
			!($upload_max_filesize = ini_get('upload_max_filesize')) && $upload_max_filesize = '2M';
			$maxSize                                                                         = min($post_max_size, $upload_max_filesize);
			$this->maxSize                                                                   = $maxSize;
			$this->extsize                                                                   = unserialize(C('EXTSIZE'));
			$this->navtabs                                                                   = $this->navtabs();
			$this->display();
		}
	}

	/**
	 * 附件存储
	 * @author waco <etoupcom@126.com>
	 */
	public function storage() {
		if (IS_POST) {
			$storage = I('post.storage', '');
			$storage && M('Config')->where(array('name' => 'STORAGE'))->setField('value', abs($storage));
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->storageType = C('STORAGETYPE');
			$this->storage     = C('STORAGE');
			$this->navtabs     = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 附件缩略
	 * @author waco <etoupcom@126.com>
	 */
	public function thumb() {
		if (IS_POST) {
			list($thumb, $thumbsize_width, $thumbsize_height, $quality) = array(
				I('post.thumb', 0, 'int'),
				I('post.thumbsize_width', 0, 'int'),
				I('post.thumbsize_height', 0, 'int'),
				I('post.quality', 0, 'int')
			);
			$thumb && M('Config')->where(array('name'           => 'THUMB'))->setField('value', abs($thumb));
			$thumbsize_width && M('Config')->where(array('name' => 'THUMBWIDTH'))->setField('value', abs($thumbsize_width));
			if ($thumbsize_height) {
				M('Config')->where(array('name' => 'THUMBHEIGHT'))->setField('value', abs($thumbsize_height));
			} else {

				M('Config')->where(array('name' => 'THUMBHEIGHT'))->setField('value', '');
			}

			$quality && M('Config')->where(array('name' => 'QUALITY'))->setField('value', abs($quality));
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->navtabs = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 缩略图预览
	 * @author waco <etoupcom@126.com>
	 */
	public function view() {
		list($thumb, $thumbsize_width, $thumbsize_height, $quality) = array(
			I('post.thumb', 0, 'int'),
			I('post.thumbsize_width', 0, 'int'),
			I('post.thumbsize_height', 0, 'int'),
			I('post.quality', 0, 'int')
		);
		$image = new \Think\Image();
		$image->open('./Uploads/Attachment/demo.jpg');
		$image->thumb($thumbsize_width, $thumbsize_height, $thumb)->save('./Uploads/Attachment/demo_thumb.jpg');
		$data = array(
			'refresh' => false,
			'state'   => 'success',
			'message' => 'ok',
			'data'    => array('img'    => __ROOT__.'/Uploads/Attachment/demo_thumb.jpg?'.time())
		);
		ajaxMsg($data);
	}

	/**
	 * 导航列表
	 *
	 * @author waco <etoupcom@126.com>
	 */
	private function navtabs() {
		$tabs = array(
			array(
				'title' => '附件设置',
				'tag'   => 'index',
				'url'   => U('index')
			),
			array(
				'title' => '附件存储',
				'tag'   => 'storage',
				'url'   => U('storage')
			),
			array(
				'title' => '附件缩略',
				'tag'   => 'thumb',
				'url'   => U('thumb')
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
