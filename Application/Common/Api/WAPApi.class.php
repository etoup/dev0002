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
class WAPApi {
	private $version = '4.0.0.0';  //版本号
    private $input_charset = 1;  //字符集
    private $language = 1;  //语言种类
    private $txn_type = '03311';  //交易类型
    private $txn_sub_type = '01';  //交易子类
    private $data_type = 'json';  //加密类型
    private $biz_type = '0000';  //接入类型
    private $id_card_type = '01'; //身份证类型

    private $post_url;  //提交地址
    private $terminal_id;  //终端号
    private $member_id;  //商户号
    private $key;  //商户密钥

    private $page_url;  //页面通知地址
    private $return_url;  //服务器通知地址
    private $back_url;  //商户返回页面地址

    private $txn_amt;  //充值金额（单位：分）
    private $pay_code;  //银行编码
    private $acc_no;  //银行卡号
    private $id_holder;  //持卡人姓名
    private $id_card;  //身份证号码
    private $mobile;  //开户预留手机号

    private $trans_id;  //订单号
    private $trade_date;  //订单时间

    function __construct($txn_amt,$pay_code,$acc_no,$id_holder,$id_card,$mobile) {
        $this->txn_amt = $txn_amt * 100;
        $this->pay_code = $pay_code;
        $this->acc_no = trim($acc_no);
        $this->id_holder = $id_holder;
        $this->id_card = $id_card;
        $this->mobile = $mobile;

        $this->trans_id = $this->_getTransID();
        $this->trade_date = $this->_getDate();

        $this->post_url = $this->_getConfig()['PostUrl'];
        $this->terminal_id = $this->_getConfig()['TerminalID'];
        $this->member_id = $this->_getConfig()['MemberID'];
        $this->key = $this->_getConfig()['Key'];

        $this->page_url = $this->_getPageUrl();
        $this->return_url = $this->_getReturnUrl();
        $this->back_url = $this->_getBackUrl();
    }

    public function postData(){
        $data_content_array = [
            'txn_sub_type' => $this->txn_sub_type,
            'biz_type'     => $this->biz_type,
            'terminal_id'  => $this->terminal_id,
            'member_id'    => $this->member_id,
            'trans_id'     => $this->trans_id,
            'pay_code'     => $this->pay_code,
            'acc_no'       => $this->acc_no,
            'id_card_type' => $this->id_card_type,
            'id_card'      => $this->id_card,
            'id_holder'    => $this->id_holder,
            'mobile'       => $this->mobile,
            'trade_date'   => $this->trade_date,
            'txn_amt'      => $this->txn_amt,
            'page_url'     => $this->page_url,
            'return_url'   => $this->return_url,
            'back_url'     => $this->back_url
        ];
        $data_content_json = json_encode($data_content_array);
        $encode =mb_detect_encoding($data_content_json, "UTF-8,GB2312,GBK");
        if (trim($encode) == "UTF-8"){
            $data_content_json = iconv("UTF-8","GBK",$data_content_json);
        }
        Vendor('Baofu.BaofooSdk');
        $path = VENDOR_PATH.'Baofu/cer/';
        $baofooSdk = new \BaofooSdk($this->member_id, $this->terminal_id,$this->data_type,$path.'bfkey_100000178@@100000916.pfx',$path.'bfkey_100000178@@100000916.cer',$this->key);
        $data_content = $baofooSdk->encryptedByPrivateKey($data_content_json);
        $OrderMoney = $this->txn_amt/100;
        $back = D('MainRecord')->saveData($OrderMoney,$this->trans_id,'WAP');
        if ($back) {
            //站内信
            $title = '您通过宝付移动支付在线操作充值';
            $user_con = time_format() . '您通过宝付移动支付在线操作充值，金额：' . money($OrderMoney);
            $admin_con = time_format() . '用户通过宝付移动支付在线操作充值，用户名：' . USERNAME . '，金额：' . money($OrderMoney) . '，单号：' . $this->trans_id . '，<a href="'.C('HTTPHOST').'/admin.php/Record/all">立即查看</a>';
            D('MessageNotices')->saveData(UID, $title, $user_con, 1, $admin_con);
            session('OrderMoney',$this->txn_amt); //设置提交金额的Session
            $html = <<<HTML
                <form action="{$this->post_url}" method="post" id="frm">
                    <input type="hidden" name="version" value="{$this->version}">
                    <input type="hidden" name="input_charset" value="{$this->input_charset}">
                    <input type="hidden" name="language" value="{$this->language}" >
                    <input type="hidden" name="terminal_id" value="{$this->terminal_id}">
                    <input type="hidden" name="txn_type" value="{$this->txn_type}">
                    <input type="hidden" name="txn_sub_type" value="{$this->txn_sub_type}">
                    <input type="hidden" name="member_id" value="{$this->member_id}">
                    <input type="hidden" name="data_type" value="{$this->data_type}">
                    <input type="hidden" name="back_url" value="{$this->back_url}">
                    <input type="hidden" name="data_content" value="{$data_content}">
                </form>
                <script language="javascript">
                    document.getElementById("frm").submit();
                </script>
HTML;
            return $html;
        }else{
            return '操作失败';
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
        $url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['PageUrl']);
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
        $url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['BackUrl']);
        return $url;
    }

}