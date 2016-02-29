<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Index\Controller;
use User\Api\UserApi;
/**
 * 股指吧控制器
 *
 */

class GuzhiController extends HomeController {

	/**
	 * 配资首页
	 */
	public function index() {
        $this->seo = array('title' => '期货吧');
        $this->display();
	}
    public function main() {
        $this->seo = array('title' => '期货吧详情');
        $this->display();
    }
    public function pages() {
        $this->seo = array('title' => '期货吧详情');
        $this->display();
    }
    public function joins() {
        $this->seo = array('title' => '加盟业务');
        $this->display();
    }

    public function openPay(){
        if (!UID) {
            // 还没登录 跳转到登录页面
            $data['url'] = U('Uc/Uc/login');
            $this->ajaxReturn($data);
            return;
        }
        $pass_realname = M('UcenterMember')->where(['id'=>UID])->getField('pass_realname');
        if($pass_realname != 1){
            $data['url'] = U('Uc/Authentication/realname');
            $this->ajaxReturn($data);
            return;
        }
        $num = I('post.num')?intval(I('post.num')):0;
        $num or $data['msg'] = '请选择手数';
        $guzhibond = C('GUZHIBOND')?intval(C('GUZHIBOND')):1;
        $own_funds = $num * $guzhibond*10000;
        $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
        $data = [
            'own_funds' => money($own_funds),
            'balance'   => money($balance)
        ];
        $this->ajaxReturn($data);
    }

    public function pay(){

        $num = I('post.num')?intval(I('post.num')):0;
        $num or $this->error('请先选择手数');
        $paypwd = I('post.paypwd','');
        $paypwd or $this->error('请先填写支付密码');
        if (!UID) {
            $this->error('请先登录',U('Uc/Uc/login'));
        }
        $pass_realname = M('UcenterMember')->where(['id'=>UID])->getField('pass_realname');
        if($pass_realname != 1){
            $this->error('请先完成实名认证',U('Uc/Authentication/realname'));
        }
        if(!$this->_checkHasPwd()){
            $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
        }
        if($this->_checkPwd($paypwd,true)){
            $guzhibond = C('GUZHIBOND')?intval(C('GUZHIBOND')):1;
            $guzhiscale = C("GUZHISCALE")?C("GUZHISCALE"):8;
            $own_funds = $num * $guzhibond;
            $with_funds = $own_funds * $guzhiscale;
            $difference = $this->_checkMoney($own_funds*10000);
            if($difference){
                $this->error('余额不足，请先充值',U('Uc/Recharge/index',['amount'=>$difference]));
            }
            $data = [
                'uid' => UID,
                'own_funds' => $own_funds,
                'with_funds' => $with_funds,
                'time_limit' => 12,
                'interest_rate' => 2.00,
                'num'=>$num,
                'types'=>2,
                'aggre' => 1
            ];
            $mod = D('MainNeeds');
            if($orders = $mod->addData($data)){
                $own_funds = intval($own_funds)*10000;//本金
                D('MainFunds')->addData(-$own_funds,21);
                //站内信
                $title = '您成功提交了配资需求';
                $user_con = time_format().'您成功提交了配资需求，等待审核，单号：'.$orders;
                $admin_con = time_format().'用户成功提交了配资需求【股指吧】，用户名：'.USERNAME.'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/guzhi">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                $this->success('操作成功，等待审核',U('Uc/Qihuo/index'));
            }else{
                $this->error($mod->getError());
            }
        }else{
            $this->error('支付密码错误');
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