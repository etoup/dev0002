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
 * 后台水印配置控制器
 * @author waco <etoupcom@126.com>
 */
class WatermarkController extends AdminController {

	/**
	 * 水印设置
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			list($markLimitwidth, $markLimitheight, $markPosition, $markType, $markText, $markFontfamily, $markFontsize, $markFontcolor, $markFile, $markTransparency) = array(
				I('post.markLimitwidth', 0, 'int'),
				I('post.markLimitheight', 0, 'int'),
				I('post.markPosition', 0, 'int'),
				I('post.markType', 0, 'int'),
				I('post.markText', ''),
				I('post.markFontfamily', ''),
				I('post.markFontsize', 0, 'int'),
				I('post.markFontcolor', ''),
				I('post.markFile', ''),
				I('post.markTransparency', 0, 'int')
			);
			$Config = M('Config');
			$Config->where(array('name' => 'MARKLIMITWIDTH'))->setField('value', abs($markLimitwidth));
			$Config->where(array('name' => 'MARKLIMITHEIGHT'))->setField('value', abs($markLimitheight));
			$Config->where(array('name' => 'MARKPOSITION'))->setField('value', abs($markPosition));
			$Config->where(array('name' => 'MARKTYPE'))->setField('value', abs($markType));
			$Config->where(array('name' => 'MARKTEXT'))->setField('value', $markText);
			$Config->where(array('name' => 'MARKFONTFAMILY'))->setField('value', $markFontfamily);
			$Config->where(array('name' => 'MARKFONTSIZE'))->setField('value', abs($markFontsize));
			$Config->where(array('name' => 'MARKFONTCOLOR'))->setField('value', $markFontcolor);
			$Config->where(array('name' => 'MARKFILE'))->setField('value', $markFile);
			$Config->where(array('name' => 'MARKTRANSPARENCY'))->setField('value', abs($markTransparency));

			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$pathArr = traverse(LIB_PATH.'Think/Verify/ttfs');
			natcasesort($pathArr);
			$this->pathArr    = $pathArr;
			$this->pathArrImg = traverse('./Uploads/Mark/water');
			$this->navtabs    = $this->navtabs();
			$this->display();
		}
	}

	/**
	 * 水印预览
	 * @author waco <etoupcom@126.com>
	 */
	public function view() {
		list($markLimitwidth, $markLimitheight, $markPosition, $markType, $markText, $markFontfamily, $markFontsize, $markFontcolor, $markFile, $markTransparency) = array(
			I('post.markLimitwidth', 0, 'int'),
			I('post.markLimitheight', 0, 'int'),
			I('post.markPosition', 0, 'int'),
			I('post.markType', 0, 'int'),
			I('post.markText', ''),
			I('post.markFontfamily', ''),
			I('post.markFontsize', 0, 'int'),
			I('post.markFontcolor', ''),
			I('post.markFile', ''),
			I('post.markTransparency', 0, 'int')
		);
		$image = new \Think\Image();
		switch ($markType) {
			case 1:
				$image->open('./Uploads/Mark/demo.jpg');
				$image->crop($markLimitwidth, $markLimitheight)->save('./Uploads/Mark/crop.jpg');
				$image->water('./Uploads/Mark/water/'.$markFile, $markPosition, $markTransparency)->save("./Uploads/Mark/water.gif");
				$image->open('./Uploads/Mark/demo.jpg')->water('./Uploads/Mark/water/'.$markFile, $markPosition, $markTransparency)->save('./Uploads/Mark/water.jpg');
				break;

			case 2:
				$image->open('./Uploads/Mark/demo.jpg')->text($markText, LIB_PATH.'Think/Verify/ttfs/'.$markFontfamily, $markFontsize, $markFontcolor, $markPosition)->save('./Uploads/Mark/water.jpg');
				break;
		}
		$data = array(
			'refresh' => false,
			'state'   => 'success',
			'message' => 'ok',
			'data'    => __ROOT__.'/Uploads/Mark/water.jpg?'.time()
		);
		ajaxMsg($data);
	}

	/**
	 * 水印策略
	 * @author waco <etoupcom@126.com>
	 */
	public function set() {
		if (IS_POST) {
			# code...
		} else {
			$this->navtabs = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 导航列表
	 *
	 * @author waco <etoupcom@126.com>
	 */
	private function navtabs() {
		$tabs = array(
			array(
				'title' => '水印设置',
				'tag'   => 'index',
				'url'   => U('index')
			),
			array(
				'title' => '水印策略',
				'tag'   => 'set',
				'url'   => U('set')
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
