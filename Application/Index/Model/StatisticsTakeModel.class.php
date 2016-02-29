<?php
namespace Index\Model;
use Think\Model;

/**
 * 业务提成模型
 */

class StatisticsTakeModel extends Model {
    protected $_validate = array(
        array('orders', 'require', '缺少订单数据', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );

	protected $_auto = array(
		array('created_time', NOW_TIME, self::MODEL_INSERT)
	);

    /**
     * 保存数据
     * @param string $orders 订单号
     * @param array $data 数据集 需求信息
     * @return boolean
     * @author waco <etoupcom@126.com>
     */
	public function addData($orders, $data) {
        $info = M('UcenterMember')->field('username,realname,custom_service')->where(['id'=>$data['uid']])->find();
        $username = $info['custom_service']?$info['custom_service']:'admin';
        $uid = M('UcenterMember')->where(['username'=>$username])->getField('id');
        $map = [
            'uid'=>$uid,
            'username'=>$username,
            'orders'=> $orders,
            'with_funds' => $data['with_funds'],
            'need_username' => $info['username'],
            'deal_ratio' => $data['interest_rate'],
            'cose_ratio' => $data['benchmark']
        ];

        if($this->create($map))
            $back = $this->add();
        else
            $back = false;
        return $back;
	}
}
