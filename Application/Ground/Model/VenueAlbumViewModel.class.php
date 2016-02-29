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

class VenueAlbumViewModel extends ViewModel {
	public $viewFields = array(
		'VenueAlbum'   => array('*'),
		'Picture' => array('path', 'create_time', '_on' => 'VenueAlbum.pid=Picture.id')
	);
}
