<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model\RelationModel;

class AccountModel extends RelationModel {
    protected $tableName = 'stock_account';
    protected $_link = [
        'NeedsAccount'=>[
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'NeedsAccount',
            'foreign_key' => 'aid',
            'mapping_name' => 'needsaccount',
            'mapping_order' => 'id desc',
        ]
    ];
}
