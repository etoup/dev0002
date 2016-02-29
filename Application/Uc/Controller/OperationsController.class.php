<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;
use User\Api\UserApi;

/**
 * 收还款管理控制器
 */

class OperationsController extends UcenterController {

	/**
	 * 收还款管理
	 */
	public function index() {
        $list = M('MainRepayment')->where(['uid'=>UID])->order('time_repayment')->select();
        if(!empty($list)){
            $cn_total  = 0;//初始化收款总计
            $cn_receipt = 0;//初始化收款已收
            $cn_never = 0;//初始化收款未收

            $cr_total  = 0;//初始化还款总计
            $cr_repay = 0;//初始化还款已还
            $cr_norepay = 0;//初始还收款未还
            foreach($list as $k => $v){
                switch($v['types']){
                    case -1;
                        $cr_total++;
                        $v['status']?$cr_repay++:$cr_norepay++;
                        break;
                    case 0;
                    case 1;
                        $cn_total++;
                        $v['status']?$cn_receipt++:$cn_never++;
                        break;
                }
            }
            if($cn_never)
                $this->cn_info = M('MainRepayment')->where(['uid'=>UID,'types'=>0,'status'=>0])->order('time_repayment')->find();
            if($cr_norepay)
                $cr_info = M('MainRepayment')->where(['uid'=>UID,'types'=>-1,'status'=>0])->order('time_repayment')->find();
            if($cr_info['orders']){
                $cr_info['nid']= M('MainNeeds')->where(['orders'=>$cr_info['orders']])->getField('id');
            }

            $this->cr_info = $cr_info;
            $this->cn_total = $cn_total;
            $this->cn_receipt = $cn_receipt;
            $this->cn_never = $cn_never;
            $this->cr_total = $cr_total;
            $this->cr_repay = $cr_repay;
            $this->cr_norepay = $cr_norepay;
        }
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '我的收还款'];
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
     * 收款管理
     */
    public function collections() {
        $map['uid'] = UID;
        $map['types'] = ['in',[0,1]];
        if(IS_POST){
            $status = I('post.status',0,'int');
            $map['status'] = $status;
            $orders = I('post.orders','');
            $orders and $map['orders'] = ['like','%'.$orders.'%'];
            $time_start=I('time_start', '');
            $time_end=I('time_end', '');
            if ($time_start && $time_end) {
                $map['time_repayment'] = array('between', [strtotime($time_start),strtotime($time_end)]);
            }
            if ($time_start && !$time_end) {
                $map['time_repayment'] = array('gt', strtotime($time_start));
            }
            if (!$time_start && $time_end) {
                $map['time_repayment'] = array('lt', strtotime($time_end));
            }
            $this->search = true;
        }
        $sort = I('get.sort')?I('get.sort'):'status desc,time_repayment,orders';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('MainRepayment',$map,$sort.$desc);
        int_to_string($list);
        $this->_list = $list;
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '我的收款记录'];
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('collectionsweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 还款管理
     */
    public function repayment() {
        $map['uid'] = UID;
        $map['types'] = -1;
        if(IS_POST){
            $status = I('post.status',0,'int');
            $map['status'] = $status;
            $orders = I('post.orders','');
            $orders and $map['orders'] = ['like','%'.$orders.'%'];
            $time_start=I('time_start', '');
            $time_end=I('time_end', '');
            if ($time_start && $time_end) {
                $map['time_repayment'] = array('between', [strtotime($time_start),strtotime($time_end)]);
            }
            if ($time_start && !$time_end) {
                $map['time_repayment'] = array('gt', strtotime($time_start));
            }
            if (!$time_start && $time_end) {
                $map['time_repayment'] = array('lt', strtotime($time_end));
            }
            $this->search = true;
        }
        if($id = I('get.id',0,'int')){
            $map['id'] = $id;
        }
        $sort = I('get.sort')?I('get.sort'):'status desc,time_repayment,orders';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('MainRepayment',$map,$sort.$desc);
        int_to_string($list);
        $this->_list = $list;
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '我的还款记录'];
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('repaymentweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 支付利息
     */
    public function pay(){
        if(IS_POST){
            list(
                $id,
                $orders,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.orders',''),
                I('post.paypwd','')
            ];
            $orders or $this->error('无效单号');
            $paypwd or $this->error('请填写支付密码');
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                $this->error('无效单号');
            }
            $mod = M('MainRepayment');
            $lxInfo = $mod->where(['uid'=>UID,'orders'=>$orders,'status'=>0])->order('time_repayment')->find();
            if($pass_date = $this->get_pass_date($lxInfo['time_repayment'])){
                $bx = $info['with_funds']*10000+$lxInfo['money'];
                $pass_money = $this->get_pass_money($lxInfo['time_repayment'],$bx);
                $money = $lxInfo['money'] + $pass_money;
                $interest = $money;//利息+罚金

            }else{
                $interest = $lxInfo['money'];//利息
            }
            if($diff = $this->_checkMoney($interest)){
                $this->error('余额不足，请充值',U('Uc/Recharge/index',['amount'=>$diff]));
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                //是否预期
                if($pass_date){
                    //资金记录-利息
                    D('MainFunds')->addData(-$lxInfo['money'],23);
                    //资金记录-逾期罚金
                    D('MainFunds')->addData(-$pass_money,29);
                    //逾期罚金站内信
                    $title = '您成功支付了逾期罚息';
                    $user_con = time_format().'您成功支付了一笔逾期罚息，逾期天数：'.$pass_date.'天，罚息金额：'.money($pass_money).'，单号：'.$orders;
                    D('MessageNotices')->saveData(UID,$title,$user_con);
                    //站内信
                    $title = '您成功支付了一笔利息';
                    $user_con = time_format().'您成功支付了一笔利息，金额：'.money($lxInfo['money']).'，单号：'.$orders;
                    $admin_con = time_format().'用户成功支付利息，用户名：'.USERNAME.'，金额：'.money($lxInfo['money']).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Repayment/index">立即查看</a>';
                    D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                }else{
                    D('MainFunds')->addData(-$interest,23);
                    //站内信
                    $title = '您成功支付了一笔利息';
                    $user_con = time_format().'您成功支付了一笔利息，金额：'.money($interest).'，单号：'.$orders;
                    $admin_con = time_format().'用户成功支付利息，用户名：'.USERNAME.'，金额：'.money($interest).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Repayment/index">立即查看</a>';
                    D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                }
                //修改预警状态
                $earlyMsg = M('MessageNotices')->where(['param'=>$lxInfo['id']])->find();
                if(!empty($earlyMsg)){
                    M('MessageNotices')->save(['id'=>$earlyMsg['id'],'early'=>0]);
                }
                //调整状态到已付
                $mod->where(['id'=>$lxInfo['id']])->save(['status'=>1,'time_do_repaymen'=>NOW_TIME]);
                //业务提成
                api('Needs/doStatisticsTake',[$info]);

                $this->success('支付成功');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $orders = I('get.orders','');
            $orders or ajaxMsg('请选择操作项',false);
            $info = M('MainNeeds')->where(['orders'=>$orders])->find();
            $lxInfo = M('MainRepayment')->find($id);
            $this->lx = money($lxInfo['money']);
            $this->paytime = prevDate($lxInfo['time_repayment'],'Y-m-d');
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = money($balance);

            if($pass_date = $this->get_pass_date($lxInfo['time_repayment'])){
                $bx = $info['with_funds']*10000+$lxInfo['money'];
                $this->pass_date = $pass_date;
                $pass_money = $this->get_pass_money($lxInfo['time_repayment'],$bx);
                $this->pass_money = money($pass_money);

                $money = $lxInfo['money'] + $pass_money;
                if($money > $balance){
                    $this->amount = round($money-$balance,2);
                    $this->recharge = true;
                }else{
                    $this->amount = round($money,2);
                }
            }else{
                if($lxInfo['money'] > $balance){
                    $this->amount = round($lxInfo['money']-$balance,2);
                    $this->recharge = true;
                }
            }
            $this->info = $info;
            $this->display();
        }
    }

    /**
     * 计算预期天数
     */
    private function get_pass_date($pay_time){
        $time_pass_date = intval(ceil((NOW_TIME-$pay_time)/3600/24));
        if($time_pass_date > 0){
            return $time_pass_date;
        }else{
            return false;
        }
    }

    /**
     * 计算预期罚金
     */
    private function get_pass_money($pay_time,$bx){
        if($time_pass_date = $this->get_pass_date($pay_time)){
            if($time_pass_date < 7){
                $pass_money=$bx*0.1/100*$time_pass_date;
            }
            if($time_pass_date >= 7){
                $pass_money=$bx*0.3/100*$time_pass_date;
            }
            return $pass_money;
        }else{
            return false;
        }
    }

    /**
     * 验证是否设置支付密码
     * @return boolean     检测结果
     */
    private function _checkHasPwd() {
        $info = M('UcenterMember')->find(UID);
        return $info['pass_paypwd'];
    }

    /**
     * 验证密码
     * @param  string $password 密码
     * @param  boolean $paypwd 是否验证支付密码
     * @return boolean     检测结果
     */
    private function _checkPwd($password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser(UID, $password,$paypwd);
    }

    /**
     * 验证额度
     * @param  int $money 密码
     * @return boolean     检测结果
     */
    private function _checkMoney($money) {
        $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
        if($money > $balance){
            $difference = $money-$balance;
            return $difference;
        }else{
            return 0;
        }
    }
}
