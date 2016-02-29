<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;

/**
 * 推广管理控制器
 */

class SpreadController extends UcenterController {

	/**
	 * 推广用户管理
	 */
    public function index() {
        if(IS_POST){
            $time_start=I('time_start', '');
            $time_end=I('time_end', '');
            if ($time_start && $time_end) {
                $map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
            }
            if ($time_start && !$time_end) {
                $map['created_time'] = array('gt', strtotime($time_start));
            }
            if (!$time_start && $time_end) {
                $map['created_time'] = array('lt', strtotime($time_end));
            }
            $this->search = true;

        }
        $map['uid'] = UID;
        $sort = I('get.sort')?I('get.sort'):'created_time desc';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('Extension',$map,$sort.$desc);
        int_to_string($list);
        $this->_list = $list;
        $this->url = $this->_get_spread_url();
        $this->money = $this->_get_spread_money();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '我的推广'];
        $this->crumbs = $this->_get_crumbs();
        $this->display();
    }

    /**
     * 推广资金管理
     */
    public function log() {
        if(IS_POST){
            $time_start=I('time_start', '');
            $time_end=I('time_end', '');
            if ($time_start && $time_end) {
                $map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
            }
            if ($time_start && !$time_end) {
                $map['created_time'] = array('gt', strtotime($time_start));
            }
            if (!$time_start && $time_end) {
                $map['created_time'] = array('lt', strtotime($time_end));
            }
            $this->search = true;

        }
        $map['uid'] = UID;
        $sort = I('get.sort')?I('get.sort'):'created_time desc';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('ExtensionLogView',$map,$sort.$desc,true,1);
        int_to_string($list);
        $this->_list = $list;
        $this->url = $this->_get_spread_url();
        $this->money = $this->_get_spread_money();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '我的推广'];
        $this->crumbs = $this->_get_crumbs();
        $this->display();
    }

    private function _get_spread_url(){
        $tags = think_encrypt(UID);
        $url = U('/Index/Index/index@'.$_SERVER['HTTP_HOST'],['tags'=>$tags]);
        return $url;
    }

    private function _get_spread_money(){
        $money = M('ExtensionLog')->where(['uid'=>UID])->sum('yield');
        return $money;
    }

    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
    }
}
