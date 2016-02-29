<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Uc\Model;
use Think\Model\ViewModel;

class ExtensionLogViewModel extends ViewModel {
	public $viewFields = array(
		'ExtensionLog'      => array('id', 'uid', 'eid', 'orders', 'yield', 'money', 'types', 'remark', 'created_time'),
		'Extension' => array('username','reg_time', '_on' => 'ExtensionLog.eid=Extension.id')
	);
}
