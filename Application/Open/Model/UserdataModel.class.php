<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Open\Model;
use Common\Api\UtilApi;
use Think\Model;
/**
 * 用户信息模型
 */

class UserdataModel extends Model {

	protected $_validate = array(
		array('uid', 'require', '请登录后操作', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('realname', 'require', '请填写真实姓名', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('card_number', 'require', '请填写身份证号码', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('card_number', 'check_card_number', '身份证号码格式不正确', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
		array('card_front', 'require', '请上传身份证正面照片', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('card_back', 'require', '请上传身份证反面照片', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
	);

	//检查身份证格式
	public function check_card_number() {
		return UtilApi::idcard_checksum18(I('post.card_number'));
	}

	//检查是否重复申请
	public function hasInfo($uid) {
		$info = $this->find($uid);
		return $info;
	}
}
