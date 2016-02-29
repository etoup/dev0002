<?php
namespace Index\Model;
use Think\Model;

/**
 * 还款日志模型
 */

class MainRepaymentModel extends Model {
    protected $_validate = array(
        array('money', 'require', '缺少金额', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('orders', 'require', '缺少订单数据', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );

	protected $_auto = array(
		array('created_time', NOW_TIME, self::MODEL_INSERT)
	);

    /**
     * 保存数据
     * @param  integer $types 数据类型
     * @param  integer $money 金额
     * @param  string $orders 订单号
     * @param  integer $status 状态
     * @param  integer $paytypes 支付方式
     * @author waco <etoupcom@126.com>
     * @return boolean
     */
	public function addData($types, $money, $orders, $status = 0, $paytypes = 0) {
        $map = [
            'types' => $types,
            'money' => $money,
            'orders'=> $orders
        ];
        switch($types){
            case -1:
            case -2:
                $map['uid_rep'] = UID;
                if($paytypes){
                    //$remind = strtotime(date('Y-m-d',strtotime('-3 days',$vl['repayment_time'])));
                    //意向金
                    $remind = strtotime(date('Y-m-d',NOW_TIME));
                    $time_repayment = strtotime(date('Y-m-d',strtotime('+3 days',NOW_TIME)));
                    $map['remind'] = $remind;
                    $map['time_repayment'] = $time_repayment;
                }else{
                    $map['time_repayment'] = NOW_TIME;
                    $map['time_do_repaymen'] = NOW_TIME;
                }
                $status and $map['status'] = $status;
                break;
        }
        if($this->create($map)){
            $id = $this->add();
            if($id)
                return $orders;
            else
                return false;
        }else{
            return false;
        }
	}
}
