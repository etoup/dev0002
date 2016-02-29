<?php
namespace Admin\Model;
use Think\Model;

/**
 * 股票账户模型
 */

class StockAccountModel extends Model {
    protected $_validate = array(
        array('broker', 'require', '券商必须填写'),
        array('account', 'require', '账户必须填写'),
        array('promoter', 'require', '出资人必须填写'),
        array('password', 'require', '密码必须填写',self::EXISTS_VALIDATE,'regex',self::MODEL_INSERT),
        array('downurl', 'require', '下载地址必须填写')
    );

	protected $_auto = array(
		array('password', 'think_encrypt', self::MODEL_UPDATE,'function'),
        array('created_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE)
	);
}
