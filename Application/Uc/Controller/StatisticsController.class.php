<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;

/**
 * 盈亏统计控制器
 */

class StatisticsController extends UcenterController {

    const peiziNum = 7;
    const touziNum = 12;
	/**
	 * 盈亏统计
	 */
    public function index() {
        //p(strtotime('2015-01-28'));
        //配资盈亏时间节点
        $dateList = M('ProfitLoss')->where(['uid'=>UID])->order('node')->group('node')->getField('node',true);
        //p($dateList);
        if(!empty($dateList)){
            foreach($dateList as $k => $v){
                $peiziDate[$k] = '"'.date('Y-m-d',$v).'"';
                //总资产
                $tids = M('ProfitLoss')->where(['node'=>$v,'uid'=>UID])->group('aid')->getField('tid',true);
                $allMoney[$k] = M('TraderChart')->where(['id'=>['in',$tids]])->sum('total_assets');
                $allMoney[$k] = $allMoney[$k]/10000;
                //配资金额
                $with_funds[$k] = M('ProfitLoss')->where(['node'=>$v,'uid'=>UID])->sum('with_funds');
                $with_funds[$k] = -$with_funds[$k]/10000;
                //自有金额
                $own_funds[$k] = M('ProfitLoss')->where(['node'=>$v,'uid'=>UID])->sum('own_funds');
                $own_funds[$k] = -$own_funds[$k]/10000;
                //账户出金
                $cash[$k] = M('ProfitLoss')->where(['node'=>$v,'uid'=>UID])->sum('cash');
                $cash[$k] = $cash[$k]/10000;
                //补充保证金
                $supply_bond[$k] = M('ProfitLoss')->where(['node'=>$v,'uid'=>UID])->sum('supply_bond');
                $supply_bond[$k] = -$supply_bond[$k]/10000;
                //盈亏金额
                $allLast[$k] = $allMoney[$k] + $with_funds[$k] + $own_funds[$k] + $cash[$k] + $supply_bond[$k];
            }
            //p($allLast);
            $this->peiziDate = implode(',',$peiziDate);
            $this->allMoney = implode(',',$allMoney);
            $this->with_funds = implode(',',$with_funds);
            $this->own_funds = implode(',',$own_funds);
            $this->cash = implode(',',$cash);
            $this->supply_bond = implode(',',$supply_bond);
            $this->allLast = implode(',',$allLast);
        }


        //当前年月
        $now = date('Y-m').'-01';
        for($ti=self::touziNum;$ti>=1;$ti--){
            $pnode = strtotime("-$ti months",strtotime($now));
            $nnode = strtotime("next months",$pnode);
            $touziDate[$ti] = '"'.date('Y-m',$pnode).'"';
            $sumMoney = M('MainTender')->where(['uid'=>UID,'created_time'=>['between',[$pnode,$nnode]]])->sum('money');
            $touziMoney[$ti] = empty($sumMoney)?0:$sumMoney/10000;
            //$sumProfit = M('MainRepayment')->where(['uid'=>UID,'types'=>0,'status'=>1,'time_repayment'=>['between',[$pnode,$nnode]]])->sum('money');
            $sumProfit = M('MainTender')->where(['uid'=>UID,'created_time'=>['between',[$pnode,$nnode]]])->sum('total_revenue');
            $touziProfit[$ti] = empty($sumProfit)?0:$sumProfit/10000;
        }
        $this->touziDate = implode(',',$touziDate);//投资统计月份
        $this->touziMoney = implode(',',$touziMoney);//投资金额
        $this->touziProfit = implode(',',$touziProfit);//投资盈利
        //p($this->touziMoney);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = ['title' => '盈亏统计'];
        $this->crumbs = $this->_get_crumbs();
        $this->display();
    }

    private function _get_crumbs() {
        return ['url' => U('Index/index'), 'title' => '会员中心'];
    }
}
