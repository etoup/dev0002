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
	private $PostUrl;    //提交地址
	private $MemberID;   //商户ID
	private $TerminalID; //终端ID
    private $Key;        //商户密钥
	private $PageUrl;    //支付结果成功返回的商户URL
	private $ReturnUrl;  //返回页面URL

	private $OrderMoney; //订单金额(保留2位小数)
	private $TradeDate;  //订单日期 20150808080808
	private $TransID;    //订单号

	private $Md5Sign;    //加密参数

    private $KeyType             = 1;    //加密类型
    private $NoticeType          = 1;    //通知类型
    private $InterfaceVersion    = '4.0';//接口版本
    private $PayID               = '';   //功能ID
    private $MARK                = '|';    //通知类型
    private $AdditionalInfo      = 'WAP';

	function __construct($OrderMoney) {
		$this->OrderMoney  = $OrderMoney*100;

		$this->TransID     = $this->_getTransID();
		$this->TradeDate   = $this->_getDate();

		$this->PostUrl     = $this->_getConfig()['PostUrl'];
		$this->MemberID    = $this->_getConfig()['MemberID'];
        $this->TerminalID  = $this->_getConfig()['TerminalID'];
		$this->Key         = $this->_getConfig()['Key'];
        $this->PageUrl     = $this->_getPageUrl();
		$this->ReturnUrl   = $this->_getReturnUrl();

		$this->Signature = $this->_getMd5Sign();
	}

	/**
	 * 获取数据
	 */
	public function postData() {
		$data = array(
            'PostUrl'             => $this->PostUrl,
			'MemberID'            => $this->MemberID,
			'TerminalID'          => $this->TerminalID,
			'InterfaceVersion'    => $this->InterfaceVersion,
			'KeyType'             => $this->KeyType,
			'PayID'               => $this->PayID,
			'TradeDate'           => $this->TradeDate,
			'TransID'             => $this->TransID,
			'OrderMoney'          => $this->OrderMoney,
			'PageUrl'             => $this->PageUrl,
			'ReturnUrl'           => $this->ReturnUrl,
			'NoticeType'          => $this->NoticeType,
			'Signature'           => $this->Signature,
            'AdditionalInfo'      => $this->AdditionalInfo,
		);
        $OrderMoney = $data['OrderMoney']/100;
		$back = D('MainRecord')->saveData($OrderMoney,$data['TransID'],'WAP');
        if ($back) {
            //站内信
            $title = '您通过宝付移动支付在线操作充值';
            $user_con = time_format() . '您通过宝付移动支付在线操作充值，金额：' . money($OrderMoney);
            $admin_con = time_format() . '用户通过宝付移动支付在线操作充值，用户名：' . USERNAME . '，金额：' . money($OrderMoney) . '，单号：' . $data['TransID'] . '，<a href="'.C('HTTPHOST').'/admin.php/Record/all">立即查看</a>';
            D('MessageNotices')->saveData(UID, $title, $user_con, 1, $admin_con);
            session('OrderMoney',$this->OrderMoney); //设置提交金额的Session
            $html = <<<HTML
                <form action="{$data['PostUrl']}" method="post" id="frm">
                    <input type="hidden" name="MemberID" value="{$data['MemberID']}">
                    <input type="hidden" name="TerminalID" value="{$data['TerminalID']}">
                    <input type="hidden" name="InterfaceVersion" value="{$data['InterfaceVersion']}" >
                    <input type="hidden" name="KeyType" value="{$data['KeyType']}">
                    <input type="hidden" name="PayID" value="{$data['PayID']}">
                    <input type="hidden" name="TradeDate" value="{$data['TradeDate']}">
                    <input type="hidden" name="TransID" value="{$data['TransID']}">
                    <input type="hidden" name="OrderMoney" value="{$data['OrderMoney']}">
                    <input type="hidden" name="PageUrl" value="{$data['PageUrl']}">
                    <input type="hidden" name="ReturnUrl" value="{$data['ReturnUrl']}">
                    <input type="hidden" name="NoticeType" value="{$data['NoticeType']}">
                    <input type="hidden" name="Signature" value="{$data['Signature']}">
                    <input type="hidden" name="AdditionalInfo" value="{$data['AdditionalInfo']}">
                </form>
                <script language="javascript">
                    document.getElementById("frm").submit();
                </script>
HTML;
            return $html;
        } else {
            return '操作失败';
        }

	}

    /**
	 * 获取配置参数
	 */
	private function _getConfig() {
		$config = unserialize(C('WAP'));
		return $config;
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
	 * 获取加密信息
	 */
	private function _getMd5Sign() {
		$date = md5($this->MemberID.$this->MARK.$this->PayID.$this->MARK.$this->TradeDate.$this->MARK.$this->TransID.$this->MARK.$this->OrderMoney.$this->MARK.$this->PageUrl.$this->MARK.$this->ReturnUrl.$this->MARK.$this->NoticeType.$this->MARK.$this->Key);
		return $date;
	}
}