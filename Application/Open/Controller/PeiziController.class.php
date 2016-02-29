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
Class PeiziController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    public function getconfig(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $userinfo = M('UcenterMember')->field('id,balance,pass_realname,pass_paypwd,rate,maxscale')->find($uid);
                $list = [
                    'uid'=>$uid,
                    'pass_paypwd'=>$userinfo['pass_paypwd'],
                    'pass_realname'=>$userinfo['pass_realname'],
                    'qihuo_max_scale' => C("QIHUOMAXSCALE")?C("QIHUOMAXSCALE"):10,
                    'guzhi_scale' => C("GUZHISCALE")?C("GUZHISCALE"):8,
                    'guzhi_fee' => C("GUZHIFEE")?C("GUZHIFEE"):150,
                    'guzhi_bond' => C("GUZHIBOND")?C("GUZHIBOND"):1
                ];
                $this->response($list,'json');
                break;
        }
    }
    public function getinfo(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $own_funds = $_POST['own_funds']?intval($_POST['own_funds']):0;
                $scale = $_POST['scale']?intval($_POST['scale']):0;
                $time_limit = $_POST['time_limit']?intval($_POST['time_limit']):0;
                $qihuo = $_POST['qihuo']?intval($_POST['qihuo']):0;
                $maxscale = C("QIHUOMAXSCALE")?C("QIHUOMAXSCALE"):10;
                if(empty($uid)){
                    $data = [
                        'msg' => '登录后才可以操作配资'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(empty($own_funds)){
                    $data = [
                        'msg' => '请填写自有资金'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(!is_numeric($own_funds)){
                    $data = [
                        'msg' => '自有资金必须为数字'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(($own_funds < 10) || ($own_funds > 1000)){
                    $data = [
                        'msg' => '自有资金范围：10-1000'
                    ];
                    $this->response($data,'json');
                    return;
                }

                if(empty($scale)){
                    $data = [
                        'msg' => '请填写配资比例'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(!is_numeric($scale)){
                    $data = [
                        'msg' => '配资比例必须为数字'
                    ];
                    $this->response($data,'json');
                    return;
                }
                switch($qihuo){
                    case 1:
                        if(($scale < 1) || ($scale > $maxscale)){
                            $data = [
                                'msg' => '配资比例范围：1-' . $maxscale
                            ];
                            $this->response($data,'json');
                            return;
                        }
                        break;
                    default:
                        if(($scale < 1) || ($scale > 5)){
                            $data = [
                                'msg' => '配资比例范围：1-5'
                            ];
                            $this->response($data,'json');
                            return;
                        }
                }

                if(empty($time_limit)){
                    $data = [
                        'msg' => '请填写配资期限'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(!is_numeric($time_limit)){
                    $data = [
                        'msg' => '配资期限必须为数字'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(($time_limit < 1) || ($time_limit > 12)){
                    $data = [
                        'msg' => '配资期限范围：1-12'
                    ];
                    $this->response($data,'json');
                    return;
                }
                //是否实名认证
                $userinfo = M('UcenterMember')->field('id,balance,pass_realname,pass_paypwd,rate,maxscale')->find($uid);
                if(!$userinfo['pass_paypwd']){
                    $data = [
                        'msg' => '为了您的资金安全，请先设置支付密码'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(!$userinfo['pass_realname']){
                    $data = [
                        'msg' => '完成实名认证后才可配资'
                    ];
                    $this->response($data,'json');
                    return;
                }
                //获取配置项
                $needs = $this->_getNeeds();
                $interstrate = $userinfo['rate']?$userinfo['rate']:$needs['interstrate'];
                $with_funds = $own_funds * $scale;
                switch($qihuo){
                    case 1:
                        $warningline = '--';
                        $openline = $own_funds*30/100 + $with_funds;
                        break;
                    default:
                        $warningline = $needs['warningline']/100 * $with_funds;
                        $openline = $needs['openline']/100 * $with_funds;
                }
                $interest = $interstrate/100*$with_funds*10000;
                $pay_funds = $own_funds*10000+$interest;
//                if($pay_funds > $userinfo['balance']){
//                    $data = [
//                        'msg' => '余额不足，请充值'
//                    ];
//                    $this->response($data,'json');
//                    return;
//                }
                $data = [
                    'pay_funds'    => money($pay_funds),
                    'own_funds'    => $own_funds,
                    'scale'        => $scale,
                    'with_funds'   => $with_funds,
                    'funds'        => $with_funds + $own_funds,
                    'warningline'  => $warningline,
                    'openline'     => $openline,
                    'time_limit'   => $time_limit,
                    'interstrate'  => $interstrate,
                    'interest'     => money($interest),
                    'qihuo'        => $qihuo
                ];
                $this->response($data,'json');
                break;
        }
    }

    public function peizipay(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $userinfo = M('UcenterMember')->field('id,balance')->find($uid);
                if(!empty($userinfo)){
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
                $own_funds = $_POST['own_funds']?intval($_POST['own_funds']):0;
                $scale = $_POST['scale']?intval($_POST['scale']):0;
                $time_limit = $_POST['time_limit']?intval($_POST['time_limit']):0;
                $paypwd = $_POST['paypwd']?_safe($_POST['paypwd']):'';
                $qihuo = $_POST['qihuo']?intval($_POST['qihuo']):0;
                if(empty($uid)){
                    $list = [
                        'status'=> 1,
                        'msg'=>'请先登录后操作'
                    ];
                    $this->response($list,'json');
                }
                if(empty($own_funds)){
                    $list = [
                        'status'=> 2,
                        'msg'=>'请填写自有资金'
                    ];
                    $this->response($list,'json');
                }
                if(!is_numeric($own_funds)){
                    $list = [
                        'status'=> 3,
                        'msg'=>'自有资金必须为数字'
                    ];
                    $this->response($list,'json');
                }
                switch($qihuo){
                    case 1:
                        if(($own_funds < 10) || ($own_funds > 1000)){
                            $list = [
                                'status'=> 4,
                                'msg'=>'自有资金范围：10-1000'
                            ];
                            $this->response($list,'json');
                        }
                        break;
                    default:
                        if(($own_funds < 1) || ($own_funds > 1000)){
                            $list = [
                                'status'=> 4,
                                'msg'=>'自有资金范围：1-1000'
                            ];
                            $this->response($list,'json');
                        }
                }
                if(empty($scale)){
                    $list = [
                        'status'=> 5,
                        'msg'=>'请填写配资比例'
                    ];
                    $this->response($list,'json');
                }
                if(!is_numeric($scale)){
                    $list = [
                        'status'=> 6,
                        'msg'=>'配资比例必须为数字'
                    ];
                    $this->response($list,'json');
                }
                switch($qihuo){
                    case 1:
                        if(($scale < 1) || ($scale > 10)){
                            $list = [
                                'status'=> 7,
                                'msg'=>'配资比例范围：1-10'
                            ];
                            $this->response($list,'json');
                        }
                        break;
                    default:
                        if(($scale < 1) || ($scale > 5)){
                            $list = [
                                'status'=> 7,
                                'msg'=>'配资比例范围：1-5'
                            ];
                            $this->response($list,'json');
                        }
                }


                if(empty($time_limit)){
                    $list = [
                        'status'=> 8,
                        'msg'=>'请填写配资期限'
                    ];
                    $this->response($list,'json');
                }
                if(!is_numeric($time_limit)){
                    $list = [
                        'status'=> 9,
                        'msg'=>'配资期限必须为数字'
                    ];
                    $this->response($list,'json');
                }
                if(($time_limit < 1) || ($time_limit > 12)){
                    $list = [
                        'status'=> 10,
                        'msg'=>'配资期限范围：1-12'
                    ];
                    $this->response($list,'json');
                }

                if(empty($paypwd)){
                    $list = [
                        'status'=> 11,
                        'msg'=>'请填写支付密码'
                    ];
                    $this->response($list,'json');
                }

                $userinfo = M('UcenterMember')->field('id,balance,pass_realname,rate,maxscale')->find($uid);
                if(!empty($userinfo)){
                    $needs = $this->_getNeeds();
                    $interstrate = $userinfo['rate']?$userinfo['rate']:$needs['interstrate'];
                    $with_funds = $own_funds * $scale;
                    $interest = $interstrate/100*$with_funds*10000;
                    $pay_funds = $own_funds*10000+$interest;
                    if($pay_funds > $userinfo['balance']){
                        $list = [
                            'status'=> 12,
                            'msg'=>'账户可用余额不足，请充值'
                        ];
                        $this->response($list,'json');
                    }
                }
                if(!$this->_checkPwd($uid,$paypwd,true)){
                    $list = [
                        'status'=> 13,
                        'msg'=>'请正确填写支付密码'
                    ];
                    $this->response($list,'json');
                }

                $data = [
                    'uid' => $uid,
                    'own_funds' => $own_funds,
                    'scale' => $scale,
                    'with_funds' => $with_funds,
                    'time_limit' => $time_limit,
                    'interest_rate' => $interstrate,
                    'pay_type' => 0,
                    'types' => $qihuo ? 1 : 0,
                    'aggre' => 1
                ];

                $mod = D('MainNeeds');
                if($orders = $mod->addData($data)){
                    $own_funds = intval($own_funds)*10000;//本金
                    $interest = $data['interest_rate']/100*$own_funds*$scale;//利息
                    //资金记录-首期保证金
                    D('MainFunds')->addData(-$own_funds,$uid,21);
                    //资金记录-利息
                    D('MainFunds')->addData(-$interest,$uid,23);
                    //站内信
                    $title = '您成功提交了配资需求';
                    $user_con = time_format().'您成功提交了配资需求，等待审核，单号：'.$orders;
                    $admin_con = time_format().'用户成功提交了配资需求，用户名：'.$userinfo['username'].'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/index">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                    $list = [
                        'status'=> 0,
                        'msg'=>'配资成功'
                    ];
                    $this->response($list,'json');
                }else{
                    $list = [
                        'status'=> 14,
                        'msg'=>$mod->getError()
                    ];
                    $this->response($list,'json');
                }
                break;
        }
    }

    public function dopayguzhi(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $data = $_POST['data']?intval($_POST['data']):0;
                $paypwd = $_POST['paypwd']?intval($_POST['paypwd']):'';
                if(empty($uid)){
                    $list = [
                        'status'=> 1,
                        'msg'=>'请先登录后操作'
                    ];
                    $this->response($list,'json');
                }
                if(empty($data)){
                    $list = [
                        'status'=> 2,
                        'msg'=>'请选择操盘手数'
                    ];
                    $this->response($list,'json');
                }
                if(!is_numeric($data)){
                    $list = [
                        'status'=> 3,
                        'msg'=>'操盘手数必须为数字'
                    ];
                    $this->response($list,'json');
                }
                if(($data < 1) || ($data > 10)){
                    $list = [
                        'status'=> 4,
                        'msg'=>'操盘手数范围：1-10'
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

                $userinfo = M('UcenterMember')->field('id,balance,pass_realname,pass_paypwd,rate,maxscale')->find($uid);
                if(!empty($userinfo)){
                    if($userinfo['pass_realname'] != 1){
                        $list = [
                            'status'=> 6,
                            'msg'=>'请先完成实名认证'
                        ];
                        $this->response($list,'json');
                    }
                    if($userinfo['pass_paypwd'] != 1){
                        $list = [
                            'status'=> 7,
                            'msg'=>'请先设置支付密码'
                        ];
                        $this->response($list,'json');
                    }
                    $guzhiscale = C("GUZHISCALE")?C("GUZHISCALE"):8;
                    $guzhibond = C('GUZHIBOND')?intval(C('GUZHIBOND')):1;
                    $own_funds = $data * $guzhibond;
                    $with_funds = $own_funds * $guzhiscale;
                    $pay_funds = $own_funds*10000;
                    if($pay_funds > $userinfo['balance']){
                        $money = $pay_funds-$userinfo['balance'];
                        $list = [
                            'status'=> 8,
                            'money'=>$money,
                            'msg'=>'账户可用余额不足，请充值'
                        ];
                        $this->response($list,'json');
                    }
                }
                if(!$this->_checkPwd($uid,$paypwd,true)){
                    $list = [
                        'status'=> 9,
                        'msg'=>'请正确填写支付密码'
                    ];
                    $this->response($list,'json');
                }
                $data = [
                    'uid' => $uid,
                    'own_funds' => $own_funds,
                    'with_funds' => $with_funds,
                    'time_limit' => 12,
                    'interest_rate' => 2.00,
                    'num'=>$data,
                    'types'=>2,
                    'aggre' => 1
                ];

                $mod = D('MainNeeds');
                if($orders = $mod->addData($data)){
                    $own_funds = intval($own_funds)*10000;//本金
                    D('MainFunds')->addData(-$own_funds,$uid,21);
                    //站内信
                    $title = '您成功提交了配资需求';
                    $user_con = time_format().'您成功提交了配资需求，等待审核，单号：'.$orders;
                    $admin_con = time_format().'用户成功提交了配资需求【股指吧】，用户名：'.$userinfo['username'].'，单号：'.$orders.'，<a href="'.C('HTTPHOST').'/admin.php/Needs/guzhi">立即查看</a>';
                    D('MessageNotices')->saveData($uid,$title,$user_con,1,$admin_con);
                    $list = [
                        'status'=> 0,
                        'msg'=>'配资成功'
                    ];
                    $this->response($list,'json');
                }else{
                    $list = [
                        'status'=> 10,
                        'msg'=>$mod->getError()
                    ];
                    $this->response($list,'json');
                }
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

    /**
     * 获取参数
     */
    private static function _getNeeds() {
        $needs = [
            'interstrate' => C('INTERSTRATE') ? C('INTERSTRATE') : 2.00,
            'warningline' => C('WARNINGLINE') ? C('WARNINGLINE') :  112,
            'openline' => C('OPENLINE') ? C('OPENLINE') : 108,
        ];
        return $needs;
    }

}