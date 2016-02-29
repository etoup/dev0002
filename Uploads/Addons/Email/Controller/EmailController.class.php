<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com>
// +----------------------------------------------------------------------

namespace Addons\Email\Controller;

use Common\Api\EmailApi;
use Home\Controller\AddonsController;

class EmailController extends AddonsController {

	/**
	 * 发送注册邮件
	 * autor:waco
	 * demo  R('Addons://Email/Email/sendRegister',array($uid));
	 */
	public function sendRegister($uid) {
		$where       = array('id' => $uid);
		$db          = M('ucenterMember');
		$server_host = "http://".$_SERVER['HTTP_HOST'];
		$field       = '';
		$email       = $db->where($where)->field('username,password,email')->select();
		$body        = '尊敬的客户，欢迎您注册成为'.C('WEB_SITE_TITLE').'用户。在您开始浏览或者发布信息时请您遵守如下服务条款：<br>
            不得在'.C('WEB_SITE_TITLE').'上上传和发布下列违法文件及信息：<br>(1) 反对宪法所确定的基本原则的； 
<br>(2) 危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的； <br>(3) 损害国家荣誉和利益的； 
<br>(4) 煽动民族仇恨、民族歧视，破坏民族团结的； <br>(5) 破坏国家宗教政策，宣扬邪教和封建迷信的； <br>(6) 散布谣言，扰乱社会秩序，破坏社会稳定的； 
<br>(7) 散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的； <br>(8) 侮辱或者诽谤他人，侵害他人合法权益的； <br>(9) 含有法律、行政法规禁止的其他内容的。'
		.'<hr>请牢记以下信息：<br>您的用户名是：'.$email[0]['username'].'<br>您的注册邮箱为：'.$email[0]['email'].'<hr><a href='.$server_host.'>'.C('WEB_SITE_TITLE').'</a>';
		//发送邮件
		$res = EmailApi::sendHtmlMail($email[0]['email'], C('WEB_SITE_TITLE').'注册提醒邮件', $body);
	}
	/**
	 * 发送找回密码邮件
	 * autor:waco
	 * demo  R('Addons://Email/Email/sendRegister',array($email, $url));
	 */
	public function renPassword($email, $url) {
		$server_host = "http://".$_SERVER['HTTP_HOST'].$url;
		$token       = $this->create_rand(25);
		$body        = '亲爱的'.$email[0]['email'].'：<br>
欢迎使用'.C('WEB_SITE_TITLE').'找回密码功能。<br>
请您点击此链接来修改您的密码：<a href='.$server_host.'>http:// '.$token.'</a>。<br>
如果您并未发过此请求，则可能是因为其他用户在尝试重设密码时误输入了您的电子邮件地址而使您收到这封邮件，那么您可以放心的忽略此邮件，无需进一步采取任何操作。<br>
此致<br>
'.C('WEB_SITE_TITLE').'敬上<br>
';
		$res = EmailApi::sendHtmlMail($email[0]['email'], C('WEB_SITE_TITLE').'重置密码邮件', $body);

	}
	/**
	 * 文章推送邮件
	 * autor:waco
	 */
	Public function ressDocument() {
		$ids = I('request.ids');
		if (IS_POST) {
			if (empty($ids)) {
				$this->error('请选择要操作的数据');
			}
			foreach ($ids as $val) {
				$this->success('正在跳转...', U('/Addons/execute/_addons/Email/_controller/Email/_action/ressDocument/', array('ids' => $val)));
			}

		} else {
			$id       = I('request.ids');
			$map      = array('status' => 1);
			$mailList = D('MailList')->where($map)->select();
			foreach ($mailList as $k => $v) {
				$address[$v['id']] = $v['address'];
			}
			$data['address'] = implode('; ', $address);

			/* 获取详细信息 */
			$Document = D('Document');
			$info     = $Document->detail($id);
			$this->assign('mailList', $data);
			$this->assign('info', $info);
			$this->display(T('Addons://Email@Email/sendEmail'));
		}
	}

	/**
	 * 邮箱列表
	 * @param string $address
	 * autor:waco
	 */
	public function mailList() {
		$address = I('address');
		$map     = array('status' => 1);
		if ($address != '') {
			$map['address'] = array('like', '%'.$address.'%');
		}

		$mailList = D('MailList')->where($map)->select();

		$this->assign('mailList', $mailList);
		$this->assign('current', 'list');

		$this->display(T('Addons://Email@Email/mailList'));
	}

	public function setStatus() {
		$ids    = I('ids');
		$status = I('get.status');
		$res    = D('MailHistory')->where(array('id' => array('in', $ids)))->setField('status', $status);
		if ($res) {
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}

	}
	/**
	 * 添加邮箱
	 * @param string $address
	 * autor:waco
	 */
	public function addEmail() {
		$address = I('address');
		if (IS_POST) {
			$check = D('MailList')->where(array('address' => $address))->find();
			if ($check) {
				if ($check['status']) {
					$this->error('该邮箱已经存在');
				} else {
					$res = D('MailList')->where(array('address' => $address))->setField('status', 1);
				}
			} else {
				$res = D('MailList')->add(array('address' => $address, 'status' => 1, 'create_time' => time()));
			}
			if ($res) {
				$this->success('添加成功。');
			} else {
				$this->error('添加失败。');
			}
		} else {
			$this->display(T('Addons://Email@Email/addEmail'));
		}
	}

	/**删除邮箱
	 * @param $ids
	 * autor:waco
	 */
	public function delEmail() {
		$ids = I('ids');
		$res = D('MailList')->where(array('id' => array('in', $ids)))->setField('status', 0);
		if ($res) {
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
	/**
	 * 发送邮件页面
	 * @param string $ids
	 * autor:waco
	 */
	public function sendEmail() {
		$ids  = I('ids');
		$list = D('MailList')->where(array('id' => array('in', $ids)))->select();
		foreach ($list as $k                    => $v) {
			$address[$v['id']] = $v['address'];
		}
		$data['address'] = implode('; ', $address);

		$this->assign($data);
		$this->display(T('Addons://Email@Email/sendEmail'));
	}
	/**
	 * 执行发送邮件操作
	 * @param $address 地址列表
	 * @param string $title 邮件标题
	 * @param string $body 邮件正文
	 * autor:waco
	 */
	public function doSendEmail() {
		$address     = I('address');
		$title       = I('title');
		$body        = I('body');
		$server_host = "http://".$_SERVER['HTTP_HOST'];
		if ($title == '' || $body == '') {
			$this->error('请填写完整！');
		}
		//获取邮件配置信息
		$address = explode('; ', $address);

		//将邮件内容写入数据库
		$data                = D('MailHistory')->create();
		$data['create_time'] = time();
		$data['status']      = 1;
		$data['from']        = C('WEB_SITE_TITLE');
		$history             = D('MailHistory')->add($data);
		//匹配图片地址
		preg_match_all('/src="([^http].*?)"/', $body, $out);
		$body = str_replace($out[1][0], $server_host.$out[1][0], $body);
		$res  = false;
		if ($address[0] == '') {
			$address = D('MailList')->where(array('status' => 1))->field('address')->select();
			foreach ($address as $k                        => &$v) {
				$v = $v['address'];
			}
		}
		foreach ($address as $k                                => $v) {
			if ($token_data = D('MailToken')->where(array('email' => $v))->select()) {
				$token = $token_data[0]['token'];
			} else {
				$token               = $this->create_rand(10);
				$data_token['token'] = $token;
				$data_token['email'] = $v;
				D('MailToken')->add($data_token);
			}
			$url = $server_host.addons_url('Email://Email/unsubscribe', array('token' => $token));
			//发送邮件
			$body1 = $body.'<hr/><div style="float:right;margin-right: 20px;"><a href="'.$url.'">取消订阅</a></div>';

			$res = EmailApi::sendHtmlMail($v, $title, $body1);
			//将发送情况和状态写入数据库
			$data_link['status']  = $status;
			$data_link['mail_id'] = $history;
			$data_link['to']      = $v;
			$link                 = D('MailHistoryLink')->add($data_link);
			if ($status) {
				$res = true;
			}
		}
		if ($res) {
			$this->success('邮件发送成功。', addons_url('Email://Email/mailList'));
		}
	}
	/**
	 * 邮件订阅
	 * autor:waco
	 */
	public function subscribe() {
		$email_address = I('email_address');
		$match         = preg_match("/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/", $email_address);

		if ($email_address == '' || !$match) {
			$this->error('邮箱格式不正确');
		}

		$check = D('MailList')->where(array('address' => $email_address))->find();
		if ($check) {
			if ($check['status']) {
				$this->error('该邮箱已经存在');
			} else {
				$res = D('MailList')->where(array('address' => $email_address))->setField('status', 1);
			}

		} else {
			$res = D('MailList')->add(array('address' => $email_address, 'status' => 1, 'create_time' => time()));

		}
		if ($res) {
			$this->success('订阅成功.');
		} else {
			$this->error(' 订阅失败');
		}
	}
	/**
	 * 邮件取消订阅
	 * @param string $title
	 * autor:waco
	 */
	public function unsubscribe() {
		$token = I('token');
		if ($token) {
			$arr = D('MailToken')->where(array('token'  => $token))->find();
			$res = D('MailList')->where(array('address' => $arr['email']))->setField('status', 0);
			D('MailToken')->where(array('token'         => $token))->delete();
			if ($res) {
				$this->success('取消订阅成功', "Home");
			} else {
				$this->error('取消订阅失败', "Home");
			}
		}
	}

	/**
	 * 邮件发送历史
	 * @param string $title
	 * autor:waco
	 */
	public function history() {
		$title = I('title');
		$map   = array('status' => 1);
		if ($title != '') {
			$map['title'] = array('like', '%'.$title.'%');
		}

		$mailList = D('MailHistory')->where($map)->order('create_time desc')->select();
		foreach ($mailList as $k => &$v) {
			$v['title'] = $this->getShortSp($v['title'], 20);
			$v['body']  = $this->getShortSp($v['body'], 50);
		}

		$this->assign('mailList', $mailList);

		$this->display(T('Addons://Email@Email/history'));
	}
	/**
	 * 字符串截取
	 * @param string $title
	 * @param string $lenth
	 * autor:waco
	 */
	public function getShortSp($title, $lenth = 20) {
		$str = substr($title, 0, $lenth);
		if (mb_detect_encoding($str) == 'UTF-8') {//判断内容编码是否为UTF-8
			$str = $str;
		} else {
			$str = iconv('GB2312', 'UTF-8', $str);
		}
		return $str;
	}

	/**
	 * 邮件详情
	 * @param string $id
	 *  autor:waco
	 */
	public function mailDetail() {
		$id      = I('id');
		$history = D('MailHistory')->where(array('id'          => $id))->find();
		$link    = D('MailHistoryLink')->where(array('mail_id' => $id))->select();
		$this->assign('history', $history);
		$this->assign('link', $link);
		$this->display(T('Addons://Email@Email/mailDetail'));
	}

	/**
	 * 随机生成字符串
	 * @param int $length
	 * @return string
	 * autor:waco
	 */
	private function create_rand($length = 8) {
		$chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $chars[mt_rand(0, strlen($chars)-1)];
		}
		return $password;
	}

}
