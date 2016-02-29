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
 * 消息控制器
 */
class MessageController extends UcenterController {
	/**
	 * 未读消息管理
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
        $map['is_read'] = 0;
        $sort = I('get.sort')?I('get.sort'):'id desc';
        $list = $this->lists('MessageNotices',$map,$sort);
        int_to_string($list);
        $this->_list = $list;
        $this->unread_num = M('MessageNotices')->where($map)->count();
        $this->fundsTypes = fundsTypesArr();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = array('title' => '我的未读消息');
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

    /**
     * 已读消息管理
     */
    public function read() {
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
        $map['is_read'] = 1;
        $sort = I('get.sort')?I('get.sort'):'id desc';
        $list = $this->lists('MessageNotices',$map,$sort);
        int_to_string($list);
        $this->_list = $list;
        $this->unread_num = M('MessageNotices')->where(['uid'=>UID,'is_read'=>0])->count();
        $this->fundsTypes = fundsTypesArr();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = array('title' => '我的已读消息');
        $this->crumbs = $this->_get_crumbs();
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('readweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 查看
     */
    public function view() {
        $id = I('get.id',0,'int');
        $id or $this->error('请选择操作项');
        $mod = M('MessageNotices');

        $info = $mod->find($id);
        $mod->save(['id'=>$id,'is_read'=>1]);
        if(!I('get.nod'))
            M('Member')->where(['uid'=>$info['uid']])->setDec('notices');
        $this->info = $info;
        $this->unread_num = M('MessageNotices')->where(['uid'=>UID,'is_read'=>0])->count();
        $this->seo = array('title' => '查看消息详情');
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('viewweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 一键阅读
     */
    public function akeyToRead() {
        $mod = M('MessageNotices');
        $list = $mod->where(['uid'=>UID,'is_read'=>0])->select();
        if(!empty($list)){
            foreach($list as $k => $v){
                $mod->save(['id'=>$v['id'],'is_read'=>1]);
                M('Member')->where(['uid'=>$v['uid']])->setDec('notices');
            }
        }
        $backUrl = array('referer' => cookie('__forward__')?cookie('__forward__'):U('index'));
        ajaxMsg($backUrl);
    }

    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
    }
}
