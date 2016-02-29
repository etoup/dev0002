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
 * 用户中心公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */

class UcenterController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty() {
		$this->redirect('Index/Index/index');
	}

	protected function _initialize() {
		// 获取当前用户ID
		if(defined('UID')) {
			return;
		}
		define('UID', is_login());
		if(!UID) {// 还没登录 跳转到登录页面
            $mtype = I('get.mobile');
            switch($mtype){
                case 'weixin':
                    $this->redirect('Uc/login',['mobile'=>'weixin']);
                    break;
                default:
                    $this->redirect('Uc/login');
            }
		}else{
            $member=M('Member')->find(UID);
            $this->member = $member;
            define('USERNAME', $member['nickname']);
            $rate = M('UcenterMember')->where(['id'=>UID])->getField('rate');
            define('RATE', $rate);
            $this->rate = RATE?RATE:C('INTERSTRATE');
        }

		// 读取站点配置
		$config = api('Config/lists');
		C($config);//添加配置
		if (!C('WEB_SITE_CLOSE')) {
			$this->error('站点已经关闭，请稍后访问~');
		}
        define('ROOT',C('HTTPHOST'));

        //获取左侧导航菜单
        $this->cont = CONTROLLER_NAME;
        $this->act  = ACTION_NAME;
        $navs = M('Nav')->where(['type'=>2,'link'=>CONTROLLER_NAME.'/index'])->find();
        $this->link = CONTROLLER_NAME.'/index';
        $this->navs = $navs;
        $this->navList = M('Nav')->where(['type'=>2,'parentid'=>$navs['parentid']])->order('orderid')->select();

		//页面导航
		$tnav = $this->_get_top_nav(0);
        foreach($tnav as $k => $v){
            $tnav[$k]['url'] = U('Index/'.$v['link']);
        }
        $this->tnav = $tnav;
		$this->bnav = $this->_get_top_nav(1);
		if (!S('UNAV')) {
			S('UNAV', $this->_get_top_nav(2));
		}
		$this->unav = S('UNAV');

        //获取用户组-是否可以推广
        $this->openT = M('MemberBelong')->where(['uid'=>UID,'gid'=>13])->find();
        //获取用户组-是否是客户经理
        $this->openM = M('MemberBelong')->where(['uid'=>UID,'gid'=>8])->find();
		//会员信息
        $field = array(
            'pass_phone',
            'pass_email',
            'pass_realname',
            'realname',
            'pass_paypwd',
            'pass_qa',
            'answer',
            'custom_service',
            'balance',
            'score'
        );
		$uc = M('UcenterMember')->where(array('id' => UID))->field($field)->find();
        $this->uc = $uc;
        $this->member = M('Member')->where(['uid'=>UID])->find();
        //获取客户经理信息
        $this->custom = M('UcenterMember')->where(['username'=>$uc['custom_service']])->field('username,realname,mobile')->find();
        //保证金统计信息
        $sum_b_list = M('MainBond')->where(['uid'=>UID])->order('created_time desc')->select();
        if(!empty($sum_b_list)){
            foreach($sum_b_list as $kb => $vb){
                if($kb < 10){
                    $sum_b_time_arr[] = "'".date('Y-m-d',$vb['created_time'])."'";
                    $sum_b_money_arr[] = $vb['money'];
                }
            }
            $this->sum_b = M('MainBond')->where(['uid'=>UID])->sum('money');
            $this->sum_b_time = implode(',',$sum_b_time_arr);
            $this->sum_b_money = implode(',',$sum_b_money_arr);
        }
        //出金统计信息
        //$this->sum_c = M('MainDraw')->where(['uid'=>UID,'types'=>0])->sum('amount');
        $sum_c_list = M('MainDraw')->where(['uid'=>UID,'types'=>0,'status'=>1])->order('created_time desc')->select();
        if(!empty($sum_c_list)){
            foreach($sum_c_list as $kc => $vc){
                if($kc < 10){
                    $sum_c_time_arr[] = "'".date('Y-m-d',$vc['created_time'])."'";
                    $sum_c_money_arr[] = $vc['money'];
                }
            }
            $this->sum_c = M('MainDraw')->where(['uid'=>UID,'types'=>0])->sum('amount');
            $this->sum_c_time = implode(',',$sum_c_time_arr);
            $this->sum_c_money = implode(',',$sum_c_money_arr);
        }
        //利息统计信息
        //$this->sum_l = M('MainRepayment')->where(['uid'=>UID,'types'=>-1,'status'=>1])->sum('money');
        $sum_l_list = M('MainRepayment')->where(['uid'=>UID,'types'=>-1,'status'=>1])->order('created_time desc')->select();
        if(!empty($sum_l_list)){
            foreach($sum_l_list as $kl => $vl){
                if($kl < 10){
                    $sum_l_time_arr[] = "'".date('Y-m-d',$vl['created_time'])."'";
                    $sum_l_money_arr[] = $vl['money'];
                }
            }
            $this->sum_l = M('MainRepayment')->where(['uid'=>UID,'types'=>-1,'status'=>1])->sum('money');
            $this->sum_l_time = implode(',',$sum_l_time_arr);
            $this->sum_l_money = implode(',',$sum_l_money_arr);
        }
        //投资统计信息
        //$this->sum_t = M('MainTender')->where(['uid'=>UID])->sum('money');
        $sum_t_list = M('MainTender')->where(['uid'=>UID])->order('created_time desc')->select();
        if(!empty($sum_t_list)){
            foreach($sum_t_list as $kt => $vt){
                if($kt < 10){
                    $sum_t_time_arr[] = "'".date('Y-m-d',$vt['created_time'])."'";
                    $sum_t_money_arr[] = $vt['money'];
                }
            }
            $this->sum_t = M('MainTender')->where(['uid'=>UID])->sum('money');
            $this->sum_t_time = implode(',',$sum_t_time_arr);
            $this->sum_t_money = implode(',',$sum_t_money_arr);
        }
        //预计收益统计信息
        //$this->sum_s = M('MainTender')->where(['uid'=>UID])->sum('total_revenue');
        $sum_s_list = M('MainTender')->where(['uid'=>UID])->order('created_time desc')->select();
        if(!empty($sum_s_list)){
            foreach($sum_s_list as $ks => $vs){
                if($ks < 10){
                    $sum_s_time_arr[] = "'".date('Y-m-d',$vs['created_time'])."'";
                    $sum_s_money_arr[] = $vs['total_revenue'];
                }
            }
            $this->sum_s = M('MainTender')->where(['uid'=>UID])->sum('total_revenue');
            $this->sum_s_time = implode(',',$sum_s_time_arr);
            $this->sum_s_money = implode(',',$sum_s_money_arr);
        }
        //累计收益统计信息
        //$this->sum_g = M('MainRepayment')->where(['uid'=>UID,'types'=>0,'status'=>1])->sum('money');
        $sum_g_list = M('MainRepayment')->where(['uid'=>UID,'types'=>0,'status'=>1])->order('created_time desc')->select();
        if(!empty($sum_g_list)){
            foreach($sum_g_list as $kg => $vg){
                if($kg < 10){
                    $sum_g_time_arr[] = "'".date('Y-m-d',$vg['created_time'])."'";
                    $sum_g_money_arr[] = $vg['money'];
                }
            }
            $this->sum_g = M('MainRepayment')->where(['uid'=>UID,'types'=>0,'status'=>1])->sum('money');
            $this->sum_g_time = implode(',',$sum_g_time_arr);
            $this->sum_g_money = implode(',',$sum_g_money_arr);
        }
        //消息统计
        $this->unread_num = M('MessageNotices')->where(['uid'=>UID,'is_read'=>0])->count();

        //处理预警
        $earlys = M('MainRepayment')->where(['uid'=>UID,'status'=>0,'types'=>-1,'remind'=>['elt',NOW_TIME]])->select();
        if(!empty($earlys)){
            foreach($earlys as $k => $v){
                //是否存在预警消息
                $hasMsg = M('MessageNotices')->where(['param'=>$v['id']])->count();
                if(empty($hasMsg)){
                    $title = '您有利息即将逾期，请及时支付';
                    $user_con = '您有利息即将逾期，请及时支付，<br/>单号：'.$v['orders'].'，<br/>金额：'.money($v['money']).'，<br/>截止时间：'.prevDate($v['time_repayment']).'，<br/><b><a href="'.U('Operations/repayment',['id'=>$v['id']]).'"><span aria-hidden="true" class="glyphicon glyphicon-share-alt"></span> 前往支付</a></b>';
                    D('MessageNotices')->saveData(UID,$title,$user_con,0,'',0,$v['id'],1);
                }
            }
        }

	}

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @param int      $type   模型类型  0：普通模型；1：视图模型；2：关联模型
     * @author waco <etoupcom@126.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists($model, $where = array(), $order = '', $field = true, $type = 0) {
        $options = array();
        $REQUEST = (array) I('request.');
        if (is_string($model)) {
            switch ($type) {
                case 1:
                    $model = D($model);
                    break;

                case 2:
                    $model = D($model)->relation(true);
                    break;

                default:
                    $model = M($model);
                    break;
            }
        }


        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk = $model->getPk();
        if ($order === null) {
            # code... //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk.' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);

        if (empty($where)) {
            $where = array('status' => array('egt', 0));
        }
        if (!empty($where)) {
            $options['where'] = $where;
        }
        $options = array_merge((array) $OPT->getValue($model), $options);
        $total   = $model->where($options['where'])->count();
        if (cookie('rows')) {
            $listRows = (int) cookie('rows');
        } elseif (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0?C('LIST_ROWS'):10;
        }
        //$listRows = 5;//调试用
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p = $page->show();
        $this->assign('_page', $p?$p:'');
        $this->assign('_total', $total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options', $options);
        return $model->field($field)->select();
    }

	private function _get_top_nav($type) {
		$nav = D('Nav')->get_nav($type);
		return $nav;
	}

    public function closepagewalkthrough(){
        $expire = 3600*24*7;
        cookie('closepagewalkthrough','yes',$expire);
    }
}
