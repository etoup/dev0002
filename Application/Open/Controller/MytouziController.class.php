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
Class MytouziController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    public function touzilists(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $bids = M('MainTender')->where(['uid'=>$uid])->order('created_time desc')->page($pages.',5')->getField('bid',true);
                if(!empty($bids)){
                    $list = M('MainBids')->field('id,nid,orders,own_funds,with_funds,profit,time_limit,status,created_time')->where(['id'=>['in',$bids]])->order('created_time desc')->select();
                    foreach($list as $k => $v){
                        $needs = M('MainNeeds')->field('id,types,num,own_funds,with_funds,orders')->find($v['nid']);
                        switch($needs['types']){
                            case 0:
                                $list[$k]['types_txt'] = '股票配资';
                                $list[$k]['guzhi'] = 0;
                                break;
                            case 1:
                                if($needs['num']){
                                    $list[$k]['types_txt'] = '股指配资';
                                    $list[$k]['num'] = $needs['num'];
                                    $list[$k]['guzhi'] = 1;
                                    $list[$k]['funds'] = intval($needs['own_funds']+$needs['with_funds']);
                                }
                                else{
                                    $list[$k]['types_txt'] = '期货配资';
                                    $list[$k]['guzhi'] = 0;
                                }
                                break;
                        }
                        $tender = M('MainTender')->where(['bid'=>$v['id']])->sum('money');
                        $list[$k]['speed'] = round($tender/($v['with_funds']*10000)*100,2);
                        $list[$k]['time'] = time_format($v['created_time']);
                        switch($v['status']){
                            case 0:
                                $list[$k]['status_txt'] = '投标中';
                                break;
                            case 1:
                                $list[$k]['status_txt'] = '投标成功';
                                break;
                            case -2:
                                $list[$k]['status_txt'] = '配资结束';
                                break;
                        }
                    }
                }
                $this->response($list,'json');
                break;
        }
    }

    public function touziinfo(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainBids')->field('id,nid,orders,own_funds,with_funds,profit,status,time_limit')->find($id);
                if(!empty($list)){
                    $needs = M('MainNeeds')->field('id,types,num')->find($list['nid']);
                    $list['types'] = $needs['types'];
                    $list['num'] = $needs['num'];
                    $list['id'] = intval($list['id']);
                    $list['income'] = 10000*$list['profit']/100/12;
                    $tender = M('MainTender')->where(['bid'=>$list['id']])->sum('money');
                    $list['speed'] = round($tender/($list['with_funds']*10000)*100,2);
                    $investor = M('MainTender')->where(['bid'=>$list['id']])->count();
                    $list['investor'] = $investor;
                    $tenderinfo = M('MainTender')->where(['bid'=>$list['id'],'uid'=>$uid])->find();
                    $list['tender_funds'] = $tenderinfo['money'];
                    $list['total_revenue'] = $tenderinfo['total_revenue'];
                    $list['needsstatus'] = M('MainNeeds')->where(['orders'=>$list['orders']])->getField('status');
                }
                $this->response($list,'json');
                break;
        }
    }

    public function invest(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $orders = $_POST['orders']?$_POST['orders']:'';
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
                    $list['balance'] = money($userinfo['balance']);
                }
                if($orders){
                    $bidsinfo = M('MainBids')->where(['orders'=>$orders])->find();
                    $tender = M('MainTender')->where(['orders'=>$orders])->sum('money');
                    $pay_money = $bidsinfo['with_funds']*10000 - $tender;
                    $list['pay_money'] = money($pay_money);
                }
                $this->response($list,'json');
                break;
        }
    }
    public function investpay(){
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
                    $list['msg'] = '请填写投资金额';
                    $this->response($list,'json');
                }
                if(!is_numeric($money)){
                    $list['msg'] = '请正确填写投资金额';
                    $this->response($list,'json');
                }
                if($money < 1000){
                    $list['msg'] = '投资金额最少1000元';
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
                    $bidlist = M('MainBids')->where(['orders'=>$orders])->find();
                    $tender = M('MainTender')->where(['bid'=>$bidlist['id']])->sum('money');
                    $surplus = ($bidlist['with_funds']*10000) - $tender;
                    //资金记录-利息
                    D('MainFunds')->addData(-$money,$uid,24);
                    //处理投资日志
                    $tid = M('MainTender')->where(['bid'=>$bidlist['id'],'uid'=>$uid])->getField('id');
                    if($tid)
                        //处理追加投资
                        api('Needs/doMainTender',[$bidlist,$money,$tid]);
                    else
                        api('Needs/doMainTender',[$bidlist,$money]);
                    //站内信
                    $title = '您成功完成一笔投资';
                    $user_con = time_format().'您成功完成一笔投资，金额：'.money($money).'，单号：'.$bidlist['orders'];
                    $admin_con = time_format().'用户完成投资，用户名：'.$userinfo['username'].'，金额：'.money($money).'，单号：'.$bidlist['orders'].'，<a href="'.C('HTTPHOST').'/admin.php/Tender/index">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                    //处理自动满标
                    if($surplus == $money){
                        M('MainNeeds')->where(['orders'=>$bidlist['orders']])->setField('status',3);
                        M('MainBids')->save(['id'=>$bidlist['id'],'status'=>1,'update_time'=>NOW_TIME]);
                        //站内信
                        $admin_con = time_format().'需求满标，及时绑定股票账户，单号：'.$bidlist['orders'].'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
                        D('MessageNotices')->saveData(1,'','',1,$admin_con);
                    }
                }else{
                    $list['msg'] = '支付密码填写错误';
                    $this->response($list,'json');
                }
                $list['money'] = money($money);
                $this->response($list,'json');
                break;
        }
    }

    public function backfunds(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $orders = $_POST['orders']?$_POST['orders']:'';
                if($uid && $orders){
                    $list = M('MainRepayment')->where(['uid'=>$uid,'orders'=>$orders,'types'=>['in',[0,1]]])->select();
                    if($list){
                        foreach($list as $k => $v){
                            $list[$k]['repaymentData'] = time_format($v['time_repayment'],'Y-m-d');
                            switch($v['status']){
                                case 0:
                                    $list[$k]['statusTxt'] = '待收';
                                    break;
                                case 1:
                                    $list[$k]['statusTxt'] = '已收';
                                    break;
                                case -1:
                                    $list[$k]['statusTxt'] = '回收';
                                    break;
                            }
                        }
                    }
                }
                $this->response($list,'json');
                break;
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