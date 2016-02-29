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
Class MyfundsController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    public function index(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $list = M('MainRepayment')->where(['uid'=>$uid])->order('time_repayment')->select();
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
                        $cn_info = M('MainRepayment')->where(['uid'=>$uid,'types'=>0,'status'=>0])->order('time_repayment')->find();
                    if($cr_norepay)
                        $cr_info = M('MainRepayment')->where(['uid'=>$uid,'types'=>-1,'status'=>0])->order('time_repayment')->find();

                    $data = [
                        'cn_time' => time_format($cn_info['time_repayment'],'Y-m-d'),
                        'cn_money' => money($cn_info['money'],'%s'),
                        'cn_total' => $cn_total,
                        'cn_receipt' => $cn_receipt,
                        'cn_never' => $cn_never,
                        'cr_time' => time_format($cr_info['time_repayment'],'Y-m-d'),
                        'cr_money' => money($cr_info['money'],'%s'),
                        'cr_total' => $cr_total,
                        'cr_repay' => $cr_repay,
                        'cr_norepay' => $cr_norepay
                    ];
                    $this->response($data,'json');
                }
                $data['msg'] = '没有找到数据';
                $this->response($data,'json');
                break;
        }
    }

    public function repayment() {
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $statusTxt = $_POST['statusTxt']?$_POST['statusTxt']:'';
                switch($statusTxt){
                    case 'yes':
                        $map = [
                            'uid' => $uid,
                            'types'=>-1,
                            'status'=>1
                        ];
                        break;
                    case 'no':
                        $map = [
                            'uid' => $uid,
                            'types'=>-1,
                            'status'=>0
                        ];
                        break;
                    default:
                        $map = [
                            'uid' => $uid,
                            'types'=>-1
                        ];
                }
                $list = M('MainRepayment')->where($map)->field('id,orders,money,status,time_repayment')->order('status,created_time desc')->page($pages.',10')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){

                        $list[$k]['money'] = money($v['money']);
                        switch($v['status']){
                            case 0:
                                $list[$k]['statusTxt'] = '待付';
                                $list[$k]['datetime'] = prevDate($v['time_repayment'],'Y-m-d');
                                $list[$k]['pay'] = true;
                                break;
                            default:
                                $list[$k]['statusTxt'] = '已付';
                                $list[$k]['datetime'] = time_format($v['time_repayment'],'Y-m-d');
                        }
                    }
                }
                $this->response($list,'json');
                break;
        }
    }
    public function collections() {
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $statusTxt = $_POST['statusTxt']?$_POST['statusTxt']:'';
                switch($statusTxt){
                    case 'yes':
                        $map = [
                            'uid' => $uid,
                            'types'=>['in',[0,1]],
                            'status'=>1
                        ];
                        break;
                    case 'no':
                        $map = [
                            'uid' => $uid,
                            'types'=>['in',[0,1]],
                            'status'=>0
                        ];
                        break;
                    default:
                        $map = [
                            'uid' => $uid,
                            'types'=>['in',[0,1]]
                        ];
                }
                $list = M('MainRepayment')->where($map)->field('id,orders,money,status,types,time_repayment')->order('status,created_time desc')->page($pages.',10')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $list[$k]['datetime'] = time_format($v['time_repayment'],'Y-m-d');
                        $list[$k]['money'] = money($v['money']);
                        switch($v['status']){
                            case 0:
                                $list[$k]['statusTxt'] = '待收';
                                break;
                            default:
                                $list[$k]['statusTxt'] = '已收';

                        }
                        switch($v['types']){
                            case 1:
                                $list[$k]['remark'] = '本金回款';
                                break;
                            default:
                                $list[$k]['remark'] = '投资收益';

                        }
                    }
                }
                $this->response($list,'json');
                break;
        }
    }

    public function dowithdrawals(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $money = $_POST['money']?$_POST['money']:0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $cid = $_POST['cid']?intval($_POST['cid']):0;
                if(!$uid){
                    $list['msg'] = '请先登录';
                    $this->response($list,'json');
                }
                if(!$money){
                    $list['msg'] = '请填写转出金额';
                    $this->response($list,'json');
                }
                if(!is_numeric($money)){
                    $list['msg'] = '请正确填写转出金额';
                    $this->response($list,'json');
                }
                if(!$paypwd){
                    $list['msg'] = '请填写支付密码';
                    $this->response($list,'json');
                }
                if($diff = $this->_checkMoney($uid,$money)){
                    $list['msg'] = '可用余额不足';
                    $this->response($list,'json');
                }
                if(!$this->_checkHasPwd($uid)){
                    $list['msg'] = '请先设置支付密码';
                    $this->response($list,'json');
                }
                if($this->_checkPwd($uid,$paypwd,true)){
                    $userinfo = M('UcenterMember')->field('id,balance,username')->find($uid);
                    $data = [
                        'uid'=>$uid,
                        'cid'=>$cid,
                        'username'=>$userinfo['username'],
                        'operation_amount'=>$money,
                        'amount'=>$money,
                        'types'=>1,
                        'created_time'=>NOW_TIME,
                        'created_ip'=>get_client_ip(),
                        'remark'=>'成功提交申请，等待审核'
                    ];
                    $back = M('MainDraw')->add($data);
                    if($back){
                        //操作资金日志
                        D('MainFunds')->addData(-$money,$uid,2,'',$back);
                        //站内信
                        $title = '您提交了提现申请';
                        $user_con = time_format().'您提交了提现申请，请等待审核，金额：'.money($money);
                        $admin_con = time_format().'用户提交了提现申请，用户名：'.$userinfo['username'].'，金额：'.money($money).'，<a href="'.C('HTTPHOST').'/admin.php/Draw/index">立即查看</a>';
                        D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
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

    public function withdrawalslogs(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $list = M('MainDraw')->where(['uid'=>$uid,'types'=>1])->field('id,cid,operation_amount,status,created_time')->order('created_time desc')->page($pages.',10')->select();
                $data = [];
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $cardinfo = M('MyCard')->find($v['cid']);
                        $data[$k]['remark'] = $cardinfo['bank_name'].'，尾号：'.substr($cardinfo['card_number'],-4);
                        $data[$k]['money'] = money($v['operation_amount']);
                        $data[$k]['datetime'] = time_format($v['created_time']);
                        switch($v['status']){
                            case 0:
                                $data[$k]['statusTxt'] = '待审';
                                break;
                            case 1:
                                $data[$k]['statusTxt'] = '已审';
                                break;
                        }
                    }
                }
                $this->response($data,'json');
                break;
        }
    }

    public function rechargelogs(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $list = M('MainRecord')->where(['uid'=>$uid])->field('id,billno,operation_amount,methods,status,created_time')->order('created_time desc')->page($pages.',10')->select();
                $data = [];
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $data[$k]['billno'] = $v['billno'];
                        $data[$k]['money'] = money($v['operation_amount']);
                        $data[$k]['datetime'] = time_format($v['created_time']);
                        switch($v['methods']){
                            case 0:
                                $data[$k]['methodsTxt'] = '在线充值';
                                break;
                            case 1:
                                $data[$k]['methodsTxt'] = '银行汇款';
                                break;
                        }
                        switch($v['status']){
                            case 0:
                                $data[$k]['statusTxt'] = $v['methods']?'待审':'失败';
                                break;
                            case 1:
                                $data[$k]['statusTxt'] = '已审';
                                break;
                        }
                    }
                }
                $this->response($data,'json');
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