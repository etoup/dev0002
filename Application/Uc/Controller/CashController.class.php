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
 * 提现管理控制器
 */

class CashController extends UcenterController {

	/**
	 * 提现管理
	 */
	public function index() {
        $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
        if(IS_POST){
            list(
                $cid,
                $money,
                $paypwd
                ) = [
                I('post.cid',0,'int'),
                I('post.money',0,'abs'),
                I('post.paypwd','')
            ];
            $cid or ajaxMsg('请选择银行卡',false);
            $money or ajaxMsg('请填写提现金额',false);
            is_numeric($money) or ajaxMsg('提现金额只能是数字',false);
            ($money >= 100) or ajaxMsg('提现金额必须大于等于100',false);
            $paypwd or ajaxMsg('请填写支付密码',false);
            if($diff = $this->_checkMoney($money)){
                $backUrl = U('Uc/Recharge/index');
                $back = array('message'=>['余额不足，请充值'],'referer' => $backUrl);
                ajaxMsg($back);
            }
            if(!$this->_checkHasPwd()){
                $backUrl = U('Uc/Password/paypwd');
                $back = array('message'=>['请先设置支付密码'],'referer' => $backUrl);
                ajaxMsg($back);
            }
            if($this->_checkPwd($paypwd,true)){
                $data = [
                    'uid'=>UID,
                    'cid'=>$cid,
                    'username'=>USERNAME,
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
                    D('MainFunds')->addData(-$money,2,'',$back);
                    //站内信
                    $title = '您提交了提现申请';
                    $user_con = time_format().'您提交了提现申请，请等待审核，金额：'.money($money);
                    $admin_con = time_format().'用户提交了提现申请，用户名：'.USERNAME.'，金额：'.money($money).'，<a href="'.C('HTTPHOST').'/admin.php/Draw/index">立即查看</a>';
                    D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                }
                $okback = array('message'=>['申请成功，等待审核'],'referer' => U('Uc/Cash/log'));
                ajaxMsg($okback);
            }else{
                ajaxMsg('支付密码错误',false);
            }
        }else{
            $this->balance = $balance;
            $myCard = D('MyCardView')->where(['uid'=>UID,'status'=>1])->select();
            if(empty($myCard)){
                $this->error('请先添加银行卡',U('Card/index'));
            }else{
                $this->myCard = $myCard;
                $this->realname = $myCard[0]['realname'];
            }
            $this->seo = ['title' => '我要提现'];
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('indexweixin');
                    break;
                default:
                    $this->display();
            }
        }
	}

    /**
     * 提现日志
     */
    public function log(){
        $map = ['uid'=>UID];
        $list = $this->lists('MainDrawView',$map,'',true,1);
        int_to_string($list);
        $this->_list = $list;
        $this->seo = ['title' => '提现记录'];
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('logweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 出金日志
     */
    public function shrank(){
        $map = ['uid'=>UID,'types'=>0];
        $list = $this->lists('MainDraw',$map);
        int_to_string($list);
        $this->_list = $list;
        $this->seo = ['title' => '出金记录'];
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('shrankweixin');
                break;
            default:
                $this->display();
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
