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

class HomeController extends AdminController {

	/**
	 * 后台首页
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (UID) {
			$this->display();
		} else {
			$this->redirect('Public/login');
		}
	}

	/**
	 * 常用菜单
	 * @author waco <etoupcom@126.com>
	 */
	public function custom() {
		if (IS_POST) {
			$customs = I('post.customs', array());
			if (is_array($customs) && !empty($customs)) {
				//清除
				M('Custom')->where(array('uid' => UID))->delete();
				foreach ($customs as $value) {
					//url
					$info = M('Menu')->find($value);
					$data = array(
						'uid' => UID,
						'url' => $info['url'],
						'mid' => $value,
					);
					M('Custom')->add($data);
				}
				ajaxMsg('操作成功,重新登录后生效');
			} else {
				//清除
				M('Custom')->where(array('uid' => UID))->delete();
				ajaxMsg('操作成功,重新登录后生效');
			}
		} else {
			$this->node_list = $this->returnNodes();
			$myMenu          = M('Custom')->where(array('uid' => UID))->getField('mid', true);
			$this->myMenu    = json_encode($myMenu);
			$this->display();
		}

	}
}
