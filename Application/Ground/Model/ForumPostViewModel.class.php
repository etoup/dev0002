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

class ForumPostViewModel extends ViewModel {
	public $viewFields = array(
		'ForumPost' => array('id', 'forum_id', 'title', 'content', 'create_time', 'update_time', 'status', 'last_reply_time', 'view_count', 'reply_count', 'is_top', 'create_ip', 'operator', 'operate_time', 'reason'),
		'Forum'     => array('title'     => 'name', '_on'     => 'ForumPost.forum_id=Forum.id'),
		'Member'    => array('nickname', '_on'    => 'ForumPost.uid=Member.uid'),
	);
}
