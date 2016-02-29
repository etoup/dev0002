<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Index\Model;
use Think\Model\RelationModel;

class MainBidsModel extends RelationModel {
    protected $_link = array(
        'MainTender' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'MainTender',
            'foreign_key' => 'bid',
            'mapping_name' => 'tenders',
            'mapping_order' => 'created_time desc',
        )
    );

    /**
     * 返回可投数据 重新获取数据 安全
     */
    public function may_throw($id){
        $info = $this->relation(true)->find($id);
        $with_funds = $info['with_funds']*10000;
        if(empty($info['tenders'])){
            $money = 0;
        }else{
            $money = 0;
            foreach($info['tenders'] as $v){
                $money += $v['money'];
            }
        }
        $may_throw = $with_funds-$money;

        return $may_throw;
    }
}