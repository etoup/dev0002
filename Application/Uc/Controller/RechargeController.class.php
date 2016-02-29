<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;

use Common\Api\BAOFOOApi;
use Common\Api\WAPApi;
/**
 * 充值管理控制器
 */

class RechargeController extends UcenterController {

	/**
	 * 充值管理
	 */
	public function index() {
        $amount = I('request.amount','');
        if($amount)
            is_numeric($amount) or $this->error('金额只能是数字');
        $this->amount = $amount;
        $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
        $this->balance = $balance;
        $this->interface = C('PAYINTERFACE');
        $interfaceval = C('PAYINTERFACEVAL');
        $this->interfacevalarr = empty($interfaceval) ? array() : explode(',',$interfaceval);
		$this->seo = ['title' => '我要充值'];
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->id_holder = M('UcenterMember')->where(['id'=>UID])->getField('realname');
                $this->id_card = M('Userdata')->where(['uid'=>UID])->getField('card_number');
                $this->mobile = M('UcenterMember')->where(['id'=>UID])->getField('mobile');
                $this->display('indexweixin');
                break;
            default:
                $this->display();
        }
	}

    /**
     * 充值
     */
    public function recharge() {
        $amount = I('post.amount', 0,'abs');
        $amount or $this->error('请填写充值金额');
        is_numeric($amount) or $this->error('金额只能是数字');
        $onlinetype = I('post.onlinetype','BAOFOO');
        switch($onlinetype){
            case 'IPS':
                $amount = number_format($amount, 2, '.', '');
                $IPSApi = new IPSApi($amount, $amount);
                echo $IPSApi->postData();
                break;
            case 'BAOFOO':
                $amount = number_format($amount, 2, '.', '');
                $BAOFOOApi = new BAOFOOApi($amount, $amount);
                echo $BAOFOOApi->postData();
                break;
            case 'WAP':
                $pay_code = I('post.pay_code','');
                $pay_code or $this->error('请选择银行');
                $acc_no = I('post.acc_no','');
                $acc_no or $this->error('请填写银行卡号');
                $id_holder = I('post.id_holder','');
                $id_holder or $this->error('请填写持卡人姓名');
                $id_card = I('post.id_card','');
                $id_card or $this->error('请填写持卡人身份证号码');
                $mobile = I('post.mobile','');
                $mobile or $this->error('请填写银行卡预留手机号');
                is_numeric($mobile) or $this->error('手机号只能是数字');
                $amount = number_format($amount, 2, '.', '');
                if($amount>10){
                    $this->error('移动支付接口测试中，请填写小于10的数字');
                }
                $WAPApi = new WAPApi($amount, $pay_code,$this->trimall($acc_no),$id_holder,$id_card,$mobile);
                echo $WAPApi->postData();
                break;
            default:
                $this->error('无效的支付方式');
        }
    }
    //删除空格
    public function trimall($str)
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }

    /**
     * 操作充值
     */
    public function doBack(){
        list(
            $MemberID,        //商户ID
            $TerminalID,      //商户终端ID
            $TransID,         //商户流水号
            $Result,          //支付结果
            $ResultDesc,      //支付结果描述
            $FactMoney,       //实际成功金额
            $AdditionalInfo,  //订单附加消息
            $SuccTime,        //支付完成时间
            $Md5Sign,         //md5签名
            $Md5key,          //md5密钥（KEY）
            $MARK             //分隔符
            ) = array(
            I('request.MemberID'),
            I('request.TerminalID'),
            I('request.TransID'),
            I('request.Result'),
            I('request.ResultDesc'),
            I('request.FactMoney'),
            I('request.AdditionalInfo'),
            I('request.SuccTime'),
            I('request.Md5Sign'),
            $this->_getConfig()['Key'],
            '~|~'
        );

        $TransID or $this->error('无效订单', U('recharge'));
        //MD5签名格式
        $WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$Md5key);
        $OrderMoney = session('OrderMoney');
        if($OrderMoney){
            $OrderMoney = $OrderMoney;//获取提交金额的Session
        }

        if($Md5Sign == $WaitSign){
            //校验通过开始处理订单
            if($OrderMoney == $FactMoney){
                //卡面金额与用户提交金额一致
//                switch($AdditionalInfo){
//                    case 'WAP':
//                        $backUrl = cookie('__forward__')?cookie('__forward__'):U('log',['mobile'=>'weixin']);
//                        break;
//                    default:
//                        $backUrl = cookie('__forward__')?cookie('__forward__'):U('log');
//                }
                $backUrl = cookie('__forward__')?cookie('__forward__'):U('log');
                $this->success('充值成功', $backUrl);
            }else{
                $this->error('实际成交金额与您提交的订单金额不一致', U('index'));//实际成交金额与商户提交的订单金额不一致
            }
        }else{
            $this->error('签名不正确', U('index'));
        }
    }

    /**
     * 操作wap充值
     */
    public function doWapBack(){
        Vendor('Baofu.BaofooSdk');
        $path = VENDOR_PATH.'Baofu/cer/';
        $baofooSdk = new \BaofooSdk($this->_getWapConfig()['MemberID'], $this->_getWapConfig()['TerminalID'],'json',$path.'bfkey_100000178@@100000916.pfx',$path.'bfkey_100000178@@100000916.cer',$this->_getWapConfig()['Key']);
        $endata_content = $_POST["data_content"];
        $endata_content = $baofooSdk->decryptByPublicKey($endata_content);
        switch($endata_content['resp_code']){
            case '0000':
                $backUrl = cookie('__forward__')?cookie('__forward__'):U('log',['mobile'=>'weixin']);
                $this->success('充值成功', $backUrl);
                break;
            default:
                $this->error('操作失败',U('index',['mobile'=>'weixin']));
        }
    }
    /**
     * 获取配置参数
     */
    private function _getWapConfig() {
        $config = unserialize(C('WAP'));
        return $config;
    }

    /**
     * 获取配置参数
     */
    private function _getConfig() {
        $config = unserialize(C('BAOFOO'));
        return $config;
    }

    /**
     * 银行转账
     */
    public function bank(){
        if(IS_POST){
            list(
                $amount,
                $serialNumber
                )=[
                I('post.amount',0),
                _safe(I('post.serialNumber',''))
            ];
            $amount or ajaxMsg('请填写转账金额',false);
            is_numeric($amount) or ajaxMsg('金额只能是数字',false);
            $serialNumber or ajaxMsg('请填写银行流水号',false);
            $billno = $this->_getBillNo();
            $info = [
                'serialNumber' => $serialNumber,
                'billno'=>$billno
            ];
            $back = D('MainRecord')->saveData($amount,$billno,'银行转账',serialize($info),1);
            if($back){
                $mtype = I('get.mobile');
                switch($mtype){
                    case 'weixn':
                        $backUrl = U('Uc/Recharge/log',['mobile'=>'weixin']);
                        break;
                    default:
                        $backUrl = U('Uc/Recharge/log');
                }
                //站内信
                $title = '您成功提交银行转账信息';
                $user_con = time_format().'您成功提交银行转账信息，等待审核，金额：'.money($amount);
                $admin_con = time_format().'用户提交银行转账信息，等待审核，用户名：'.USERNAME.'，金额：'.money($amount).'，<a href="'.C('HTTPHOST').'/admin.php/Record/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);

                $back = array('message'=>['操作成功，等待审核'],'referer' => $backUrl);
                ajaxMsg($back);
            }else {
                ajaxMsg('操作失败',false);
            }
        }else{
            $balance = M('UcenterMember')->where(['id'=>UID])->getField('balance');
            $this->balance = $balance;
            $this->seo = ['title' => '我要充值'];
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('bankweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 充值记录
     */
    public function log(){
        $map = ['uid'=>UID];
        $list = $this->lists('MainRecord',$map);
        int_to_string($list);
        $this->_list = $list;
        $this->seo = ['title' => '充值记录'];
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
     * 获取订单号
     */
    private function _getBillNo() {
        $billNo = date('YmdHis').mt_rand(100000, 999999);
        $info   = M('MainRecord')->where(array('billno' => $billNo))->find();
        if (!empty($info)) {
            $this->_getBillNo();
        }
        return $billNo;
    }
}
