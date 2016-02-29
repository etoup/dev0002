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
 * 充值管理控制器
 * @author waco <etoupcom@126.com>
 */
class RecordController extends AdminController {

	/**
	 * 银行转账待审管理
	 * @author waco <etoupcom@126.com>
	 */
    public function index(){
        $map['methods'] = 1;
        $map['status'] = 0;
        list(
            $username,
            $uid,
            $time_start,
            $time_end,
            $minContent,
            $maxContent,
            $p,
            $soso
            ) = array(
            I('username', ''),
            I('uid', ''),
            I('time_start', ''),
            I('time_end', ''),
            I('minContent', 0, 'int'),
            I('maxContent', 0, 'int'),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $username && $map['username'] = array('like', '%'.(string) $username.'%');
        $uid && $map['uid']           = intval($uid);
        if ($time_start && $time_end) {
            $map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
        }
        if ($time_start && !$time_end) {
            $map['created_time'] = array('gt', strtotime($time_start));
        }
        if (!$time_start && $time_end) {
            $map['created_time'] = array('lt', strtotime($time_end));
        }
        if ($minContent && $maxContent && ($minContent < $maxContent)) {
            $map['amount'] = array('between', [$minContent,$maxContent]);
        }
        if ($minContent && !$maxContent) {
            $map['amount'] = array('gt', $minContent);
        }
        if ($maxContent && !$minContent) {
            $map['amount'] = array('lt', $maxContent);
        }
        $list = $this->lists('MainRecordView', $map,'',true,1);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->username   = $username;
        $this->uid        = $uid;
        $this->time_start = $time_start;
        $this->time_end   = $time_end;
        $this->minContent = $minContent?$minContent:'';
        $this->maxContent = $maxContent?$maxContent:'';
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->display();
    }

    /**
     * 充值管理
     * @author waco <etoupcom@126.com>
     */
    public function all(){
        list(
            $username,
            $billno,
            $uid,
            $time_start,
            $time_end,
            $minContent,
            $maxContent,
            $p,
            $soso
            ) = array(
            I('username', ''),
            I('billno', ''),
            I('uid', ''),
            I('time_start', ''),
            I('time_end', ''),
            I('minContent', 0, 'int'),
            I('maxContent', 0, 'int'),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $username && $map['username'] = array('like', '%'.(string) $username.'%');
        $billno && $map['billno'] = array('like', '%'.(string) $billno.'%');
        $uid && $map['uid']           = intval($uid);
        if ($time_start && $time_end) {
            $map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
        }
        if ($time_start && !$time_end) {
            $map['created_time'] = array('gt', strtotime($time_start));
        }
        if (!$time_start && $time_end) {
            $map['created_time'] = array('lt', strtotime($time_end));
        }
        if ($minContent && $maxContent && ($minContent < $maxContent)) {
            $map['amount'] = array('between', [$minContent,$maxContent]);
        }
        if ($minContent && !$maxContent) {
            $map['amount'] = array('gt', $minContent);
        }
        if ($maxContent && !$minContent) {
            $map['amount'] = array('lt', $maxContent);
        }
        $list = $this->lists('MainRecordView', $map,'',true,1);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->username   = $username;
        $this->billno     = $billno;
        $this->uid        = $uid;
        $this->time_start = $time_start;
        $this->time_end   = $time_end;
        $this->minContent = $minContent?$minContent:'';
        $this->maxContent = $maxContent?$maxContent:'';
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->display();
    }

    /**
     * 后台充值管理
     * @author waco <etoupcom@126.com>
     */
    public function backs(){
        list(
            $username,
            $uid,
            $time_start,
            $time_end,
            $minContent,
            $maxContent,
            $p,
            $soso
            ) = array(
            I('username', ''),
            I('uid', ''),
            I('time_start', ''),
            I('time_end', ''),
            I('minContent', 0, 'int'),
            I('maxContent', 0, 'int'),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $username && $map['UcenterMember.username'] = array('like', '%'.(string) $username.'%');
        if($username){
            $where['UcenterMember.username'] = array('like', '%'.(string) $username.'%');
            $where['UcenterMember.realname'] = array('like', '%'.(string) $username.'%');
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        $uid && $map['uid']           = intval($uid);
        if ($time_start && $time_end) {
            $map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
        }
        if ($time_start && !$time_end) {
            $map['created_time'] = array('gt', strtotime($time_start));
        }
        if (!$time_start && $time_end) {
            $map['created_time'] = array('lt', strtotime($time_end));
        }
        if ($minContent && $maxContent && ($minContent < $maxContent)) {
            $map['amount'] = array('between', [$minContent,$maxContent]);
        }
        if ($minContent && !$maxContent) {
            $map['amount'] = array('gt', $minContent);
        }
        if ($maxContent && !$minContent) {
            $map['amount'] = array('lt', $maxContent);
        }
        $map['types'] = 0;
        $list = $this->lists('MainFundsView', $map,'',true,1);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->username   = $username;
        $this->uid        = $uid;
        $this->time_start = $time_start;
        $this->time_end   = $time_end;
        $this->minContent = $minContent?$minContent:'';
        $this->maxContent = $maxContent?$maxContent:'';
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->display();
    }

    /**
     * 回收站
     * @author waco <etoupcom@126.com>
     */
    public function recycle(){
        $map['methods'] = 1;
        $map['status'] = -1;
        list(
            $username,
            $uid,
            $time_start,
            $time_end,
            $minContent,
            $maxContent,
            $p,
            $soso
            ) = array(
            I('username', ''),
            I('uid', ''),
            I('time_start', ''),
            I('time_end', ''),
            I('minContent', 0, 'int'),
            I('maxContent', 0, 'int'),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $username && $map['username'] = array('like', '%'.(string) $username.'%');
        $uid && $map['uid']           = intval($uid);
        if ($time_start && $time_end) {
            $map['created_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
        }
        if ($time_start && !$time_end) {
            $map['created_time'] = array('gt', strtotime($time_start));
        }
        if (!$time_start && $time_end) {
            $map['created_time'] = array('lt', strtotime($time_end));
        }
        if ($minContent && $maxContent && ($minContent < $maxContent)) {
            $map['amount'] = array('between', [$minContent,$maxContent]);
        }
        if ($minContent && !$maxContent) {
            $map['amount'] = array('gt', $minContent);
        }
        if ($maxContent && !$minContent) {
            $map['amount'] = array('lt', $maxContent);
        }
        $list = $this->lists('MainRecordView', $map,'',true,1);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->username   = $username;
        $this->uid        = $uid;
        $this->time_start = $time_start;
        $this->time_end   = $time_end;
        $this->minContent = $minContent?$minContent:'';
        $this->maxContent = $maxContent?$maxContent:'';
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->display();
    }

    /**
     * 审核
     * @author waco <etoupcom@126.com>
     */
    public function examine(){
        if(IS_POST){
            $id = I('post.id',0,'int');
            $id or $this->error('请选择操作项');
            $info = M('MainRecord')->find($id);
            $back = D('MainRecord')->saveData($id,I('post.remark'));
            if($back){
                D('MainFunds')->saveData($info['amount'],1,$info['uid'],'银行转账');
                //站内信
                $title = '确认收到您的一笔银行转账';
                $user_con = time_format().'确认收到您的一笔银行转账，金额：'.$info['amount'].'；<a href="index.php/Uc/Recharge/log">立即查看</a>';
                D('MessageNotices')->saveData($info['uid'],$title,$user_con);
                ajaxMsg('操作成功');
            }else{
                ajaxMsg('操作失败',false);
            }

        }else{
            $id = I('get.id',0,'int');
            $id or $this->error('请选择操作项');
            $info = M('MainRecord')->find($id);
            $info['info'] = unserialize($info['info']);
            $this->info = $info;
            $this->display();
        }
    }

    /**
     * 回收
     * @author waco <etoupcom@126.com>
     */
    public function del(){
        $id = I('id');
        if(!empty($id)){
            $back = M('MainRecord')->where(['id'=>['in',$id]])->setField('status',-1);
            if($back)
                ajaxMsg('操作成功');
            else
                ajaxMsg('操作失败',false);
        }else{
            ajaxMsg('请选择操作项',false);
        }
    }
}
