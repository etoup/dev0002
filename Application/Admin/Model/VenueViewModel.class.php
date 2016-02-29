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

class VenueViewModel extends ViewModel {
	public $viewFields = array(
		'Venue'   => array(
            'id',
            'uid',
            'name',
            'status',
            'created_time',
            'update_time'
        ),
		'UcenterMember' => array('username', 'realname', 'mobile', 'email','_on' => 'Venue.uid=UcenterMember.id'),
	);
}
