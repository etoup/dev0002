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
class MemberViewModel extends ViewModel{
	public $viewFields = array(
		'Member'=>array('uid','nickname','sex','birthday','qq','score','login','reg_ip','reg_time','last_login_ip','last_login_time','groupid','status'),
		'Ucenter_member'=>array('username','email','mobile','realname','balance','rate','maxscale', '_on'=>'Member.uid=Ucenter_member.id'),
	);
}
