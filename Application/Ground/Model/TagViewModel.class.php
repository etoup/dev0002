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

class TagViewModel extends ViewModel {
	public $viewFields = array(
		'Tag'         => array('tag_id', 'parent_tag_id', 'tag_name', 'tag_logo', 'ifhot', 'excerpt', 'content_count', 'attention_count', 'hits', 'iflogo'),
		'TagCategory' => array('username', 'email', 'mobile', '_on' => 'Member.uid=Ucenter_member.id'),
	);
}
