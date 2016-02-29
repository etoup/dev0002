<?php

namespace Addons\Email;
use Common\Controller\Addon;

/**
 * 邮件管理插件
 * @author Marvin9002
 */

class EmailAddon extends Addon {

	public $info = array(
		'name'        => 'Email',
		'title'       => '邮件管理',
		'description' => '邮件发送插件',
		'status'      => 1,
		'author'      => 'waco',
		'version'     => '0.2',
	);
	//获取表前缀
	public function db_prefix() {
		$db_prefix = C('DB_PREFIX');
		return $db_prefix;
	}
	public function install() {

		$sql1=<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->db_prefix()}mail_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `from` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;
		$sql2=<<<SQLT
CREATE TABLE IF NOT EXISTS `{$this->db_prefix()}mail_history_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) NOT NULL,
  `to` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQLT;

		$sql3=<<<SQLS
CREATE TABLE IF NOT EXISTS `{$this->db_prefix()}mail_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQLS;

		$sql4=<<<SQLF
CREATE TABLE IF NOT EXISTS `{$this->db_prefix()}mail_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `token` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQLF;
		D()->execute($sql1);
		D()->execute($sql2);
		D()->execute($sql3);
		D()->execute($sql4);
		return true;
}

	public function uninstall() {
		$model = D();
		$model->execute("DROP TABLE IF EXISTS {$this->db_prefix()}mail_history;");

		$model->execute("DROP TABLE IF EXISTS {$this->db_prefix()}mail_history_link;");

		$model->execute("DROP TABLE IF EXISTS {$this->db_prefix()}mail_list;");

		$model->execute("DROP TABLE IF EXISTS {$this->db_prefix()}mail_token;");

		return true;
	}

	//实现的pageFooter钩子方法
	public function pageFooter($param) {
		if ($param !== 'send') {
			$this->display('subscribe');
		} else {
			$this->display('ressDocument');
		}
	}

}
