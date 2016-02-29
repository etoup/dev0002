<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Open\Controller;
use Think\Controller\RestController;
use User\Api\UserApi;
Class MyrechargeController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    /**
     * 充值
     */
    public function recharge() {
        $uid = $_POST['uid']?intval($_POST['uid']):0;
        $amount = I('post.amount', 0,'abs');
        $amount or $this->error('请填写充值金额');
        is_numeric($amount) or $this->error('金额只能是数字');
        $onlinetype = I('post.onlinetype','BAOFOO');

        switch($onlinetype){
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
                    $data['msg'] = '移动支付接口测试中，请填写小于10的数字';
                    $this->response($data,'json');
                }
                $acc_no = $this->trimall($acc_no);

                $version = '4.0.0.0';  //版本号
                $input_charset = 1;  //字符集
                $language = 1;  //语言种类
                $txn_type = '03311';  //交易类型
                $txn_sub_type = '01';  //交易子类
                $data_type = 'json';  //加密类型
                $biz_type = '0000';  //接入类型
                $id_card_type = '01'; //身份证类型

                $trans_id = $this->_getTransID();
                $trade_date = $this->_getDate();

                $post_url = $this->_getConfig()['PostUrl'];
                $terminal_id = $this->_getConfig()['TerminalID'];
                $member_id = $this->_getConfig()['MemberID'];
                $key = $this->_getConfig()['Key'];

                $page_url = $this->_getPageUrl();
                $return_url = $this->_getReturnUrl();
                $back_url = $this->_getBackUrl();


                $data_content_array = [
                    'txn_sub_type' => $txn_sub_type,
                    'biz_type'     => $biz_type,
                    'terminal_id'  => $terminal_id,
                    'member_id'    => $member_id,
                    'trans_id'     => $trans_id,
                    'pay_code'     => $pay_code,
                    'acc_no'       => $acc_no,
                    'id_card_type' => $id_card_type,
                    'id_card'      => $id_card,
                    'id_holder'    => $id_holder,
                    'mobile'       => $mobile,
                    'trade_date'   => $trade_date,
                    'txn_amt'      => $amount*100,
                    'page_url'     => $page_url,
                    'return_url'   => $return_url,
                    'back_url'     => $back_url
                ];
                $data_content_json = json_encode($data_content_array);
                $encode =mb_detect_encoding($data_content_json, "UTF-8,GB2312,GBK");
                if (trim($encode) == "UTF-8"){
                    $data_content_json = iconv("UTF-8","GBK",$data_content_json);
                }
                Vendor('Baofu.BaofooSdk');
                $path = VENDOR_PATH.'Baofu/cer/';
                $baofooSdk = new \BaofooSdk($member_id, $terminal_id,$data_type,$path.'bfkey_100000178@@100000916.pfx',$path.'bfkey_100000178@@100000916.cer',$key);
                $data_content = $baofooSdk->encryptedByPrivateKey($data_content_json);
                $OrderMoney = $amount*100/100;
                $userinfo = M('UcenterMember')->field('id,username')->find($uid);
                $back = D('MainRecord')->saveData($OrderMoney,$trans_id,$uid,$userinfo['username'],'WAP');
                if ($back) {
                    //站内信
                    $title = '您通过宝付移动支付在线操作充值';
                    $user_con = time_format() . '您通过宝付移动支付在线操作充值，金额：' . money($OrderMoney);
                    $admin_con = time_format() . '用户通过宝付移动支付在线操作充值，用户名：' . $userinfo['username'] . '，金额：' . money($OrderMoney) . '，单号：' . $trans_id . '，<a href="'.C('HTTPHOST').'/admin.php/Record/all">立即查看</a>';
                    D('MessageNotices')->saveData($uid, $title, $user_con, 1, $admin_con);
                    session('OrderMoney',$amount); //设置提交金额的Session
                    $data = [
                        'post_url' => $post_url,
                        'version' => $version,
                        'input_charset' => $input_charset,
                        'language' => $language,
                        'terminal_id' => $terminal_id,
                        'txn_type' => $txn_type,
                        'txn_sub_type' => $txn_sub_type,
                        'member_id' => $member_id,
                        'data_type' => $data_type,
                        'back_url' => $back_url,
                        'data_content' => $data_content,
                        'id_holder' => $id_holder,
                        'id_card' => $id_card,
                        'mobile' => $mobile
                    ];
                    $this->response($data,'json');
                }else{
                    $data['msg'] = '操作失败';
                    $this->response($data,'json');
                }
                break;
            default:
                $data['msg'] = '无效的支付方式';
                $this->response($data,'json');
        }
    }

    /**
     * 操作App充值
     */
    public function doBack(){
        Vendor('Baofu.BaofooSdk');
        $path = VENDOR_PATH.'Baofu/cer/';
        $baofooSdk = new \BaofooSdk($this->_getConfig()['MemberID'], $this->_getConfig()['TerminalID'],'json',$path.'bfkey_100000178@@100000916.pfx',$path.'bfkey_100000178@@100000916.cer',$this->_getConfig()['Key']);
        $endata_content = $_POST["data_content"];
        $endata_content = $baofooSdk->decryptByPublicKey($endata_content);
        switch($endata_content['resp_code']){
            case '0000':
                $data['msg'] = '操作成功';
                $this->response($data,'json');
                break;
            default:
                $data['msg'] = '操作失败';
                $this->response($data,'json');
        }
    }

    /**
     * 获取订单号
     */
    private function _getTransID() {
        $billNo = date('YmdHis').mt_rand(100000, 999999);
        $info   = M('MainRecord')->where(array('billno' => $billNo))->find();
        if (!empty($info)) {
            $this->_getTransID();
        }
        return $billNo;
    }

    /**
     * 获取时间
     */
    private function _getDate() {
        $date = date('Ymdhis');
        return $date;
    }

    /**
     * 获取配置参数
     */
    private function _getConfig() {
        $config = unserialize(C('WAP'));
        return $config;
    }
    /**
     * 获取完整url
     */
    private function _getPageUrl() {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?'https://':'http://';
        $url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['AppPageUrl']);
        return $url;
    }

    /**
     * 获取完整url
     */
    private function _getReturnUrl() {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?'https://':'http://';
        $url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['ReturnUrl']);
        return $url;
    }

    /**
     * 获取完整url
     */
    private function _getBackUrl() {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?'https://':'http://';
        $url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['BackAppUrl']);
        return $url;
    }
    //删除空格
    public function trimall($str)
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }
    /**
     * 验证是否设置支付密码
     * @return boolean     检测结果
     */
    private function _checkHasPwd($uid) {
        $info = M('UcenterMember')->find($uid);
        return $info['pass_paypwd'];
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
}