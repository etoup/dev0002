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
 * 股票配资管理控制器
 */

class DopeiziController extends UcenterController {

	/**
	 * 配资需求
	 */
	public function index() {
        $this->maxscale = $this->getMaxScale();
        $needs = self::_getNeeds();
        if($needs){
            $needs['with_funds'] = intval($needs['own_funds']*$needs['scale']);
            //本金
            $needs['own_funds_format'] = money(intval($needs['own_funds'])*10000);
            //利息
            $interest_rate = C('INTERSTRATE')?C('INTERSTRATE'):2.00;
            $needs['interest'] = intval($needs['with_funds'])*10000*$interest_rate/100;
            $needs['interest_format'] = money($needs['interest']);
            //本息
            $needs['all_funds_format'] = money($needs['own_funds']*10000+$needs['interest']);
            //意向金
            $needs['bond'] = C('BOND')?C('BOND'):0.1;
            $paymoney = $this->_getBond($needs);
            $needs['intention_format'] = money($paymoney);
            $needs['intention_format_all'] = money($paymoney+($needs['own_funds']*10000+$needs['interest']));
            //总操盘资金
            $needs['all_funds'] = $needs['own_funds'] + $needs['with_funds'];
            //预警线
            $needs['warning_val'] = C('WARNINGLINE')?C('WARNINGLINE'):112;
            $needs['warning_funds'] = $needs['with_funds']*$needs['warning_val']/100;
            //平仓线
            $needs['open_val'] = C('OPENLINE')?C('OPENLINE'):108;
            $needs['open_funds'] = $needs['with_funds']*$needs['open_val']/100;
            //月利率
            $needs['interest_rate'] = C('INTERSTRATE')?C('INTERSTRATE'):2.00;
            $needs['interest_funds'] = $needs['with_funds']*$needs['interest_rate']/100;

            $this->needs = $needs;
        }
        $this->seo = ['title' => '股票配资'];
        $this->crumbs = $this->_get_crumbs();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
	}

    private function getMaxScale(){
        $maxscale = M('UcenterMember')->where(['id'=>UID])->getField('maxscale');
        return $maxscale;
    }

    /**
     * 处理提交的需求
     */
    public function needs(){
        if(IS_POST){
            list(
                $own_funds,
                $scale,
                $time_limit,
                $caches,
                $from
                ) = [
                I('post.own_funds',0,'int'),
                I('post.scale',0,'int'),
                I('post.time_limit',0,'int'),
                I('post.caches',0,'int'),
                I('post.from','peizi')
            ];
            $own_funds or $this->error('请填写自有资金，必须为正整数');
            $scale or $this->error('请填写配资比例，必须为正整数');
            $time_limit or $this->error('请填写配资期限，必须为正整数');
            $own_funds <= 1000 or $this->error('您的配资需求超出范围，确定需要配资，请联系在线客服');
            $scale <= $this->getMaxScale() or $this->error('您的配资需求超出范围，确定需要配资，请联系在线客服');
            $time_limit <= 12 or $this->error('您的配资需求超出范围，确定需要配资，请联系在线客服');
            if($with_funds=I('post.with_funds',0,'int'))
                $with_funds/$own_funds <= $this->getMaxScale() or $this->error('您的配资需求超出范围，确定需要配资，请联系在线客服');

            switch($from){
                case 'index':
                    //缓存
                    S('NEEDS'.UID,null);
                    api('Needs/addCaches',[['own_funds'=>$own_funds,'scale'=>$scale,'time_limit'=>$time_limit],$caches]);
                    $backUrl = 'index';
                    break;
                case 'peizi':

                    if (!UID) {
                        // 还没登录 跳转到登录页面
                        $backUrl = 'Uc/Uc/login';
                    }else{
                        //缓存
                        $data = I('post.');
                        $data['interest_rate'] = C('INTERSTRATE')?C('INTERSTRATE'):2.00;
                        $data['interest_rate'] = RATE?RATE:C('INTERSTRATE');
                        api('Needs/addCaches',[$data,1]);
                        $backUrl = 'doneeds';
                    }
                    break;
            }
            $this->redirect($backUrl);
        }else{
            $this->error('非法操作');
        }
    }

    /**
     * 确认配资需求  安全确认
     */
    public function doneeds() {
        if (!UID) {
            // 还没登录 跳转到登录页面
            $this->redirect('Uc/Uc/login');
        }
        $pass_realname = M('UcenterMember')->where(['id'=>UID])->getField('pass_realname');
        if($pass_realname != 1)
            $this->redirect('Uc/Authentication/realname');
        $needs = self::_getNeeds();
        if($needs){
            $warning_val = C('WARNINGLINE')?C('WARNINGLINE'):112;
            $open_val = C('OPENLINE')?C('OPENLINE'):108;
            $needs['own_funds'] =intval($needs['own_funds']);
            $needs['own_funds_format'] = money(intval($needs['own_funds'])*10000);
            $needs['with_funds'] = intval($needs['with_funds']);
            $needs['with_funds_format'] = money(intval($needs['with_funds'])*10000);
            $needs['all_funds'] = intval($needs['own_funds']+$needs['with_funds']);
            $needs['all_funds_format'] = money(($needs['own_funds']+$needs['with_funds'])*10000);
            $needs['warning_line'] = round(intval($needs['with_funds'])*$warning_val/100,2);
            $needs['warning_line_format'] = money($needs['warning_line']*10000);
            $needs['open_line'] = round(intval($needs['with_funds'])*$open_val/100,2);
            $needs['open_line_format'] = money($needs['open_line']*10000);
            if($needs['pay_type']){
                $paymoney = $this->_getBond($needs);
                $needs['intention'] = money($paymoney);
            }else{
                $paymoney = (intval($needs['own_funds'])*10000)+(intval($needs['with_funds'])*10000*$needs['interest_rate']/100);
                $needs['principal_interest'] = money($paymoney);
            }

            $this->needs = $needs;
        }else{
            $this->redirect('index');
        }
        //可用资金
        $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
        $this->balance = money($balance);
        $this->paymoney = money($paymoney);
        $this->diff = $paymoney-$balance;
        $this->diff_format = money($this->diff);
        ($balance >= $paymoney) or $this->tip=true;
        $this->crumbs = $this->_get_crumbs();
        $this->seo = array('title' => '确认配资需求');
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 提交需求
     */
    public function saveneeds(){
        if(IS_POST){
            $paypwd = I('post.paypwd','');
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            empty($paypwd) and $this->error('填写支付密码');
            if($this->_checkPwd($paypwd,true)){
                $needs = S('NEEDS'.UID);
                empty($needs) and $this->error('请重新填写配资需求');
                $data = [
                    'uid' => UID,
                    'own_funds' => $needs['own_funds'],
                    'scale' => $needs['scale'],
                    'with_funds' => $needs['with_funds'],
                    'time_limit' => $needs['time_limit'],
                    'interest_rate' => $needs['interest_rate'],
                    'pay_type' => $needs['pay_type'],
                    'aggre' => 1
                ];
                $mod = D('MainNeeds');
                if($orders = $mod->addData($data)){
                    $own_funds = intval($needs['own_funds'])*10000;//本金
                    $interest = $needs['interest_rate']/100*$own_funds*$needs['scale'];//利息
                    switch($needs['pay_type']){
                        case 0://本息支付
                            //资金记录-首期保证金
                            D('MainFunds')->addData(-$own_funds,21);
                            //资金记录-利息
                            D('MainFunds')->addData(-$interest,23);
                            break;
                        case 1://意向金支付
                            //资金记录-意向金
                            $paymoney = $this->_getBond($needs);
                            D('MainFunds')->addData(-$paymoney,25);
                            break;
                    }
                    //销毁
                    S('NEEDS'.UID,null);
                    cookie('NEEDS',null);
                    //站内信
                    $title = '您成功提交了配资需求';
                    $user_con = time_format().'您成功提交了配资需求，等待审核，单号：'.$orders;
                    $admin_con = time_format().'用户成功提交了配资需求，用户名：'.USERNAME.'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
                    D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                    $this->success('操作成功，等待审核',U('Uc/Peizi/index'));
                }else{
                    $this->error($mod->getError());
                }
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $this->display();
        }
    }

    /**
     * 确认需求
     */
    public function affirm(){

    }



    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
    }

    /**
     * 获取参数
     */
    private static function _getNeeds() {
        $needs = S('NEEDS'.UID)?S('NEEDS'.UID):cookie('NEEDS');
        return $needs;
    }

    /**
     * 获取意向金
     */
    private static function _getBond($needs) {
        $bond = C('BOND')?C('BOND'):0.1;
        $paymoney = $needs['with_funds']*10000*$bond/100;
        return ($paymoney>50)?$paymoney:50;
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
