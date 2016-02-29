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

class LocksModel extends Model {

    protected $_validate = array(
        array('items_id', 'require', '缺少运动项目', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bid', 'require', '缺少场地信息', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tid', 'require', '缺少订单模板信息', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('start', 'require', '缺少开始时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('end', 'require', '缺少结束时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('nodes', 'require', '缺少时间节点', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('created_time', NOW_TIME, self::MODEL_INSERT)
    );

    /**
     * 保存数据并返回订单号
     */
    public function addData($data){
        if($this->create($data)){
            $id = $this->add();
            if($id)
                return true;
            else
                return false;
        }else{
            return false;
        }
    }
}
