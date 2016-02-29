<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;


/**
 * 后台消息管理控制器
 * @author waco <etoupcom@126.com>
 */

class MessageNoticesController extends AdminController {

	/**
	 * 消息管理管理
	 * @author waco <etoupcom@126.com>
	 */
    public function index() {
        list(
            $keyword,
            $time_start,
            $time_end,
            $p,
            $soso
            ) = array(
            trim(I('keyword', '')),
            I('time_start', ''),
            I('time_end', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        if($keyword){
            $map['username'] = array('like', '%'.(string) $keyword.'%');
            $map['realname'] = array('like', '%'.(string) $keyword.'%');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        if ($time_start && $time_end) {
            $where['time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
        }
        if ($time_start && !$time_end) {
            $where['time'] = array('gt', strtotime($time_start));
        }
        if (!$time_start && $time_end) {
            $where['time'] = array('lt', strtotime($time_end));
        }
        $where['to_admin'] = 1;
        $list = $this->lists('MessageNoticesView', $where,'is_ignore,created_time desc',true,1);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->keyword   = $keyword;
        $this->time_start = $time_start;
        $this->time_end   = $time_end;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->display();
    }

    /**
     * 一键阅读
     * @author waco <etoupcom@126.com>
     */
    public function read(){
        $id = I('post.id',array());
        if(!empty($id)){
            M('MessageNotices')->where(['id'=>['in',$id]])->setField('is_ignore',1);
            ajaxMsg('操作成功');
        }else{
            ajaxMsg('请选择操作项',false);
        }
    }

}