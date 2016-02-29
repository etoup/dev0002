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

class MessageNoticesViewModel extends ViewModel {
	public $viewFields = array(
		'MessageNotices'   => array(
            'id',
            'uid',
            'types',
            'is_read',
            'is_ignore',
            'created_time',
            'update_time',
            'to_admin',
            'admin_con',
            'status'
        ),
		'UcenterMember' => array('username', 'realname', 'mobile', '_on' => 'MessageNotices.uid=UcenterMember.id'),
	);
}
