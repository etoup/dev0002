<?php
namespace Open\Model;
use Think\Model;

/**
 * 资金日志模型
 */

class MainFundsModel extends Model {
	/**
	 * 模型自动完成
	 * @var array
	 */
	protected $_auto = array(
		array('created_time', NOW_TIME, self::MODEL_INSERT),
		array('created_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1)
	);

	public function addData($amount,$uid, $types = 0, $remark = '', $keys =0) {
        $balance = $this->_getBalance($amount,$uid);
		switch ($types) {
			case 1://充值
				$map = array(
					'amount'  => $amount,
					'balance' => $balance,
					'types'   => $types,
					'remark'  => $remark?$remark:'充值操作',
                    'keys'    => $keys,
					'uid'     => $uid
				);
				break;
			case 11://出金
				$map = array(
					'amount'  => $amount,
					'balance' => $balance,
					'types'   => $types,
					'remark'  => $remark?$remark:'账户出金',
                    'keys'    => $keys,
					'uid'     => $uid
				);
				break;
			case 12://投资失败返还
				$map = array(
					'amount'  => $amount,
					'balance' => $balance,
					'types'   => $types,
					'remark'  => $remark?$remark:'投资失败返还投资金额',
                    'keys'    => $keys,
					'uid'     => $uid
				);
				break;
			case 13://投资月收益
				$map = array(
					'amount'  => $amount,
					'balance' => $balance,
					'types'   => $types,
					'remark'  => $remark?$remark:'投资月收益',
                    'keys'    => $keys,
					'uid'     => $uid
				);
				break;
            case 14://投资本金回款
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'投资本金回款',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 15://提现失败返还
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'提现失败返还提现金额',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 16://返还首月利息差额
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'返还首月利息差额',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 17://推广收益
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'推广收益',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 18://借款方提前结束罚金收益
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'借款方提前结束罚金收益',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 19://延期未通过返还服务费
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'延期未通过返还服务费',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;

            case 2://提现
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'提现操作',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 21://首期保证金
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'首期保证金',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 22://补充保证金
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'补充保证金',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 23://支付利息
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'支付利息',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 24://投标成功扣除投标金额
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'投标成功扣除投标金额',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 25://意向金
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'支付意向金',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 26://充值手续费
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'充值手续费',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 27://提现手续费
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'提现手续费',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 28://提前结束罚金
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'提前结束罚金',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 29://逾期罚金
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'逾期罚金',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;
            case 30://延期服务费
                $map = array(
                    'amount'  => $amount,
                    'balance' => $balance,
                    'types'   => $types,
                    'remark'  => $remark?$remark:'延期服务费',
                    'keys'    => $keys,
                    'uid'     => $uid
                );
                break;

			default:
				$map = array(
					'amount'  => $amount,
					'balance' => $balance,
					'types'   => 0,
					'remark'  => $remark?$remark:'非法资金',
                    'keys'    => $keys,
					'uid'     => $uid
				);
				break;
		}

        $data = $this->create($map);
        $back = $this->add($data);
        if($back)
            //调整余额
            M('UcenterMember')->where(['id'=>$uid])->setField('balance',$balance);
        return ;
	}

	private function _getBalance($amount,$uid) {
		$balance = M('UcenterMember')->where(array('id' => $uid))->getField('balance');
		$data    = number_format(($balance+$amount), 2, '.', '');
		return $data;
	}
}
