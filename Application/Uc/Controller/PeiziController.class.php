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
 * 我的配资管理控制器
 */

class PeiziController extends UcenterController {

	/**
	 * 配资管理
	 */
	public function index() {
        $map = ['uid'=>UID,'types'=>0];

        list(
            $status,
            $orders,
            $p,
            $soso
            ) = array(
            I('request.status'),
            I('request.orders',''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        is_numeric($status) and $map['status'] = $status;
        $orders and $map['orders'] = ['like','%'.$orders.'%'];

        $sort = I('request.sort')?I('request.sort'):'created_time DESC';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('MainNeeds',$map,$sort.$desc);
        int_to_string($list);
        if(is_array($list) and !empty($list)){
            foreach($list as $k => $v){
                $lixiCount = M('MainRepayment')->where(['uid'=>UID,'orders'=>$v['orders'],'status'=>0])->count('id');
                $list[$k]['lxCount'] = $lixiCount;
                if($v['status'] == 8){
                    $list[$k]['full_time'] = M('MainBids')->where(['orders'=>$v['orders']])->getField('update_time');
                    $list[$k]['delay_info'] = M('MainNeedsdata')->where(['nid'=>$v['id'],'types'=>0])->select();
                    $list[$k]['early_info'] = M('MainNeedsdata')->where(['nid'=>$v['id'],'types'=>1])->select();
                    $end_trading = strtotime('-1 days',$v['end_trading']);
                    $list[$k]['differDays'] = dateDiff(time_format(NOW_TIME,'Y-m-d'),time_format($end_trading,'Y-m-d'));
                }
            }
        }
        $this->_list = $list;
		$this->seo = ['title' => '我的股票配资'];
        $this->crumbs = $this->_get_crumbs();
        $this->peiziStu = peiziStuArr();
        $this->status = $status;
        $this->orders   = $orders;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
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
     * 支付本息
     */
    public function paybx(){
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
            $own_funds = intval($info['own_funds'])*10000;//本金
            $interest = $info['interest_rate']/100*$own_funds*$info['scale'];//利息
            $money = $own_funds+$interest;
            if($diff = $this->_checkMoney($money)){
                $this->error('余额不足，请充值',U('Uc/Recharge/index',['amount'=>$diff]));
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                //资金记录-首期保证金
                D('MainFunds')->addData(-$own_funds,21);
                //资金记录-利息
                D('MainFunds')->addData(-$interest,23);
                //调整状态
                if($info['make_bids']){
                    M('MainNeeds')->where(['orders'=>$orders])->setField('status',2);
                    //生成标的
                    api('Needs/makeBids',[$info]);
                }
                else{
                    //生成标的
                    api('Needs/makeBids',[$info,1]);
                    //默认投标人
                    api('Needs/doDefaultTender',[$info]);
                    M('MainNeeds')->where(['orders'=>$orders])->setField('status',3);
                }
                //站内信
                $title = '您成功支付本金和利息';
                $user_con = time_format().'您成功支付本金和首月利息，保证金金额：'.money($own_funds).'，利息金额：'.money($interest).'，单号：'.$orders;
                $admin_con = time_format().'用户成功支付本金和利息，用户名：'.USERNAME.'，保证金金额：'.money($own_funds).'，利息金额：'.money($interest).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Bond/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                $this->success('支付成功');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $info = M('MainNeeds')->find($id);
            $this->id = $id;
            $this->orders = $info['orders'];
            $bx = round(($info['own_funds']*10000)+($info['with_funds']*10000*$info['interest_rate']/100),2);
            $this->bx = money($bx);
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = money($balance);
            if($bx > $balance){
                $this->amount = round($bx-$balance,2);
                $this->recharge = true;
            }

            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('paybxweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 支付利息
     */
    public function paylx(){
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
                //是否逾期
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
            $info = M('MainNeeds')->find($id);
            $this->id = $id;
            $this->orders = $info['orders'];
            $lxInfo = M('MainRepayment')->where(['uid'=>UID,'orders'=>$info['orders'],'status'=>0])->order('time_repayment')->find();
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
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('paylxweixin');
                    break;
                default:
                    $this->display();
            }
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
     * 支付保证金
     */
    public function paybzj(){
        if(IS_POST){
            list(
                $id,
                $orders,
                $money,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.orders',''),
                I('post.money',0,'abs'),
                I('post.paypwd','')
            ];
            $orders or $this->error('无效单号');
            $money or $this->error('请填写支付金额');
            $paypwd or $this->error('请填写支付密码');
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                $this->error('无效单号');
            }
            if($diff = $this->_checkMoney($money)){
                $this->error('余额不足，请充值',U('Uc/Recharge/index',['amount'=>$diff]));
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                //资金记录-利息
                D('MainFunds')->addData(-$money,22);
                //记录保证金日志
                M('MainBond')->add(['orders'=>$orders,'uid'=>UID,'money'=>$money,'types'=>1,'status'=>1,'created_time'=>NOW_TIME]);
                //站内信
                $title = '您成功支付了一笔保证金';
                $user_con = time_format().'您成功支付了一笔保证金，金额：'.money($money).'，单号：'.$orders;
                $admin_con = time_format().'用户成功支付保证金，用户名：'.USERNAME.'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Bond/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                $this->success('支付成功');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $info = M('MainNeeds')->find($id);
            $this->id = $id;
            $this->orders = $info['orders'];
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = money($balance);

            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('paybzjweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 账户出金
     */
    public function shrank(){
        if(IS_POST){
            list(
                $id,
                $cid,
                $orders,
                $money,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.cid',0,'int'),
                I('post.orders',''),
                I('post.money',0,'abs'),
                I('post.paypwd','')
            ];
            $id or $this->error('请选择操作项');
            $orders or $this->error('无效单号');
            $money or $this->error('请填写出金金额');
            $paypwd or $this->error('请填写支付密码');
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                $this->error('无效单号');
            }
            if($money < 1000){
                $this->error('每次出金金额必须大于1000');
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                //记录出金日志
                $data = [
                    'uid'=>UID,
                    'orders'=>$orders,
                    'username'=>USERNAME,
                    'operation_amount'=>$money,
                    'amount'=>$money,
                    'created_time'=>NOW_TIME,
                    'created_ip'=>get_client_ip(),
                    'remark'=>'成功提交申请，等待审核'
                ];
                if($cid){
                    $data['cid'] = $cid;
                    $data['types'] = 1;
                    $data['do_types'] = 1;
                }
                M('MainDraw')->add($data);
                //站内信
                $title = '您成功提交账户出金申请';
                $user_con = time_format().'您成功提交账户出金申请，等待审核，金额：'.money($money).'，单号：'.$orders;
                if($cid)
                    $admin_con = time_format().'用户提交账户出金 -> 提现申请，用户名：'.USERNAME.'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Draw/index">立即查看</a>';
                else
                    $admin_con = time_format().'用户提交账户出金申请，用户名：'.USERNAME.'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Shrank/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                $this->success('申请成功，等待审核',U('Uc/Cash/log'));
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $info = M('MainNeeds')->find($id);
            $this->id = $id;
            $this->orders = $info['orders'];
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = money($balance);
            $this->myCard = D('MyCardView')->where(['uid'=>UID,'status'=>1])->select();
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('shrankweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 申请延期
     */
    public function delay(){
        if(IS_POST){
            list(
                $id,
                $orders,
                $delay_time,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.orders',''),
                I('post.delay_time',0,'abs'),
                I('post.paypwd','')
            ];
            $id or $this->error('请选择操作项');
            $orders or $this->error('无效单号');
            $delay_time or $this->error('请填写延期时间');
            $paypwd or $this->error('请填写支付密码');
            is_numeric($delay_time) or $this->error('延期时间为整数');
            is_float($delay_time) and $this->error('延期时间为整数');
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                $this->error('无效单号');
            }
            if(intval($delay_time) > 12){
                $this->error('延期时间必须小于12个月');
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            $delayfee = C('DELAYFEE')?C('DELAYFEE'):0.1;
            $delay = round($info['with_funds']*10000*$delayfee/100,2);
            if($diff = $this->_checkMoney($delay)){
                $this->error('余额不足，请充值',U('Uc/Recharge/index',['amount'=>$diff]));
            }
            if($this->_checkPwd($paypwd,true)){
                $delayfee = C('DELAYFEE')?C('DELAYFEE'):0.1;
                $money = round($info['with_funds']*10000*$delayfee/100,2);
                //记录操作日志
                $data = [
                    'uid'=>UID,
                    'nid'=>$id,
                    'delay_time'=>$delay_time,
                    'money'=>$money,
                    'created_time'=>NOW_TIME,
                    'remark'=>'成功提交延期申请，等待审核'
                ];
                M('MainNeedsdata')->add($data);
                //资金记录-利息
                D('MainFunds')->addData(-$money,30);
                //站内信
                $title = '您成功提交延期申请';
                $user_con = time_format().'您成功提交延期申请，等待审核，单号：'.$orders;
                $admin_con = time_format().'用户提交延期申请，用户名：'.USERNAME.'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/delay">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                $this->success('申请成功，等待审核');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $info = M('MainNeeds')->find($id);
            $delayfee = C('DELAYFEE')?C('DELAYFEE'):0.1;
            $this->delay = round($info['with_funds']*10000*$delayfee/100,2);
            $this->info = $info;
            $end_trading = strtotime('-1 days',$this->info['end_trading']);
            if(NOW_TIME < $end_trading)
                $this->differDays = dateDiff(time_format(NOW_TIME,'Y-m-d'),time_format($end_trading,'Y-m-d'));
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = money($balance);
            if($this->delay > $balance){
                $this->amount = round($this->delay-$balance,2);
                $this->recharge = true;
            }
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('delayweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 提前结束
     */
    public function early(){
        if(IS_POST){
            list(
                $id,
                $cid,
                $orders,
                $money,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.cid',0,'int'),
                I('post.orders',''),
                I('post.money'),
                I('post.paypwd','')
            ];
            $id or $this->error('请选择操作项');
            $orders or $this->error('无效单号');
            $money or $this->error('请填写股票账户总资产');
            $paypwd or $this->error('请填写支付密码');
            is_numeric($money) or $this->error('股票账户总资产为数值');
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                $this->error('无效单号');
            }
            if(intval($money) < 0){
                $this->error('股票账户总资产为正数');
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                //记录操作日志
                $data = [
                    'uid'=>UID,
                    'nid'=>$id,
                    'cid'=>$cid,
                    'types'=>1,
                    'balance'=>$money,
                    'created_time'=>NOW_TIME,
                    'remark'=>'成功提交结束申请，等待审核'
                ];
                M('MainNeedsdata')->add($data);
                //站内信
                $title = '您成功提交结束申请';
                $user_con = time_format().'您成功提交结束申请，等待审核，单号：'.$orders;
                $admin_con = time_format().'用户提交结束申请，用户名：'.USERNAME.'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/early">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                $this->success('申请成功，等待审核');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $this->info = M('MainNeeds')->find($id);
            $end_trading = strtotime('-1 days',$this->info['end_trading']);
            $this->differDays = dateDiff(time_format(NOW_TIME,'Y-m-d'),time_format($end_trading,'Y-m-d'));
            $this->myCard = D('MyCardView')->where(['uid'=>UID,'status'=>1])->select();
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('earlyweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 账户图表信息
     */
    public function chart(){
        $id = I('get.id',0,'int');
        $id or ajaxMsg('请选择操作项',false);
        $info = M('MainNeeds')->find($id);
        $warning_val = C('WARNINGLINE')?C('WARNINGLINE'):112;
        $open_val = C('OPENLINE')?C('OPENLINE'):108;
        $info['all_funds'] = $info['own_funds']+$info['with_funds'];//操盘资金
        $info['warning_line'] = round(intval($info['with_funds'])*$warning_val/100,2);
        $info['open_line'] = round(intval($info['with_funds'])*$open_val/100,2);
        $trader = M('TraderChart')->where(['nid'=>$id])->select();
        if($info['status'] == 8) {
            if (is_array($trader) and !empty($trader)) {
                foreach ($trader as $k => $v) {
                    $total_assets[] = $v['total_assets'] / 10000;
                    $pre_value[] = $v['pre_value'] / 10000;
                    $node[] = '"' . time_format($v['node'], 'm-d') . '"';
                }
                $prev_trader = array_pop($trader);
                $this->prev_pre_value = $prev_trader['pre_value'];
                $this->prev_total_assets = $prev_trader['total_assets'];
            }
        }
        $this->node = implode(',',$node);
        $this->total_assets = implode(',',$total_assets);
        $this->pre_value = implode(',',$pre_value);
        $this->safe = $info['open_line'];
        $this->info = $info;
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('chartweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 账户信息
     */
    public function information(){
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
            $id or ajaxMsg('请选择操作项',false);
            $orders or ajaxMsg('无效单号',false);
            $paypwd or ajaxMsg('请填写支付密码',false);
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                ajaxMsg('无效单号');
            }
            if(!$this->_checkHasPwd()){
                ajaxMsg('请先设置支付密码',false);
            }
            if($this->_checkPwd($paypwd,true)){
                $accounts = D('NeedsAccountView')->where(['nid'=>$id,'status'=>1])->find();
                ajaxMsg(think_decrypt($accounts['password']));
            }else{
                ajaxMsg('支付密码错误',false);
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $this->info = M('MainNeeds')->find($id);
            $this->accounts = D('NeedsAccountView')->where(['nid'=>$id,'status'=>1])->find();
            $this->display();
        }
    }

    /**
     * 取消
     */
    public function cancel(){
        if(IS_POST){
            list(
                $id,
                $orders,
                $reason,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.orders',''),
                I('post.reason',''),
                I('post.paypwd','')
            ];
            $orders or $this->error('无效单号');
            $paypwd or $this->error('请填写支付密码');
            $info = M('MainNeeds')->find($id);
            if($info['orders'] !=$orders){
                $this->error('无效单号');
            }
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                if($this->_checkNeedsStatus($id,[0,1])){
                    //调整状态到结束
                    M('MainNeeds')->where(['id'=>$id])->save(['status'=>-2,'operator'=>USERNAME,'operate_time'=>NOW_TIME,'reason'=>$reason]);
                    //站内信
                    $title = '您成功取消了配资需求';
                    $user_con = time_format().'您成功取消了配资需求，单号：'.$orders;
                    $admin_con = time_format().'用户取消了配资需求，用户名：'.USERNAME.'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
                    D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                    $this->success('操作成功');
                }else{
                    $this->error('需求通过审核，无法取消');
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or ajaxMsg('请选择操作项',false);
            $info = M('MainNeeds')->find($id);
            $this->id = $id;
            $this->orders = $info['orders'];
            $lxInfo = M('MainRepayment')->where(['uid'=>UID,'orders'=>$info['orders'],'status'=>0])->order('time_repayment')->find();
            $this->lx = money($lxInfo['money']);
            $this->paytime = time_format($lxInfo['time_repayment'],'Y-m-d');
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = money($balance);
            if($lxInfo['money'] > $balance){
                $this->amount = round($lxInfo['money']-$balance,2);
                $this->recharge = true;
            }

            $this->display();
        }
    }

    /**
     * 查看合同-配资
     */
    public function pact(){
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        //需求信息
        $needs = M('MainNeeds')->where(['orders'=>$orders])->find();
        empty($needs) and $this->error('无效单号');
        //$needs['li'] = $needs['with_funds']*10000*interest_rate/100;
        //投标信息
        $tenderInfo = M('MainTender')->where(['orders'=>$orders])->select();
        if(!empty($tenderInfo)){
            foreach($tenderInfo as $k => $v){
                $tenderInfo[$k]['lixi'] = $lixi = $v['money'] * $needs['profit']/100/12;
                $alllixi = $lixi * $needs['time_limit'];
                $tenderInfo[$k]['benxi'] = $v['money'] + $alllixi;
            }
        }
        $this->tenderInfo = $tenderInfo;

//        if(count($tenderInfo) > 1){
//            $this->tenderInfo = $tenderInfo;
//            $this->tenderInfoArr = true;
//        }else{
//            $this->tenderInfo = $tenderInfo[0];
//        }
        //配资方用户信息
        $peiziInfo = M('UcenterMember')->find(UID);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>UID])->getField('card_number');
        //股票账户
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$needs['id']])->find();
        //还款信息
        $this->repayments = M('MainRepayment')->where(['orders'=>$orders,'uid'=>UID])->select();
        //p($this->repayments,0);
        $this->info = $needs;
        $this->peiziInfo = $peiziInfo;
        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }

    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
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
     * 验证需求状态
     * @return boolean     检测结果
     */
    private function _checkNeedsStatus($nid,$stu) {
        $info = M('MainNeeds')->find($nid);
        if(is_array($stu)){
            if(in_array($info['status'],$stu))
                return true;
        }else{
            if($info['status'] == $stu)
                return true;
        }
        return false;
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
