<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model\ViewModel;

class MainRecordViewModel extends ViewModel {
	public $viewFields = array(
		'MainRecord'   => array(
            'id',
            'uid',
            'username',
            'billno',
            'operation_amount',
            'amount',
            'rate',
            'fees',
            'methods',
            'info',
            'remark',
            'status',
            'created_time',
            'update_time'
        ),
        'UcenterMember' => array('realname', 'mobile', '_on' => 'MainRecord.uid=UcenterMember.id'),
	);
}
