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

class ForumPostReplyViewModel extends ViewModel {
	public $viewFields = array(
		'ForumPostReply' => array('*'),
		'Forum'          => array('title'          => 'name', '_on'          => 'ForumPostReply.forum_id=Forum.id'),
		'ForumPost'      => array('title', '_on'      => 'ForumPostReply.post_id=ForumPost.id'),
		'Member'         => array('nickname', '_on'         => 'ForumPostReply.uid=Member.uid'),
	);
}
