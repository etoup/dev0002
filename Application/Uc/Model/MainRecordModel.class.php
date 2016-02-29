<?php
namespace Uc\Model;
use Think\Model;

/**
 * 充值模型
 */

class MainRecordModel extends Model {
	/**
	 * 模型自动完成
	 * @var array
	 */
	protected $_auto = array(
		array('created_time', NOW_TIME, self::MODEL_INSERT)
	);

	public function saveData($operation_amount, $billno, $remark='', $info='', $methods = 0) {
        $operation_amount or $back = false;
        $billno or $back = false;
        switch(C('RATE')){
            case 0:
                $map = [
                    'uid'       => session('user_auth')['uid'],
                    'username'  => session('user_auth')['username'],
                    'operation_amount'=>$operation_amount,
                    'amount' => $operation_amount,
                    'billno'=>$billno,
                    'methods'=>$methods,
                    'remark'=>$remark,
                    'info'=>$info
                ];
                break;
            default:
                if(is_numeric(C('RATE'))){
                    $fees = round($operation_amount * C('RATE'),2);
                    $amount = $operation_amount-$fees;
                    $map = [
                        'uid'       => session('user_auth')['uid'],
                        'username'  => session('user_auth')['username'],
                        'operation_amount'=>$operation_amount,
                        'amount' => $amount,
                        'rate'=>C('RATE'),
                        'fees'=>$fees,
                        'billno'=>$billno,
                        'methods'=>$methods,
                        'remark'=>$remark,
                        'info'=>$info
                    ];
                }
                break;
        }

		$data = $this->create($map);
        $back = $this->add($data);
		return $back;
	}
}
