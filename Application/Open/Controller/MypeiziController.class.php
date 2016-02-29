<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Open\Controller;
use Think\Controller\RestController;
use User\Api\UserApi;
Class MypeiziController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    public function peizilists(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $list = M('MainNeeds')->field('id,orders,own_funds,with_funds,scale,interest_rate,time_limit,status,types,num,created_time')->where(['uid'=>$uid])->order(' created_time desc,num desc, types desc')->page($pages.',5')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $bidInfo = M('MainBids')->where(['orders'=>$v['orders']])->find();
                        $tender = M('MainTender')->where(['bid'=>$bidInfo['id']])->sum('money');
                        $list[$k]['speed'] = round($tender/($v['with_funds']*10000)*100,2);
                        $list[$k]['time'] = time_format($v['created_time']);
                        switch($v['status']){
                            case 0:
                                $list[$k]['status_txt'] = '等待审核';
                                break;
                            case 1:
                                $list[$k]['status_txt'] = '通过审核';
                                break;
                            case 2:
                                $list[$k]['status_txt'] = '配资中';
                                break;
                            case 3:
                                $list[$k]['status_txt'] = '待绑定账户';
                                break;
                            case 8:
                                $list[$k]['status_txt'] = '配资成功';
                                break;
                            case -1:
                                $list[$k]['status_txt'] = '未通过审核';
                                break;
                            case -2:
                                $list[$k]['status_txt'] = '配资结束';
                                break;
                        }

                        switch($v['types']){
                            case 0:
                                $list[$k]['types_txt'] = '股票配资';
                                $list[$k]['guzhi'] = 0;
                                break;
                            case 1:
                                if($v['num']){
                                    $list[$k]['types_txt'] = '期货配资';
                                    $list[$k]['guzhi'] = 1;
                                    $list[$k]['funds'] = intval($list[$k]['own_funds']+$list[$k]['with_funds']);
                                }
                                else{
                                    $list[$k]['types_txt'] = '期货配资';
                                    $list[$k]['guzhi'] = 0;
                                }
                                break;
                            case 2:
                                if($v['num']){
                                    $list[$k]['types_txt'] = '期货配资';
                                    $list[$k]['guzhi'] = 2;
                                    $list[$k]['funds'] = intval($list[$k]['own_funds']+$list[$k]['with_funds']);
                                }
                                else{
                                    $list[$k]['types_txt'] = '期货配资';
                                    $list[$k]['guzhi'] = 0;
                                }
                                break;
                        }

                        $delay = M('MainNeedsdata')->where(['nid'=>$bidInfo['nid'],'types'=>0])->order('created_time desc')->find();
                        if($delay){
                            $list[$k]['delay'] = 'block';
                            $list[$k]['delay_datetime'] = time_format($delay['created_time']);
                            $list[$k]['delay_remark'] = '您提交了'.$delay['delay_time'].'个月的延期申请，等待管理员审核';
                        }else{
                            $list[$k]['delay'] = 'none';
                            $list[$k]['delay_datetime'] = '';
                            $list[$k]['delay_remark'] = '';
                        }

                        $end = M('MainNeedsdata')->where(['nid'=>$bidInfo['nid'],'types'=>1])->order('created_time desc')->find();
                        if($end){
                            $list[$k]['end'] = 'block';
                            $list[$k]['end_datetime'] = time_format($end['created_time']);
                            $list[$k]['end_remark'] = '您提交了结束申请，等待管理员审核';
                        }else{
                            $list[$k]['end'] = 'none';
                            $list[$k]['end_datetime'] = '';
                            $list[$k]['end_remark'] = '';
                        }
                    }
                }
                $this->response($list,'json');
                break;
        }
    }

    public function peiziinfo(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainNeeds')->field('id,uid,orders,own_funds,with_funds,profit,interest_rate,status,types,num,time_limit,end_trading')->find($id);
                if(!empty($list)){
                    $list['id'] = intval($list['id']);
                    $list['funds'] = intval($list['own_funds'] + $list['with_funds']);
                    $list['income'] = 10000*$list['interest_rate']/100;
                    $tender = M('MainTender')->where(['orders'=>$list['orders']])->sum('money');
                    $list['speed'] = round($tender/($list['with_funds']*10000)*100,2);
                    $investor = M('MainTender')->where(['orders'=>$list['orders']])->count();
                    $list['investor'] = $investor;
                    $list['end_time'] = prevDate($list['end_trading']);
                    $bidlinfo = M('MainBids')->where(['nid'=>$id])->field('id,nid,orders')->find();
                    if($bidlinfo){
                        $list['bid'] = $bidlinfo['id'];
                    }
                }
                if($list['orders']){
                    $repayment = M('MainRepayment')->where(['uid'=>$list['uid'],'orders'=>$list['orders'],'status'=>0])->order('time_repayment')->find();
                    if($repayment){
                        $list['pay_time'] = prevDate($repayment['time_repayment']);
                    }else{
                        $list['nopay'] = true;
                    }
                }
                $delay = M('MainNeedsdata')->where(['nid'=>$list['id'],'types'=>0])->order('created_time desc')->find();
                if($delay){
                    $list['delayinfo'] = true;
                    $list['delay_datetime'] = time_format($delay['created_time']);
                    $list['delay_remark'] = '您提交了'.$delay['delay_time'].'个月的延期申请，等待管理员审核';
                }
                $end = M('MainNeedsdata')->where(['nid'=>$list['id'],'types'=>1])->order('created_time desc')->find();
                if($end){
                    $list['endinfo'] = true;
                    $list['end_datetime'] = time_format($end['created_time']);
                    $list['end_remark'] = '您提交了结束申请，等待管理员审核';
                }

                $this->response($list,'json');
                break;
        }
    }

    public function doint(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $orders = $_POST['orders']?$_POST['orders']:'';
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
                    $list['balance'] = money($userinfo['balance']);
                }
                if($orders){
                    $repayment = M('MainRepayment')->where(['uid'=>$uid,'orders'=>$orders,'status'=>0])->order('time_repayment')->find();
                    if($pass_date = $this->get_pass_date($repayment['time_repayment'])){
                        $info = M('MainNeeds')->where(['orders'=>$orders])->find();
                        $principal = $info['with_funds']*10000+$repayment['money'];
                        $pass_money = $this->get_pass_money($repayment['time_repayment'],$principal);
                        $list['pass_date'] = $pass_date;
                        $list['pass_money'] = money($pass_money);
                        $pay_money = money($repayment['money']+$pass_money);
                        $list['pay_money'] = $pay_money;
                    }else{
                        $pay_money = money($repayment['money']);
                        $list['pay_money'] = $pay_money;
                    }
                }
                $this->response($list,'json');
                break;
        }
    }
    public function dointpay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $orders = $_POST['orders']?$_POST['orders']:'';
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if(!$orders){
                    $list['msg'] = '请选择需要操作的订单';
                    $this->response($list,'json');
                }
                $info = M('MainNeeds')->where(['orders'=>$orders])->find();
                $repayment = M('MainRepayment')->where(['uid'=>$uid,'orders'=>$orders,'status'=>0])->order('time_repayment')->find();
                if($pass_date = $this->get_pass_date($repayment['time_repayment'])){
                    $principal = $info['with_funds']*10000+$repayment['money'];
                    $pass_money = $this->get_pass_money($repayment['time_repayment'],$principal);
                    $money = $repayment['money'] + $pass_money;
                    $interest = $money;//利息+罚金

                }else{
                    $interest = $repayment['money'];//利息
                }

                if($diff = $this->_checkMoney($uid,$interest)){
                    $list['msg'] = '余额不足，请充值';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $userinfo = M('UcenterMember')->field('id,username,balance')->find($uid);
                    //是否逾期
                    if($pass_date){
                        //资金记录-利息
                        D('MainFunds')->addData(-$repayment['money'],$uid,23);
                        //资金记录-逾期罚金
                        D('MainFunds')->addData(-$pass_money,$uid,29);
                        //逾期罚金站内信
                        $title = '您成功支付了逾期罚息';
                        $user_con = time_format().'您成功支付了一笔逾期罚息，逾期天数：'.$pass_date.'天，罚息金额：'.money($pass_money).'，单号：'.$orders;
                        D('MessageNotices')->saveData($uid,$title,$user_con);
                        //站内信
                        $title = '您成功支付了一笔利息';
                        $user_con = time_format().'您成功支付了一笔利息，金额：'.money($repayment['money']).'，单号：'.$orders;
                        $admin_con = time_format().'用户成功支付利息，用户名：'.$userinfo['username'].'，金额：'.money($repayment['money']).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Repayment/index">立即查看</a>';
                        D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                    }else{
                        D('MainFunds')->addData(-$interest,$uid,23);
                        //站内信
                        $title = '您成功支付了一笔利息';
                        $user_con = time_format().'您成功支付了一笔利息，金额：'.money($interest).'，单号：'.$orders;
                        $admin_con = time_format().'用户成功支付利息，用户名：'.$userinfo['username'].'，金额：'.money($interest).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Repayment/index">立即查看</a>';
                        D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                    }
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                //修改预警状态
                $earlyMsg = M('MessageNotices')->where(['param'=>$repayment['id']])->find();
                if(!empty($earlyMsg)){
                    M('MessageNotices')->save(['id'=>$earlyMsg['id'],'early'=>0]);
                }
                //调整状态到已付
                M('MainRepayment')->where(['id'=>$repayment['id']])->save(['status'=>1,'time_do_repaymen'=>NOW_TIME]);
                //业务提成
                api('Needs/doStatisticsTake',[$info]);
                $list['pay_money'] = money($interest);
                $this->response($list,'json');
                break;
        }
    }

    public function dobond(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
                    $list['balance'] = money($userinfo['balance']);
                }
                $this->response($list,'json');
                break;
        }
    }
    public function dobondpay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $money = $_POST['money']?$_POST['money']:0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $orders = $_POST['orders']?$_POST['orders']:'';
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$money){
                    $list['msg'] = '请填写支付金额';
                    $this->response($list,'json');
                }
                if(!is_numeric($money)){
                    $list['msg'] = '请正确填写支付金额';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if(!$orders){
                    $list['msg'] = '请选择需要操作的订单';
                    $this->response($list,'json');
                }
                if($diff = $this->_checkMoney($uid,$money)){
                    $list['msg'] = '余额不足，请充值';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $userinfo = M('UcenterMember')->field('id,balance,username')->find($uid);
                    //资金记录-利息
                    D('MainFunds')->addData(-$money,$uid,22);
                    //记录保证金日志
                    M('MainBond')->add(['orders'=>$orders,'uid'=>$uid,'money'=>$money,'types'=>1,'status'=>1,'created_time'=>NOW_TIME]);
                    //站内信
                    $title = '您成功支付了一笔保证金';
                    $user_con = time_format().'您成功支付了一笔保证金，金额：'.money($money).'，单号：'.$orders;
                    $admin_con = time_format().'用户成功支付保证金，用户名：'.$userinfo['username'].'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Bond/index">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                $list['money'] = money($money);
                $this->response($list,'json');
                break;
        }
    }

    public function dodelay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $orders = $_POST['orders']?$_POST['orders']:'';
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
                    $list['balance'] = money($userinfo['balance']);
                }
                if(!empty($orders)){
                    $needsinfo = M('MainNeeds')->where(['orders'=>$orders])->find();
                    $delayfee = C('DELAYFEE')?C('DELAYFEE'):0.1;
                    $list['pay_money'] = money(round($needsinfo['with_funds']*10000*$delayfee/100,2));
                }
                $this->response($list,'json');
                break;
        }
    }
    public function dodelaypay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $delay_time = $_POST['delay_time']?intval($_POST['delay_time']):0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $orders = $_POST['orders']?$_POST['orders']:'';
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$delay_time){
                    $list['msg'] = '请填写延期时间';
                    $this->response($list,'json');
                }
                if(!is_numeric($delay_time)){
                    $list['msg'] = '请正确填写延期时间';
                    $this->response($list,'json');
                }
                if($delay_time > 12){
                    $list['msg'] = '最长延期12个月';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if(!$orders){
                    $list['msg'] = '请选择需要操作的订单';
                    $this->response($list,'json');
                }
                $needsinfo = M('MainNeeds')->where(['orders'=>$orders])->find();
                $delayfee = C('DELAYFEE')?C('DELAYFEE'):0.1;
                $delay = round($needsinfo['with_funds']*10000*$delayfee/100,2);
                if($diff = $this->_checkMoney($uid,$delay)){
                    $list['msg'] = '余额不足，请充值';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $userinfo = M('UcenterMember')->field('id,balance,username')->find($uid);
                    //记录操作日志
                    $data = [
                        'uid'=>$uid,
                        'nid'=>$needsinfo['id'],
                        'delay_time'=>$delay_time,
                        'money'=>$delay,
                        'created_time'=>NOW_TIME,
                        'remark'=>'成功提交延期申请，等待审核'
                    ];
                    M('MainNeedsdata')->add($data);
                    //资金记录-利息
                    D('MainFunds')->addData(-$delay,$uid,30);
                    //站内信
                    $title = '您成功提交延期申请';
                    $user_con = time_format().'您成功提交延期申请，等待审核，单号：'.$orders;
                    $admin_con = time_format().'用户提交延期申请，用户名：'.$userinfo['username'].'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/delay">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                $list['delay_time'] = $delay_time;
                $this->response($list,'json');
                break;
        }
    }

    public function doinfo(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $orders = $_POST['orders']?$_POST['orders']:'';
                $info = M('MainNeeds')->where(['orders'=>$orders])->find();
                $accounts = D('NeedsAccountView')->where(['nid'=>$info['id'],'status'=>1])->find();
                $list = [
                    'broker'=>$accounts['broker'],
                    'account'=>$accounts['account']
                ];
                $this->response($list,'json');
                break;
        }
    }
    public function doinfopay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $orders = $_POST['orders']?$_POST['orders']:'';
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if(!$orders){
                    $list['msg'] = '请选择需要操作的订单';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $info = M('MainNeeds')->where(['orders'=>$orders])->find();
                    $accounts = D('NeedsAccountView')->where(['nid'=>$info['id'],'status'=>1])->find();
                    $list = [
                        'password'=>think_decrypt($accounts['password'])
                    ];
                    $this->response($list,'json');
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                break;
        }
    }

    public function dogoldpay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $cid = $_POST['cid']?intval($_POST['cid']):0;
                $money = $_POST['money']?$_POST['money']:0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $orders = $_POST['orders']?$_POST['orders']:'';
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$money){
                    $list['msg'] = '请填写出金金额';
                    $this->response($list,'json');
                }
                if(!is_numeric($money)){
                    $list['msg'] = '请正确填写出金金额';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if(!$orders){
                    $list['msg'] = '请选择需要操作的订单';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $userinfo = M('UcenterMember')->field('id,balance,username')->find($uid);
                    //记录出金日志
                    $data = [
                        'uid'=>$uid,
                        'orders'=>$orders,
                        'username'=>$userinfo['username'],
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
                        $admin_con = time_format().'用户提交账户出金 -> 提现申请，用户名：'.$userinfo['username'].'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Draw/index">立即查看</a>';
                    else
                        $admin_con = time_format().'用户提交账户出金申请，用户名：'.$userinfo['username'].'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Shrank/index">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                $list['money'] = money($money);
                $this->response($list,'json');
                break;
        }
    }

    public function doend(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $orders = $_POST['orders']?$_POST['orders']:'';
                $info = M('MainNeeds')->where(['orders'=>$orders])->find();
                $list = [
                    'endtime'=>'结束日期：'.prevDate($info['end_trading']),
                    'num'=>$info['num']
                ];
                $this->response($list,'json');
                break;
        }
    }

    public function doendpay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $cid = $_POST['cid']?intval($_POST['cid']):0;
                $money = $_POST['money']?$_POST['money']:0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $orders = $_POST['orders']?$_POST['orders']:'';
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$money){
                    $list['msg'] = '请填写股票账户总资产';
                    $this->response($list,'json');
                }
                if(!is_numeric($money)){
                    $list['msg'] = '请正确填写股票账户总资产';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if(!$orders){
                    $list['msg'] = '请选择需要操作的订单';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $userinfo = M('UcenterMember')->field('id,balance,username')->find($uid);
                    $needsinfo = M('MainNeeds')->where(['orders'=>$orders])->field('id,orders')->find();
                    //记录操作日志
                    $data = [
                        'uid'=>$uid,
                        'nid'=>$needsinfo['id'],
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
                    $admin_con = time_format().'用户提交结束申请，用户名：'.$userinfo['username'].'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/early">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                $list['money'] = money($money);
                $this->response($list,'json');
                break;
        }
    }

    public function mycards(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $info = M('MyCard')->where(['uid'=>$uid,'status'=>1])->select();
                $this->response($info,'json');
                break;
        }
    }
    public function getcard(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $cid = $_POST['cid']?intval($_POST['cid']):0;
                $info = M('MyCard')->find($cid);
                $card_number = substr($info['card_number'],-4);
                $selectTxt = $info['bank_name'].'，尾号：'.$card_number;
                $data = [
                    'selectTxt' => $selectTxt
                ];
                $this->response($data,'json');
                break;
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
     * 验证额度
     * @param  int $money 密码
     * @return boolean     检测结果
     */
    private function _checkMoney($uid,$money) {
        $balance = M('UcenterMember')->where(['id'=>$uid])->getField('balance');
        if($money > $balance){
            $difference = $money-$balance;
            return $difference;
        }else{
            return 0;
        }
    }

    /**
     * 验证是否设置支付密码
     * @return boolean     检测结果
     */
    private function _checkHasPwd($uid) {
        $info = M('UcenterMember')->find($uid);
        return $info['pass_paypwd'];
    }

    /**
     * 验证密码
     * @param  string $password 密码
     * @param  boolean $paypwd 是否验证支付密码
     * @return boolean     检测结果
     */
    private function _checkPwd($uid,$password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser($uid, $password,$paypwd);
    }
}