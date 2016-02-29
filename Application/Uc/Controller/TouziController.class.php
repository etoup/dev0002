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
 * 我的投资管理控制器
 */

class TouziController extends UcenterController {

	/**
	 * 投资管理
	 */
	public function index() {

        $map['uid'] = UID;

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

        $sort = I('get.sort')?I('get.sort'):'status DESC,created_time DESC';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('MainTenderView',$map,$sort.$desc,true,1);
        int_to_string($list);
        $this->_list = $list;
		$this->seo = ['title' => '我的投资'];
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
        $trader = M('TraderChart')->where(['nid'=>$info['id']])->select();
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
        $this->safe = $info['warning_line'];
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
     * 回款信息
     */
    public function collections(){
        $orders = I('get.orders');
        $orders or ajaxMsg('请选择操作的项目',false);
        $this->list = M('MainRepayment')->where(['uid'=>UID,'orders'=>$orders])->select();
        //p($this->list);
        $this->display();
    }

    /**
     * 追加投资
     */
    public function additional(){
        if(IS_POST){
            list(
                $id,
                $orders,
                $money,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.orders',''),
                I('post.money',0,'int'),
                I('post.paypwd',''),
            ];
            $id or $this->error('请选择投资标的');
            $orders or $this->error('无效单号');
            $money>=1000 or $this->error('最低投资额为1000');
            $paypwd or $this->error('请填写支付密码');
            $info = D('MainBids')->relation(true)->find($id);
            $info['orders'] == $orders or $this->error('无效单号');
            $may_throw = $this->may_throw($info);
            $may_throw >= $money or $this->error('您的投标资金超出可投范围');
            if($this->_checkPwd($paypwd,true)){
                //处理资金日志
                D('MainFunds')->addData(-$money,24);
                //处理投资日志
                $tid = M('MainTender')->where(['orders'=>$orders])->getField('id');
                if($tid)
                    //处理追加投资
                    api('Needs/doMainTender',[$info,$money,$tid]);
                else
                    api('Needs/doMainTender',[$info,$money]);
                //站内信
                $title = '您成功追加一笔投资';
                $user_con = time_format().'您成功追加一笔投资，金额：'.money($money).'，单号：'.$orders;
                $admin_con = time_format().'用户追加投资，用户名：'.USERNAME.'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Tender/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                //处理自动满标
                if($may_throw == $money){
                    M('MainNeeds')->where(['orders'=>$orders])->setField('status',3);
                    M('MainBids')->where(['orders'=>$orders])->save(['status'=>1,'update_time'=>NOW_TIME]);
                    //站内信
                    $admin_con = time_format().'需求满标，请及时绑定股票账户，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
                    D('MessageNotices')->saveData(1,'','',1,$admin_con);
                }
                $this->success('投资成功');
            }else{
                $this->error('支付密码错误');
            }
        }else{

            $id = I('get.id',0,'int');
            if(!$id){
                $this->tip = '请选择投资标的';
            }
            $info = D('MainBids')->relation(true)->find($id);
            if($info['uid'] == UID){
                $this->tip = '您不能投资自己的标的，请选择其他可投标的';
            }
            $this->info = $info;
            $this->may_throw = money($this->may_throw($info));
            $this->money = $this->may_throw($info);
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            if($balance < 1000){
                $this->tip = '您的可用资金不足，请 <a href="'.U('Uc/Recharge/index').'">充值</a>';
            }
            if(!UID){
                $this->tip = '请先登录';
            }
            $this->balance= money($balance);
            $this->display();
        }
    }

    /**
     * 查看合同-投资
     */
    public function pact(){
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        $uid = I('get.uid',0,'int')?I('get.uid'):UID;
        $info = D('MainTenderView')->where(['orders'=>$orders,'uid'=>$uid])->find();
        empty($info) and $this->error('无效单号');
        $info['lixi'] = $info['money'] * $info['profit']/100/12;
        $info['benxi'] = $info['lixi'] * $info['time_limit'] + $info['money'];
        $peiziInfo = M('UcenterMember')->find($info['peiziuid']);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>$info['peiziuid']])->getField('card_number');
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$info['nid']])->find();
        $this->info = $info;
        $this->peiziInfo = $peiziInfo;
        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }
    /**
     * 查看合同-投资
     */
    public function pactqihuo(){
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        //需求信息
        $needs = M('MainNeeds')->where(['orders'=>$orders])->find();
        empty($needs) and $this->error('无效单号');
        //投标信息
        $tenderInfo = M('MainTender')->where(['orders'=>$orders])->find();
        $this->tenderInfo = $tenderInfo;
        //配资方用户信息
        $peiziInfo = M('UcenterMember')->find($needs['uid']);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>$needs['uid']])->getField('card_number');
        //股票账户
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$needs['id']])->find();
        //还款信息
        $repayments = M('MainRepayment')->where(['orders'=>$orders,'uid'=>$needs['uid']])->sum('money');
        $this->repayment = $repayments/$needs['time_limit'];
        $this->funds = $repayments+$needs['with_funds']*10000;
        $this->info = $needs;
        $this->peiziInfo = $peiziInfo;

        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }
    /**
     * 查看合同-投资
     */
    public function pactguzhi(){
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        //需求信息
        $needs = M('MainNeeds')->where(['orders'=>$orders])->find();
        empty($needs) and $this->error('无效单号');
        //投标信息
        $tenderInfo = M('MainTender')->where(['orders'=>$orders])->find();
        $this->tenderInfo = $tenderInfo;
        //配资方用户信息
        $peiziInfo = M('UcenterMember')->find($needs['uid']);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>$needs['uid']])->getField('card_number');
        //股票账户
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$needs['id']])->find();
        $this->info = $needs;
        $this->peiziInfo = $peiziInfo;
        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }

    public function may_throw($info){
        $with_funds = $info['with_funds']*10000;
        if(empty($info['tenders'])){
            $money = 0;
        }else{
            $money = 0;
            foreach($info['tenders'] as $v){
                $money += $v['money'];
            }
        }
        $may_throw = $with_funds-$money;

        return $may_throw;
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
