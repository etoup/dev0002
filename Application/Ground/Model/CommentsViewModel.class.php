<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Ground\Model;
use Think\Model\ViewModel;

class CommentsViewModel extends ViewModel {
	public $viewFields = array(
		'Comments'   => array('*'),
        'UcenterMember' => array('username', 'realname', 'mobile', 'email','_on' => 'Comments.uid=UcenterMember.id')
	);
}
