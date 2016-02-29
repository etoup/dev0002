<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Index\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class IndexWidget extends Controller{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function link($cateId){
        $link = D('LinkType')->relation(true)->where(['typeid'=>$cateId])->find();
        $this->assign('link',$link);
        if($cateId == 1){
            $this->assign('txt',true);
        }
		$this->display('Index_link');
	}
    /* 显示指定分类的同级分类或子分类列表 */
    public function notice($cid){
        $notice = D('DocumentView')->where(['category_id'=>$cid,'status'=>1])->order('id desc')->limit(7)->select();
        $this->assign('notice',$notice);
        $this->display('Index_notice');
    }
}
