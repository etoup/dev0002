<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace User\Api;
use User\Api\Api;
use User\Model\UcenterMemberModel;

class UserApi extends Api {
	/**
	 * 构造方法，实例化操作模型
	 */
	protected function _init() {
		$this->model = new UcenterMemberModel();
	}

	/**
	 * 注册一个新用户
	 * @param  string $username 用户名
	 * @param  string $password 用户密码
	 * @param  string $email    用户邮箱
	 * @param  string $mobile   用户手机号码
	 * @return integer          注册成功-用户信息，注册失败-错误编号
	 */
	public function register($username, $password, $email = '', $mobile = '') {
		return $this->model->register($username, $password, $email, $mobile);
	}

	/**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function login($username, $password, $type = 1) {
		return $this->model->login($username, $password, $type);
	}

	/**
	 * 获取用户信息
	 * @param  string  $uid         用户ID或用户名
	 * @param  boolean $is_username 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function info($uid, $is_username = false) {
		return $this->model->info($uid, $is_username);
	}

	/**
	 * 获取用户信息
	 * @param  string  $uid         用户ID或用户名
	 * @param  boolean $is_username 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function userInfo($uid, $is_username = false) {
		return $this->model->userInfo($uid, $is_username);
	}

	/**
	 * 检测用户名
	 * @param  string  $field  用户名
	 * @return integer         错误编号
	 */
	public function checkUsername($username) {
		return $this->model->checkField($username, 1);
	}

	/**
	 * 检测密码
	 * @param  string  $pwd  密码
	 * @return integer         错误编号
	 */
	public function checkPwd($pwd) {
		return $this->model->checkField($pwd, 4);
	}

	public function verifyUser($uid, $password,$paypwd) {
		return $this->model->verifyUser($uid, $password,$paypwd);
	}

	public function savePaypwd($uid, $password, $data, $vp = true, $paypwd = true) {
		return $this->model->updateUserFields($uid, $password, $data, $vp, $paypwd = true);
	}

	/**
	 * 检测邮箱
	 * @param  string  $email  邮箱
	 * @return integer         错误编号
	 */
	public function checkEmail($email) {
		return $this->model->checkField($email, 2);
	}

	/**
	 * 检测手机
	 * @param  string  $mobile  手机
	 * @return integer         错误编号
	 */
	public function checkMobile($mobile) {
		return $this->model->checkField($mobile, 3);
	}

	/**
	 * 更新用户信息
	 * @param int $uid 用户id
	 * @param string $password 密码，用来验证
	 * @param array $data 修改的字段数组
	 * @return true 修改成功，false 修改失败
	 * @author huajie <banhuajie@163.com>
	 */
	public function updateInfo($uid, $password, $data, $vp) {
		if ($this->model->updateUserFields($uid, $password, $data, $vp) !== false) {
			$return['status'] = true;
		} else {
			$return['status'] = false;
			$return['info']   = $this->model->getError();
		}
		return $return;
	}

	/**
	 * 更新用户信息
	 */
	public function updateFields($uid, $data, $pass = false) {
		return $this->model->updateFields($uid, $data, $pass);
	}

	/**
	 * 重置密码
	 */
	public function updatePwd($uid, $data) {
		return $this->model->updatePwd($uid, $data);
	}

	/**
	 * 检查用户
	 */
	public function hasUser($where) {
		return $this->model->hasUser($where);
	}

	/**
	 * 设置字段内容
	 */
	public function setFields($uid, $field, $val = 1) {
		return $this->model->setFields($uid, $field, $val);
	}
}
