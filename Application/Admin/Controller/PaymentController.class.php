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
 * 支付控制器
 * 主要用于支付配置
 */
class PaymentController extends AdminController {

	//线上支付
	public function index() {
		if (IS_POST) {
			$iv = explode(',', C('PAYINTERFACEVAL'));
			if (is_array($iv) && !empty($iv)) {
				$Config = M('Config');
				foreach ($iv as $key => $value) {
					$map = array('name' => $value);
					$val = serialize(I('post.'.$value, array()));
					$Config->where($map)->setField('value', $val);
				}
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$iv = explode(',', C('PAYINTERFACEVAL'));
			if (is_array($iv) && !empty($iv)) {
				foreach ($iv as $key => $value) {
					if (strstr(C('PAYINTERFACEVAL'), $value)) {
						$data[$value] = C($value.'_LIST');
					}
				}
			}

            if(!empty($data)){
                foreach($data as $ka=>$va){
                    $vals = unserialize(C("$ka"));

                    foreach($va as $kb=>$vb){
                        $fordata[$ka][$kb]['title'] = $vb;
                        $fordata[$ka][$kb]['val'] = $vals[$kb];
                    }
                }
            }

			$this->data      = $fordata;

			$this->display();
		}

	}

	//选中支付接口
	public function open() {
		if (IS_POST) {
			$interface = $data = array();
			$interface = C('PAYINTERFACE');
			if (is_array($interface) && !empty($interface)) {
				foreach ($interface as $key => $value) {
					if (I('post.'.$key)) {
						array_push($data, $key);
					}
				}
			}
			$data   = implode(',', $data);
			$Config = M('Config');
			$map    = array('name' => 'PAYINTERFACEVAL');
			$Config->where($map)->setField('value', $data);
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->interface = C('PAYINTERFACE');
			$this->display();
		}
	}

	//线下转账
	public function offline() {
		if (IS_POST) {
			$Config = M('Config');
			$map    = array('name' => 'OFFLINE');
			$data   = serialize(I('post.offline', array()));
			$Config->where($map)->setField('value', $data);
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->list = unserialize(C('OFFLINE'));
			$this->display();
		}

	}
}
