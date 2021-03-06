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
 * 我要配资管理控制器
 */

class DotouziController extends UcenterController {

	/**
	 * 投资需求
	 */
	public function index() {
        $map = [];
        list(
            $orders,
            $maycast,
            $p,
            $soso
            ) = array(
            I('request.orders',''),
            I('request.maycast',1,'int'),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $maycast and $map['status'] = 0;
        $orders and $map['orders'] = ['like','%'.$orders.'%'];
        $sort = I('request.sort')?I('request.sort'):'created_time DESC';
        $desc = I('request.desc')?' desc':'';
        $list = $this->lists('MainBids',$map,$sort.$desc,true,2);
        int_to_string($list);
        if(is_array($list)&& !empty($list)){
            foreach($list as $k=>$v){
                if(!$v['status']){
                    $info = D('MainBids')->relation(true)->find($v['id']);
                    $with_funds = $info['with_funds']*10000;
                    $may_throw = $this->may_throw($info);
                    $list[$k]['speed'] = round(($with_funds-$may_throw)/$with_funds*100,2);
                }
            }
        }
        $this->_list = $list;
        $this->orders   = $orders;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->seo = ['title' => '我要投资'];
        $this->crumbs = $this->_get_crumbs();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
	}

    /**
     * 投资详情页
     */
    public function details(){
        $id = I('get.id',0,'int');
        $id or $this->error('没有对应标的');
        $info = D('MainBids')->relation(true)->find($id);
        empty($info) and $this->error('标的信息不存在');
        $warning_val = C('WARNINGLINE')?C('WARNINGLINE'):112;
        $open_val = C('OPENLINE')?C('OPENLINE'):108;
        $info['all_funds'] = $info['own_funds']+$info['with_funds'];//操盘资金
        $info['warning_line'] = round(intval($info['with_funds'])*$warning_val/100,2);
        $info['open_line'] = round(intval($info['with_funds'])*$open_val/100,2);
        $this->user = M('UcenterMember')->field(['mobile,reg_time'])->find($info['uid']);
        $userdata = M('Userdata')->where(['uid'=>$info['uid']])->find();
        $this->info = $info;
        $with_funds = $info['with_funds']*10000;
        $may_throw = $this->may_throw($info);
        $this->may_throw = money($may_throw);
        $this->speed = round(($with_funds-$may_throw)/$with_funds*100,2);
        $this->userdata = $userdata;
        $this->cover = get_cover($userdata['card_front_water']);
        $this->tenders = M('MainTender')->where(['bid'=>$id])->select();
        $nid = M('MainBids')->where(['id'=>$id])->getField('nid');
        $trader = M('TraderChart')->where(['nid'=>$nid])->select();
        if($info['status']){
            if(is_array($trader) and !empty($trader)){
                foreach($trader as $k => $v){
                    $total_assets[] = $v['total_assets']/10000;
                    $pre_value[] = $v['pre_value']/10000;
                    $node[] = '"'.time_format($v['node'],'m-d').'"';
                }
                $prev_trader = array_pop($trader);
                $this->prev_pre_value = $prev_trader['pre_value'];
                $this->prev_total_assets = $prev_trader['total_assets'];
            }
            $this->node = implode(',',$node);
            $this->total_assets = implode(',',$total_assets);
            $this->pre_value = implode(',',$pre_value);
            $this->safe = $info['open_line'];
        }

        $this->trader = $trader;
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = array('title' => '投资详情');
        $this->crumbs = $this->_get_crumbs();
        $this->display();
    }

    /**
     * 投资
     */
    public function invest(){
        if(IS_POST){
            list(
                $id,
                $orders,
                $money,
                $paypwd
                )=[
                I('request.id',0,'int'),
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
                $tid = M('MainTender')->where(['orders'=>$orders,'uid'=>UID])->getField('id');
                if($tid)
                    //处理追加投资
                    api('Needs/doMainTender',[$info,$money,$tid]);
                else
                    api('Needs/doMainTender',[$info,$money]);
                //站内信
                $title = '您成功完成一笔投资';
                $user_con = time_format().'您成功完成一笔投资，金额：'.money($money).'，单号：'.$orders;
                $admin_con = time_format().'用户完成投资，用户名：'.USERNAME.'，金额：'.money($money).'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Tender/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);
                //处理自动满标
                if($may_throw == $money){
                    M('MainNeeds')->where(['orders'=>$orders])->setField('status',3);
                    M('MainBids')->where(['orders'=>$orders])->save(['status'=>1,'update_time'=>NOW_TIME]);
                    //站内信
                    $admin_con = time_format().'需求满标，及时绑定股票账户，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
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
     * 投资计算器
     */
    public function calculator(){

        $id = I('get.id',0,'int');
        if(!$id){
            $this->tip = '请选择投资标的';
        }
        $info = D('MainBids')->relation(true)->find($id);
        $this->info = $info;
        $this->may_throw = money($this->may_throw($info));
        $this->money = $this->may_throw($info);
        $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
        if($balance < 1000){
            $this->tip = '您的可用资金不足，请 <a href="'.U('Uc/Recharge/index').'">充值</a>';
        }
        $this->balance= money($balance);
        $this->display();

    }

    public function docalculator(){
        $money = I('money','');
        $profit = I('profit','');
        $time_limit = I('time_limit','');
        $profit or $msg = '请填写年化收益';
        $time_limit or $msg = '请填写投资期限';
        $money or $msg = '请填写投资金额';
        $back = money($money * $profit/100/12 * $time_limit);
        exit(json_encode(['msg'=>$msg,'back'=>$back]));
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
     * 验证密码
     * @param  string $password 密码
     * @param  boolean $paypwd 是否验证支付密码
     * @return boolean     检测结果
     */
    private function _checkPwd($password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser(UID, $password,$paypwd);
    }
}
