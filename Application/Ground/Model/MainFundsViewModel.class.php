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

class MainFundsViewModel extends ViewModel {
	public $viewFields = array(
		'MainFunds'   => array(
            'id',
            'uid',
            'amount',
            'balance',
            'types',
            'created_time',
            'created_ip',
            'remark'
        ),
        'UcenterMember' => array('realname','username', 'mobile', '_on' => 'MainFunds.uid=UcenterMember.id'),
	);
}
