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

class MyCardViewModel extends ViewModel {
	public $viewFields = array(
		'MyCard'   => array(
            'id',
            'uid',
            'bid',
            'bank_name',
            'full_name',
            'card_number',
            'card_number_format',
            'status',
            'created_time'
        ),
		'Banks' => array('name','code', '_on' => 'MyCard.bid=Banks.id'),
        'UcenterMember' => array('username','realname', 'mobile', '_on' => 'MyCard.uid=UcenterMember.id'),
	);
}
