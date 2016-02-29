<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Ground\Model;
use Think\Model;

/**
 * 订单模型
 */

class OrdersModel extends Model {

    protected $_validate = array(
        array('uid', 'require', '请登录后操作', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('items_id', 'require', '缺少运动项目', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('vid', 'require', '缺少场馆信息', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bid', 'require', '缺少场地信息', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tid', 'require', '缺少订单模板信息', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('start', 'require', '缺少开始时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('end', 'require', '缺少结束时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('price', 'require', '缺少结果参数', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('nodes', 'require', '缺少时间节点', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('end_time', 'require', '缺少使用结束时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('created_time', NOW_TIME, self::MODEL_INSERT),
        array('pass_time', 'getPassTime', self::MODEL_INSERT,'function'),
    );

    /**
     * 保存数据并返回订单号
     */
    public function addData($data){
        $orders = $this->_getOrders($data);
        $data['num'] = $orders;
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

    /**
     * 获取订单号
     */
    private function _getOrders($data) {
        $items = M('Items')->find($data['items_id']);
        $orders = $items['initial'].date('YmdHis').mt_rand(100000, 999999);
        $info   = M('Orders')->where(array('num' => $orders))->find();
        if (!empty($info)) {
            $this->getOrders();
        }
        return $orders;
    }
}
