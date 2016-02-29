<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 客户经理模型
 * @author waco <etoupcom@126.com>
 */

class CustomServiceModel extends Model {
	protected $_validate = array(
		array('uid', 'require', '客户信息为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cid', 'require', '指定客户经理', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array(
		array('operate_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		array('operator', USERNAME, self::MODEL_BOTH),
        array('mender', USERNAME, self::MODEL_BOTH),
	);
}
