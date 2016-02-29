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
 * 后台资金明细控制器
 * @author waco <etoupcom@126.com>
 */

class FundsController extends AdminController {

	/**
	 * 资金管理
	 * @author waco <etoupcom@126.com>
	 */
	public function logs() {
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
            $map['UcenterMember.username'] = array('like', '%'.(string) $keyword.'%');
            $map['UcenterMember.realname'] = array('like', '%'.(string) $keyword.'%');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        if ($time_start && $time_end) {
            $where['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
        }
        if ($time_start && !$time_end) {
            $where['created_time'] = array('gt', strtotime($time_start));
        }
        if (!$time_start && $time_end) {
            $where['created_time'] = array('lt', strtotime($time_end));
        }
		$list = $this->lists('MainFundsView', $where,'',true,1);
		int_to_string($list);
		$this->assign('_list', $list);
        $this->keyword   = $keyword;
        $this->time_start = $time_start;
        $this->time_end   = $time_end;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
		$this->display();
	}

}