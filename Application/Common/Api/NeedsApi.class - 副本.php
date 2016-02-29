<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com>
// +----------------------------------------------------------------------

namespace Common\Api;
class NeedsApi {

    /**
     * 缓存配资需求信息
     * @param array $item 数据
     * @param int $caches 缓存方式
     * @author wacp <etoupcom@126.com>
     */
	public static function addCaches($item,$caches=0) {
        $caches?S('NEEDS'.UID,$item,3600*24*7):cookie('NEEDS', $item, 3600*24*7);
	}

    /**
     * 批量业务处理
     * @param int $nid 需求ID
     * @param int $aid 账户ID
     * @param int $status 状态
     * @return boolean
     * @author waco <etoupcom@126.com>
     */
    public static function batchData($nid,$aid,$status){
        if(empty($nid) or empty($aid) or empty($status)){
            return false;
        }
        $info = M('MainNeeds')->find($nid);
        if(!$info){
            return false;
        }
        switch($status){
            case 8:
                //调整需求状态
                self::doMainNeeds($info,$status);
                //需求账户关系
                self::doNeedsAccount($nid,$aid);
                //配资方还款信息
                self::doRepaymentPeizi($info);
                //投资方收款信息
                if($info['make_bids']){
                    self::doRepaymentTouzi($info);
                }

                break;
        }
        return true;
    }

    /**
     * 调整需求状态
     * @author waco <etoupcom@126.com>
     */
    public static function doMainNeeds($info,$status){

        $n = $info['time_limit']+1;
        $next = strtotime(date('Y-m-01', strtotime(date('Y-m-d',NOW_TIME))) . " +$n month -1 day");
        if(date('d',NOW_TIME)>date('t',$next)){
            $end_trading = strtotime(date('Y-m-01', strtotime(date('Y-m-d',NOW_TIME))) . " +$n month");
        }else{
            $end_trading = strtotime(date('Y-m-d',strtotime('+'.$info['time_limit'].'month',NOW_TIME)));
        }
        $back = M('MainNeeds')->save(['id'=>$info['id'],'status'=>$status,'begin_trading'=>NOW_TIME,'end_trading'=>$end_trading]);
        return $back?true:false;
    }

    /**
     * 添加需求账户关系数据
     * @author waco <etoupcom@126.com>
     */
    public static function doNeedsAccount($nid,$aid){
        $back = M('NeedsAccount')->add(['nid'=>$nid,'aid'=>$aid,'status'=>1]);
        return $back?true:false;
    }

    /**
     * 配资方还款信息
     * @author waco <etoupcom@126.com>
     */
    public static function doRepaymentPeizi($info){
        //利息
        $time_limit = intval($info['time_limit']);//配资期限
        $interest = round(intval($info['with_funds'])*10000*$info['interest_rate']/100,2);//月利息
        for($i=0;$i<$time_limit;$i++){
            if($i==0){
                //本金
                $capital = $info['own_funds']*10000;
                M('MainBond')->add(['orders'=>$info['orders'],'uid'=>$info['uid'],'money'=>$capital,'created_time'=>NOW_TIME,'status'=>1]);
                //业务提成
                self::doStatisticsTake($info);
                $remind = strtotime(date('Y-m-d',NOW_TIME));//提醒时间
                $time_repayment = $remind;//到期时间
                $time_do_repaymen = NOW_TIME;//操作时间
                $status = 1;
            }else{
                $n = $i+1;
                $next = strtotime(date('Y-m-01', strtotime(date('Y-m-d',NOW_TIME))) . " +$n month -1 day");
                if(date('d',NOW_TIME)>date('t',$next)){
                    $time_repayment = strtotime(date('Y-m-01', strtotime(date('Y-m-d',NOW_TIME))) . " +$n month");//到期时间
                    $remind = strtotime(date('Y-m-d',strtotime("-3 days",$next)));//提醒时间
                }else{
                    $remind = strtotime(date('Y-m-d',strtotime("-3 days,+$i month",NOW_TIME)));//提醒时间
                    $time_repayment = strtotime(date('Y-m-d',strtotime("+$i month",NOW_TIME)));//到期时间
                }
                $time_do_repaymen = 0;//操作时间
                $status = 0;
            }
            $map = [
                'orders'=>$info['orders'],
                'uid'=>$info['uid'],
                'money'=>$interest,
                'remind'=>$remind,
                'time_repayment'=>$time_repayment,
                'time_do_repaymen'=>$time_do_repaymen?$time_do_repaymen:0,
                'types'=>-1,
                'status'=>$status,
                'created_time'=>NOW_TIME
            ];
            M('MainRepayment')->add($map);
        }
        //配资方积分日志信息
        $money = intval($info['with_funds'])*10000;
        self::doScore($money,$time_limit,$info['uid'],$info['orders'],0);
    }

    /**
     * 投资方收款信息
     * @param int $data 需求信息
     * @return boolean
     * @author waco <etoupcom@126.com>
     */
    public static function doRepaymentTouzi($data){
        $time_limit = intval($data['time_limit']);//配资期限
        $tenders = M('MainTender')->where(['orders'=>$data['orders']])->select();//投资列表
        $begin_trading = $data['begin_trading']?$data['begin_trading']:NOW_TIME;//开始交易时间，满标时间
        $time = strtotime(date('Y-m-d',$begin_trading));
        if(is_array($tenders)&&!empty($tenders)){
            foreach($tenders as $k => $v){
                //利息收益
                for($i=1;$i<=$time_limit;$i++){
                    $n = $i+1;
                    $next = strtotime(date('Y-m-01', strtotime(date('Y-m-d',$time))) . " +$n month -1 day");
                    if(date('d',$time)>date('t',$next)){
                        $time_repayment = strtotime(date('Y-m-01', strtotime(date('Y-m-d',$time))) . " +$n month");//到期时间
                        $remind = strtotime(date('Y-m-d',strtotime("-3 days",$next)));//提醒时间
                    }else{
                        $remind = strtotime(date('Y-m-d',strtotime("-3 days,+$i month",$time)));//提醒时间
                        $time_repayment = strtotime(date('Y-m-d',strtotime("+$i month",$time)));//到期时间
                    }
                    $interest = round($v['money']*$data['profit']/100/12);
                    $map = [
                        'orders'=>$data['orders'],
                        'uid'=>$v['uid'],
                        'money'=>$interest,
                        'remind'=>$remind,
                        'time_repayment'=>$time_repayment,
                        'created_time'=>NOW_TIME
                    ];
                    M('MainRepayment')->add($map);
                }
                //本金

                $n = $time_limit+1;
                $next = strtotime(date('Y-m-01', strtotime(date('Y-m-d',$time))) . " +$n month -1 day");
                if(date('d',$time)>date('t',$next)){
                    $time_repayment_b = strtotime(date('Y-m-01', strtotime(date('Y-m-d',$time))) . " +$n month");//到期时间
                    $remind_b = strtotime(date('Y-m-d',strtotime("-3 days",$next)));//提醒时间
                }else{
                    $remind_b = strtotime(date('Y-m-d',strtotime("-3 days,+$time_limit month",$time)));//提醒时间
                    $time_repayment_b = strtotime(date('Y-m-d',strtotime("+$time_limit month",$time)));//到期时间
                }
                $money = round($v['money']);
                $map_b = [
                    'orders'=>$data['orders'],
                    'uid'=>$v['uid'],
                    'money'=>$money,
                    'remind'=>$remind_b,
                    'time_repayment'=>$time_repayment_b,
                    'types'=>1,//本金回款
                    'created_time'=>NOW_TIME
                ];
                M('MainRepayment')->add($map_b);
                //投资方积分日志信息
                self::doScore($money,$time_limit,$v['uid'],$data['orders'],1);
            }
        }

        return true;
    }

    /**
     * 业务提成
     * @author waco <etoupcom@126.com>
     */
    public static function doStatisticsTake($data){
        $info = M('UcenterMember')->field('username,realname,custom_service')->where(['id'=>$data['uid']])->find();
        $username = $info['custom_service']?$info['custom_service']:'admin';
        $uid = M('UcenterMember')->where(['username'=>$username])->getField('id');

        //获取账户信息
        $needsaccount = M('NeedsAccount')->where(['nid'=>$data['id']])->find();
        //获取利息收入
        $interest = $data['with_funds']*10000*$data['interest_rate']/100;
        $money = ($data['interest_rate']-$data['benchmark'])/100*$data['with_funds']*10000*0.1;
        $map = [
            'uid'=>$uid,
            'aid'=>$needsaccount['aid'],
            'username'=>$username,
            'orders'=> $data['orders'],
            'with_funds' => $data['with_funds'],
            'need_username' => $info['username'],
            'need_realname' => $info['realname'],
            'deal_ratio' => $data['interest_rate'],
            'cose_ratio' => $data['benchmark'],
            'money'=>$money,
            'interest'=>$interest,
            'created_time'=>NOW_TIME
        ];
        $back = M('StatisticsTake')->add($map);
        if($username != 'admin') {
            //站内信
            $title = '您收到一笔业务提成';
            $user_con = time_format() . '您收到一笔业务提成，金额：' . money($money) . '，来自客户：' . $info['username'] . '，单号：' . $data['orders'] . '，配资额度：' . $data['with_funds'];
            $admin_con = time_format() . '用户成功支付利息，业务经理获得提成，金额：' . money($money) . '，来自客户：' . $info['username'] . '，单号：' . $data['orders'] . '，<a href="' . C('HTTPHOST') . '/admin.php/Take/index">立即查看</a>';
            $custom_service = M('UcenterMember')->where(['id' => $data['uid']])->getField('custom_service');
            $cs_uid = M('UcenterMember')->where(['username' => $custom_service])->getField('id');
            D('MessageNotices')->saveData($cs_uid, $title, $user_con, 1, $admin_con);
        }
        return $back?true:false;

    }

    /**
     * 积分日志信息
     * @author waco <etoupcom@126.com>
     */
    public static function doScore($money,$times,$uid,$orders,$types){
        switch($types){
            case 0://配资方日志
                if(C('SCOREPEIZI')){
                    $score = M('UcenterMember')->where(['id'=>$uid])->getField('score');
                    $getval = intval($money*C('SCOREPEIZI')/100/12*$times);
                    $nowscore = intval($score+$getval);
                    M('LogScore')->add(['uid'=>$uid,'getval'=>$getval,'score'=>$nowscore,'types'=>0,'remark'=>'单号'.$orders.'，配资金额：'.money($money).'，配资期限：'.$times.'个月','created_time'=>NOW_TIME]);
                    M('UcenterMember')->save(['id'=>$uid,'score'=>$nowscore]);
                }
                break;
            case 1://投资方日志
                if(C('SCORETOUZI')){
                    $score = M('UcenterMember')->where(['id'=>$uid])->getField('score');
                    $getval = intval($money*C('SCORETOUZI')/100/12*$times);
                    $nowscore = intval($score+$getval);
                    M('LogScore')->add(['uid'=>$uid,'getval'=>$getval,'score'=>$nowscore,'types'=>1,'remark'=>'单号'.$orders.'，投资金额：'.money($money).'，投资期限：'.$times.'个月','created_time'=>NOW_TIME]);
                    M('UcenterMember')->save(['id'=>$uid,'score'=>$nowscore]);
                }
                break;
        }
    }

    /**
     * 生成标的信息
     * @author waco <etoupcom@126.com>
     */
    public static function makeBids($data,$status=0){
        $interest_sum = round($data['with_funds']*10000*$data['profit']/100/12*$data['time_limit'],2);
        $deadline = $data['deadline'];
        $time_end = strtotime(date('Y-m-d H:i:s',strtotime("+$deadline days",NOW_TIME)));
        $map = [
            'nid'=>$data['id'],
            'uid'=>$data['uid'],
            'orders'=>$data['orders'],
            'profit'=>$data['profit'],
            'interest_sum'=>$interest_sum,
            'own_funds'=>$data['own_funds'],
            'with_funds'=>$data['with_funds'],
            'time_limit'=>$data['time_limit'],
            'deadline'=>$deadline,
            'status'=>$status,
            'time_start'=>NOW_TIME,
            'time_end'=>$time_end,
            'created_time'=>NOW_TIME,
            'update_time'=>NOW_TIME
        ];

        $back = M('MainBids')->add($map);

        return $back?true:false;
    }

    /**
     * 保存投标日志
     * @author waco <etoupcom@126.com>
     */
    public static function doMainTender($data,$money,$tid=0){
        $total_revenue = round($money*$data['profit']/100/12*$data['time_limit'],2);
        $map = [
            'uid'=>UID,
            'bid'=>$data['id'],
            'orders'=>$data['orders'],
            'money'=>$money,
            'username'=>USERNAME,
            'total_revenue'=>$total_revenue,
            'created_time'=>NOW_TIME,
            'created_ip'=>get_client_ip()
        ];

        //追投 处理金额和总收益
        if($tid){
            $info = M('MainTender')->find($tid);
            $back = M('MainTender')->save(['id'=>$tid,'money'=>$info['money']+$money,'total_revenue'=>$info['total_revenue']+$total_revenue]);
        }else{
            $back = M('MainTender')->add($map);
        }

        return $back?true:false;
    }

    /**
     * 保存投标日志
     * @author waco <etoupcom@126.com>
     */
    public static function doAppMainTender($uid,$username,$data,$money,$tid=0){
        $total_revenue = round($money*$data['profit']/100/12*$data['time_limit'],2);
        $map = [
            'uid'=>$uid,
            'bid'=>$data['id'],
            'orders'=>$data['orders'],
            'money'=>$money,
            'username'=>$username,
            'total_revenue'=>$total_revenue,
            'created_time'=>NOW_TIME,
            'created_ip'=>get_client_ip()
        ];

        //追投 处理金额和总收益
        if($tid){
            $info = M('MainTender')->find($tid);
            $back = M('MainTender')->save(['id'=>$tid,'money'=>$info['money']+$money,'total_revenue'=>$info['total_revenue']+$total_revenue]);
        }else{
            $back = M('MainTender')->add($map);
        }

        return $back?true:false;
    }

    /**
     * 自有资金默认投资方
     * @author waco <etoupcom@126.com>
     */
    public static function doDefaultTender($info){
        $data = M('MainBids')->where(['orders'=>$info['orders']])->find();
        $money = $data['with_funds']*10000;
        $total_revenue = round($money*$data['profit']/100/12*$data['time_limit'],2);
        $map = [
            'uid'=>2,
            'bid'=>$data['id'],
            'orders'=>$data['orders'],
            'money'=>$money,
            'username'=>'Tai天真',
            'total_revenue'=>$total_revenue,
            'created_time'=>NOW_TIME,
            'created_ip'=>get_client_ip()
        ];
        $back = M('MainTender')->add($map);
        return $back?true:false;
    }

    /**
     * 结束需求操作
     * @author waco <etoupcom@126.com>
     */
    public static function doFinishNeeds($needsdata, $operator='admin', $reason=''){
        //结束需求 回收
        M('MainNeeds')->where(['orders'=>$needsdata['orders']])->save(['status'=>-2,'operator'=>$operator,'operate_time'=>NOW_TIME,'reason'=>$reason]);
        //账户回收
        M('NeedsAccount')->where(['nid'=>$needsdata['nid']])->setField('status',0);
        //if($needsdata['make_bids']){
            M('MainBids')->where(['orders'=>$needsdata['orders']])->save(['status'=>-2]);
            //投资方获取罚金收益
            $tenderList = M('MainTender')->where(['orders'=>$needsdata['orders']])->select();
            if(is_array($tenderList) and !empty($tenderList)){
                foreach($tenderList as $k => $v){
                    $money = round($v['money']*$needsdata['profit']/100/12,2);

                    //还款信息 回收
                    $firstInfo = M('MainRepayment')->where(['uid'=>$v['uid'],'orders'=>$needsdata['orders'],'types'=>0,'status'=>0])->find();
                    $benInfo = M('MainRepayment')->where(['uid'=>$v['uid'],'orders'=>$needsdata['orders'],'types'=>1,'status'=>0])->find();
                    M('MainRepayment')->where(['uid'=>$v['uid'],'orders'=>$needsdata['orders'],'status'=>0])->save(['status'=>-1]);
                    M('MainRepayment')->where(['uid'=>$v['uid'],'orders'=>$needsdata['orders'],'id'=>['in',[$firstInfo['id'],$benInfo['id']]]])->save(['remind'=>$firstInfo['remind'],'time_repayment'=>$firstInfo['time_repayment'],'status'=>0]);

                    //记录资金日志
                    D('MainFunds')->saveData($money,18,$v['uid']);
                }
            }
            M('MainRepayment')->where(['orders'=>$needsdata['orders'],'status'=>0,'types'=>-1])->save(['status'=>-1]);
        //}
        return true;
    }

    /**
     * 操作出金
     * @author waco <etoupcom@126.com>
     */
    public static function doMainDraw($needsdata, $fine=true){
        $with_funds = $needsdata['with_funds']*10000;//配资金额
        if($fine){
            $interest = round(intval($needsdata['with_funds'])*10000*$needsdata['interest_rate']/100,2);//月利息
            $money  = round($needsdata['balance']-$interest-$with_funds,2);
            $remark = '提前结束，已扣除罚金，等待审核';
            //记录扣款日志
            D('MainFunds')->saveData(-$interest,28,$needsdata['uid']);
        }else{
            $money  = round($needsdata['balance']-$with_funds,2);
            $remark = '结束配资需求，未扣除罚金，等待审核';
        }
        $data = [
            'uid'=>$needsdata['uid'],
            'username'=>$needsdata['username'],
            'operation_amount'=>$money,
            'amount'=>$money,
            'created_time'=>NOW_TIME,
            'created_ip'=>get_client_ip(),
            'remark'=>$remark
        ];
        if($needsdata['cid']){
            $data['cid'] = $needsdata['cid'];
            $data['types'] = 1;
            $data['do_types'] = 2;
        }
        $back = M('MainDraw')->add($data);
        return $back?true:false;
    }
}