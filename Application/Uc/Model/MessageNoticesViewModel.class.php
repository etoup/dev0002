<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Uc\Model;
use Think\Model\ViewModel;

class MessageNoticesViewModel extends ViewModel {
	public $viewFields = array(
		'MessageNotices'      => array('id', 'uid', 'types', 'param', 'is_read', 'is_ignore', 'title', 'user_con','status', 'created_time','update_time'),
		'UcenterMember' => array('username', 'realname', 'mobile', '_on' => 'MessageNotices.uid=UcenterMember.id'),
	);
}
