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
 * 后台首页控制器
 * @author waco <etoupcom@126.com>
 */

class IndexController extends AdminController {

	/**
	 * 后台首页
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		$this->meta_title = '系统后台';
		$this->display();
	}

}
