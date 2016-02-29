<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Model;
use Think\Model;

/**
 * 配资需求模型
 */

class MainNeedsModel extends Model {

    protected $_validate = array(
        array('uid', 'require', '请登录后操作', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('own_funds', 'require', '请填写自有资金', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('with_funds', 'require', '请选择或者填写配资资金', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('scale', 'check_scale', '超出配资比例', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('time_limit', 'require', '请选择或者填写资金使用期限', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('interest_rate', 'require', '月利率错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('aggre', 'require', '请同意相关协议', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('created_time', NOW_TIME, self::MODEL_INSERT),
        array('created_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '0', self::MODEL_INSERT),
    );

    /**
     * 保存数据并返回订单号
     */
    public function addData($data){
        $orders = $this->_getOrders();
        $data['orders'] = $orders;
        if($this->create($data)){
            $id = $this->add();
            if($id)
                return $orders;
            else
                return false;
        }else{
            return false;
        }
    }

    private function getMaxScale(){
        $maxscale = M('UcenterMember')->where(['id'=>UID])->getField('maxscale');
        return $maxscale;
    }

    /**
     * 检查配资比例
     */
    public function check_scale($data) {
        if($data['types']){
            $maxscale = C('QIHUOMAXSCALE')?intval(C('QIHUOMAXSCALE')):10;
        }else{
            $maxscale = $this->getMaxScale()?intval($this->getMaxScale()):C('MAXSCALE');
        }
        if (intval($data['scale']) > 0 && intval($data['scale']) <= $maxscale) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取订单号
     */
    private function _getOrders() {
        $orders = date('YmdHis').mt_rand(100000, 999999);
        $info   = M('MainNeeds')->where(array('orders' => $orders))->find();
        if (!empty($info)) {
            $this->getOrders();
        }
        return $orders;
    }
}
