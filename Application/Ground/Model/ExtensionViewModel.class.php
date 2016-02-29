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

class ExtensionViewModel extends ViewModel {
	public $viewFields = array(
		'Extension'   => array(
            'id',
            'uid',
            'tid',
            'username',
            'reg_time',
            'remark',
            'created_time'
        ),
        'UcenterMember' => array('username'=>'prevname','realname', 'mobile', '_on' => 'Extension.uid=UcenterMember.id'),
	);
}
