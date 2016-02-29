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

namespace Common\Api;

class EmailApi {
	/**
	 * 系统邮件发送函数-适用HTML内容
	 * @param string $to    接收邮件者邮箱
	 * @param string $subject 邮件主题
	 * @param string $body    邮件内容
	 * @param string $name  接收邮件者名称
	 * @param string $attachment 附件列表
	 * @return array
	 */
	public static function sendHtmlMail($to, $subject = '', $body = '', $name = '', $attachment = null) {
		vendor('PHPMailer.PHPMailerAutoload');//从PHPMailer目录导class.phpmailer.php类文件
		$mail          = new \PHPMailer();//PHPMailer对象
		$mail->CharSet = 'UTF-8';//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP();// 设定使用SMTP服务
		$mail->SMTPDebug = 0;// 关闭SMTP调试功能
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;// 启用 SMTP 验证功能
		$mail->SMTPSecure = 'ssl';// 使用安全协议
		$mail->Host       = C('MAIL_SMTP_HOST');// SMTP 服务器
		$mail->Port       = C('MAIL_SMTP_PORT');// SMTP服务器的端口号
		$mail->Username   = C('MAIL_SMTP_CE');// SMTP服务器用户名
		$mail->Password   = C('MAIL_SMTP_PASS');// SMTP服务器密码
		$mail->SetFrom(C('MAIL_SMTP_CE'), C('PLATFORMNAME'));
		$replyEmail = C('MAIL_REPLY')?C('MAIL_REPLY'):C('MAIL_SMTP_CE');
		$replyName  = C('MAIL_REPLY_NAME')?C('MAIL_REPLY_NAME'):C('PLATFORMNAME');
		$mail->AddReplyTo($replyEmail, $replyName);
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		$mail->AddAddress($to, $name);
		if (is_array($attachment)) {// 添加附件
			foreach ($attachment as $file) {
				is_file($file) && $mail->AddAttachment($file);
			}
		}
		if (!$mail->Send()) {
			return array('status' => false, 'msg' => $mail->ErrorInfo);
		} else {
			return array('status' => true, 'msg' => '发送成功，请注意查收');
		}
	}
	/**
	 * 系统邮件发送函数-适用文章内容
	 * @param string $to    接收邮件者邮箱
	 * @param string $subject 邮件主题
	 * @param string $body    邮件内容
	 * @param string $name  接收邮件者名称
	 * @return array
	 */
	public static function sendMail($to, $subject = '', $body = '', $name = '') {
		vendor('PHPMailer.PHPMailerAutoload');//从PHPMailer目录导class.phpmailer.php类文件
		$mail = new \PHPMailer();//PHPMailer对象
		$mail->IsSMTP();// 启用SMTP
		$mail->Host     = C('MAIL_SMTP_HOST');//smtp服务器的名称（这里以126邮箱为例）
		$mail->SMTPAuth = true;//启用smtp认证
		$mail->Username = C('MAIL_SMTP_CE');//你的邮箱名
		$mail->Password = C('MAIL_SMTP_PASS');//邮箱密码
		$mail->From     = C('MAIL_SMTP_CE');//发件人地址（也就是你的邮箱地址）
		$mail->FromName = C('MAIL_SMTP_USER');//发件人姓名
		$mail->AddAddress($to, $name);
		$mail->WordWrap = 50;//设置每行字符长度
		$mail->IsHTML(true);// 是否HTML格式邮件
		$mail->CharSet = 'utf-8';//设置邮件编码
		$mail->Subject = $subject;//邮件主题
		$mail->Body    = $body;//邮件内容
		$mail->AltBody = "This is the body in plain text for non-HTML mail clients";//邮件正文不支持HTML的备用显示
		if (!$mail->Send()) {
			return array('status' => false, 'msg' => $mail->ErrorInfo);
		} else {
			return array('status' => true, 'msg' => '发送成功，请注意查收');
		}
	}
}