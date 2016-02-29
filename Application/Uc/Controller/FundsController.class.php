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
 * 资金明细控制器
 */
class FundsController extends UcenterController {
	/**
	 * 资金管理首页
	 */
	public function index() {
        if(IS_POST){
            $status = I('post.types',0,'int');
            $map['types'] = $status;
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
        $sort = I('get.sort')?I('get.sort'):'id desc';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('MainFunds',$map,$sort.$desc);
        int_to_string($list);
        $this->_list = $list;
        $this->fundsTypes = fundsTypesArr();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = array('title' => '资金明细');
        $this->crumbs = $this->_get_crumbs();
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('indexweixin');
                break;
            default:
                $this->display();
        }
	}

    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
    }
}
