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
 * 客户管理控制器
 */

class CustomerController extends UcenterController {

	/**
	 * 我的客户管理
	 */
    public function index() {
        if(IS_POST){
            $time_start=I('time_start', '');
            $time_end=I('time_end', '');
            if ($time_start && $time_end) {
                $map['reg_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
            }
            if ($time_start && !$time_end) {
                $map['reg_time'] = array('gt', strtotime($time_start));
            }
            if (!$time_start && $time_end) {
                $map['reg_time'] = array('lt', strtotime($time_end));
            }
            $this->search = true;

        }
        $map['custom_service'] = USERNAME;
        $sort = I('get.sort')?I('get.sort'):'reg_time desc';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('UcenterMember',$map,$sort.$desc,'id,username,realname,email,mobile,reg_time,remark');
        int_to_string($list);
        $this->_list = $list;
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '客户管理'];
        $this->crumbs = $this->_get_crumbs();
        $this->display();
    }

    public function remark(){
        if(IS_POST){
            $id = I('post.id',0,'int');
            $remark = I('post.remark','');
            $id or $this->error('请选择操作项');
            M('UcenterMember')->save(['id'=>$id,'remark'=>$remark]);
            $this->success('操作成功');
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $this->info = M('UcenterMember')->field('id,username,realname,email,mobile,reg_time,remark')->find($id);
            $this->display();
        }
    }

    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
    }
}
