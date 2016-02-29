<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Weixin\Model;
use Think\Model\ViewModel;

class DocumentViewModel extends ViewModel {
	public $viewFields = array(
		'Document'   => array(
            'id',
            'uid',
            'title',
            'category_id',
            'description',
            'status',
            'create_time'
        ),
		'DocumentArticle' => array('content', '_on' => 'Document.id=DocumentArticle.id'),
	);
}
