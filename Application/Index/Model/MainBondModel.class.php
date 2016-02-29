<?php
namespace Index\Model;
use Think\Model;

/**
 * 保证金日志模型
 */

class MainBondModel extends Model {
    protected $_validate = array(
        array('money', 'require', '缺少金额', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('orders', 'require', '缺少订单数据', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );

	protected $_auto = array(
		array('created_time', NOW_TIME, self::MODEL_INSERT)
	);

	public function addData($orders, $money, $status=0, $types=0, $remark = '') {
        $map = [
            'orders'=> $orders,
            'money' => $money,
            'uid'   => UID
        ];
        $status and $map['status'] = $status;
        $types and $map['types'] = $types;
        $remark and $map['remark'] = $remark;

        if($this->create($map))
            $back = $this->add();
        else
            $back = false;
        return $back;
	}
}
