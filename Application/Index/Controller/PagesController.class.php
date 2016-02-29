<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Index\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class PagesController extends HomeController {

    /* 关于我们页 */
	public function about(){
        $this->seo = array('title' => '关于我们');
		$this->display();
	}

    //借款协议
    public function pact(){
        $this->seo = array('title' => '借款协议');
        $this->display();
    }

    //借款协议
    public function pactguzhi(){
        $this->seo = array('title' => '借款协议');
        $this->display();
    }

    //借款协议
    public function pactqihuo(){
        $this->seo = array('title' => '借款协议');
        $this->display();
    }

    /* 友情链接 */
    public function links(){
        $this->seo = array('title' => '友情链接');
        $this->display();
    }

    /* 新手指引 */
    public function beacon(){
        $this->seo = array('title' => '新手指引');
        $this->display();
    }

    /* 政策法规 */
    public function regulation(){
        $this->seo = array('title' => '政策法规');
        $this->display();
    }
}
