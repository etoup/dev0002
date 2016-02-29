<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;
use Think\Controller;
/**
 * 我的配资协议
 */

class PactController extends Controller {

    /**
     * 查看合同-配资
     */
    public function peizipact(){
        $uid = I('get.uid',0);
        $orders = I('get.orders','');
        $uid or $this->error('请先登录');
        $orders or $this->error('请选择操作项');
        //需求信息
        $needs = M('MainNeeds')->where(['orders'=>$orders])->find();
        empty($needs) and $this->error('无效单号');
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
        //配资方用户信息
        $peiziInfo = M('UcenterMember')->find($uid);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>$uid])->getField('card_number');
        //股票账户
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$needs['id']])->find();
        //还款信息
        $this->repayments = M('MainRepayment')->where(['orders'=>$orders,'uid'=>$uid])->select();
        $this->info = $needs;
        $this->peiziInfo = $peiziInfo;
        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }

    /**
     * 查看合同-配资
     */
    public function qihuopact(){
        $uid = I('get.uid',0);
        $uid or $this->error('请先登录');
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        //需求信息
        $needs = M('MainNeeds')->where(['orders'=>$orders])->find();
        empty($needs) and $this->error('无效单号');
        //投标信息
        $tenderInfo = M('MainTender')->where(['orders'=>$orders])->find();
        $this->tenderInfo = $tenderInfo;
        //配资方用户信息
        $peiziInfo = M('UcenterMember')->find($uid);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>$uid])->getField('card_number');
        //股票账户
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$needs['id']])->find();
        //还款信息
        $repayments = M('MainRepayment')->where(['orders'=>$orders,'uid'=>$uid])->sum('money');
        $this->repayment = $repayments/$needs['time_limit'];
        $this->funds = $repayments+$needs['with_funds']*10000;
        $this->info = $needs;
        $this->peiziInfo = $peiziInfo;
        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }

    /**
     * 查看合同-配资
     */
    public function guzhipact(){
        $uid = I('get.uid',0);
        $uid or $this->error('请先登录');
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        //需求信息
        $needs = M('MainNeeds')->where(['orders'=>$orders])->find();
        empty($needs) and $this->error('无效单号');
        //投标信息
        $tenderInfo = M('MainTender')->where(['orders'=>$orders])->find();
        $this->tenderInfo = $tenderInfo;
        //配资方用户信息
        $peiziInfo = M('UcenterMember')->find($uid);
        $peiziInfo['card_number'] = M('Userdata')->where(['uid'=>$uid])->getField('card_number');
        //股票账户
        $this->accountInfo = D('NeedsAccountView')->where(['nid'=>$needs['id']])->find();
        $this->info = $needs;
        $this->peiziInfo = $peiziInfo;
        $this->seo = ['title' => '配资借款协议'];
        $this->display();
    }

    /**
     * 查看合同-投资
     */
    public function touzipact(){
        $orders = I('get.orders','');
        $orders or $this->error('请选择操作项');
        $uid = I('get.uid',0,'int')?I('get.uid'):0;
        $uid or $this->error('请先登录');
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
    public function touziqihuopact(){
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
    public function touziguzhipact(){
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
}
