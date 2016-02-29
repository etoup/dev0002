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

class UcenterMemberViewModel extends ViewModel {
	public $viewFields = array(
		'UcenterMember' => array('id', 'username', 'email', 'mobile', 'reg_time', 'reg_ip', 'status', 'pass_phone', 'pass_email', 'pass_realname', 'reason_realname', 'pass_paypwd'),
        //'Member'      => array('groupid', 'memberid', '_on'      => 'Member.uid=UcenterMember.id'),
        'Userdata'      => array('realname', 'card_number', 'card_front', 'card_back', 'card_group', '_on'      => 'Userdata.uid=UcenterMember.id')
	);
}
