<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Open\Controller;
use User\Api\UserApi;
use Think\Controller\RestController;
Class TouziController extends RestController {

    public function touzipay(){
        switch ($this->_method){
            case 'get': // get请求处理代码
                $uid = $_GET['uid']?intval($_GET['uid']):0;
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
                    $bid = $_GET['bid']?intval($_GET['bid']):0;
                    $tender = M('MainTender')->where(['bid'=>$bid])->sum('money');
                    $with_funds = M('MainBids')->where(['id'=>$bid])->getField('with_funds');
                    $money = ($with_funds*10000) - $tender;
                    $list['money'] = $money;
                    $list['moneyTxt'] = '投资金额(元) 1000-'.$money;
                    $list['balance'] = money($userinfo['balance']);
                }
                $this->response($list,'json');
                break;
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
                    $bid = $_POST['bid']?intval($_POST['bid']):0;
                    $tender = M('MainTender')->where(['bid'=>$bid])->sum('money');
                    $with_funds = M('MainBids')->where(['id'=>$bid])->getField('with_funds');
                    $money = ($with_funds*10000) - $tender;
                    $list['money'] = $money;
                    $list['moneyTxt'] = '投资金额(元) 1000-'.$money;
                    $list['balance'] = money($userinfo['balance']);
                }
                $this->response($list,'json');
                break;
        }
    }

    public function dopay(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $bid = $_POST['bid']?intval($_POST['bid']):0;
                $money = $_POST['money']?intval($_POST['money']):0;
                $paypwd = $_POST['paypwd']?intval($_POST['paypwd']):'';
                if(empty($uid)){
                    $list = [
                        'status'=> 1,
                        'msg'=>'请先登录后操作'
                    ];
                    $this->response($list,'json');
                }
                if(empty($bid)){
                    $list = [
                        'status'=> 2,
                        'msg'=>'没有对应的标的'
                    ];
                    $this->response($list,'json');
                }
                $bidlist = M('MainBids')->find($bid);
                if($uid == $bidlist['uid']){
                    $list = [
                        'status'=> 3,
                        'msg'=>'请选投他人标的'
                    ];
                    $this->response($list,'json');
                }
                if(empty($money)){
                    $list = [
                        'status'=> 4,
                        'msg'=>'请填写投资金额'
                    ];
                    $this->response($list,'json');
                }
                if(empty($paypwd)){
                    $list = [
                        'status'=> 5,
                        'msg'=>'请填写支付密码'
                    ];
                    $this->response($list,'json');
                }
                if(!is_numeric($money)){
                    $list = [
                        'status'=> 6,
                        'msg'=>'投资金额必须为数字'
                    ];
                    $this->response($list,'json');
                }
                if($money < 1000){
                    $list = [
                        'status'=> 7,
                        'msg'=>'最小投资金额为1000'
                    ];
                    $this->response($list,'json');
                }

                $userinfo = M('UcenterMember')->field('id,username,balance')->find($uid);
                if(!empty($userinfo)){
                    $tender = M('MainTender')->where(['bid'=>$bid])->sum('money');
                    $with_funds = M('MainBids')->where(['id'=>$bid])->getField('with_funds');
                    $surplus = ($with_funds*10000) - $tender;
                    if($money > $surplus){
                        $list = [
                            'status'=> 8,
                            'msg'=>'满标或超出可投金额'
                        ];
                        $this->response($list,'json');
                    }
                    if($money > $userinfo['balance']){
                        $list = [
                            'status'=> 9,
                            'msg'=>'账户可用余额不足'
                        ];
                        $this->response($list,'json');
                    }
                }
                if(!$this->_checkPwd($uid,$paypwd,true)){
                    $list = [
                        'status'=> 10,
                        'msg'=>'请正确填写支付密码'
                    ];
                    $this->response($list,'json');
                }
                //处理资金日志
                D('MainFunds')->addData(-$money,$uid,24);
                //处理投资日志
                $tid = M('MainTender')->where(['bid'=>$bid,'uid'=>$uid])->getField('id');
                if($tid)
                    //处理追加投资
                    api('Needs/doAppMainTender',[$uid,$userinfo['username'],$bidlist,$money,$tid]);
                else
                    api('Needs/doAppMainTender',[$uid,$userinfo['username'],$bidlist,$money]);
                //站内信
                $title = '您成功完成一笔投资';
                $user_con = time_format().'您成功完成一笔投资，金额：'.money($money).'，单号：'.$bidlist['orders'];
                $admin_con = time_format().'用户完成投资，用户名：'.$userinfo['username'].'，金额：'.money($money).'，单号：'.$bidlist['orders'].'，<a href="'.C('HTTPHOST').'/admin.php/Tender/index">立即查看</a>';
                D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                //处理自动满标
                if($surplus == $money){
                    M('MainNeeds')->where(['orders'=>$bidlist['orders']])->setField('status',3);
                    M('MainBids')->save(['id'=>$bid,'status'=>1,'update_time'=>NOW_TIME]);
                    //站内信
                    $admin_con = time_format().'需求满标，及时绑定股票账户，单号：'.$bidlist['orders'].'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
                    D('MessageNotices')->saveData(1,'','',1,$admin_con);
                }
                $list = [
                    'status'=> 0,
                    'money'=>money($money),
                    'msg'=>'投资成功'
                ];
                $this->response($list,'json');
                break;
        }
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