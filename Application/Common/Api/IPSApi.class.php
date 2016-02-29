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
class IPSApi {
	private $PostUrl;//提交地址
	private $Code;//商户号
	private $Key;//商户证书
	private $MerchantUrl;//支付结果成功返回的商户URL
	private $ServerUrl;//返回页面URL
	private $Attach;//商户数据包

	private $CurrencyType    = 'RMB';//币种
	private $GatewayType     = '01';//支付卡种
	private $Lang            = 'GB';//语言
	private $OrderEncodeType = 5;//订单支付接口加密方式
	private $RetEncodeType   = 17;//交易返回接口加密方式
	private $RetType         = 1;//返回方式
	private $FailUrl         = '';//支付结果失败返回的商户URL

	private $Amount;//订单金额(保留2位小数)
	private $DispAmount;//显示金额
	private $BillNo;//商户订单编号
	private $Date;//订单日期

	private $SignMD5;//加密参数

	function __construct($Amount, $DispAmount) {
		$this->Amount     = $Amount;
		$this->DispAmount = $DispAmount;

		$this->BillNo = $this->_getBillNo();
		$this->Date   = $this->_getDate();

		$this->PostUrl     = $this->_getConfig()['postUrl'];
		$this->Code        = $this->_getConfig()['code'];
		$this->Key         = $this->_getConfig()['key'];
        $this->MerchantUrl = $this->_getUrl();
		$this->ServerUrl   = $this->_getServerUrl();
		$this->Attach      = $this->_getConfig()['attach']?$this->_getConfig()['attach']:'';

		$this->SignMD5 = $this->_getSignMD5();
	}

	/**
	 * 获取数据
	 */
	public function postData() {
		$data = array(
            'PostUrl'         => $this->PostUrl,
			'Mer_code'        => $this->Code,
			'Billno'          => $this->BillNo,
			'Amount'          => $this->Amount,
			'Date'            => $this->Date,
			'Currency_Type'   => $this->CurrencyType,
			'Gateway_Type'    => $this->GatewayType,
			'Lang'            => $this->Lang,
			'Merchanturl'     => $this->MerchantUrl,
			'FailUrl'         => $this->FailUrl,
			'Attach'          => $this->Attach,
			'DispAmount'      => $this->DispAmount,
			'OrderEncodeType' => $this->OrderEncodeType,
			'RetEncodeType'   => $this->RetEncodeType,
			'Rettype'         => $this->RetType,
			'ServerUrl'       => $this->ServerUrl,
			'SignMD5'         => $this->SignMD5,
		);
		$back = D('MainRecord')->saveData($data['Amount'],$data['Billno'],'IPS');
        if ($back) {
            //站内信
            $title = '您通过环迅支付在线操作充值';
            $user_con = time_format() . '您通过环迅支付在线操作充值，金额：' . money($data['Amount']);
            $admin_con = time_format() . '用户通过环迅支付在线操作充值，用户名：' . USERNAME . '，金额：' . money($data['Amount']) . '，单号：' . $data['Billno'] . '，<a href="'.C('HTTPHOST').'/admin.php/Record/all">立即查看</a>';
            D('MessageNotices')->saveData(UID, $title, $user_con, 1, $admin_con);
            $html = <<<HTML
                <form action="{$data['PostUrl']}" method="post" id="frm">
                    <input type="hidden" name="Mer_code" value="{$data['Mer_code']}">
                    <input type="hidden" name="Billno" value="{$data['Billno']}">
                    <input type="hidden" name="Amount" value="{$data['Amount']}" >
                    <input type="hidden" name="Date" value="{$data['Date']}">
                    <input type="hidden" name="Currency_Type" value="{$data['Currency_Type']}">
                    <input type="hidden" name="Gateway_Type" value="{$data['Gateway_Type']}">
                    <input type="hidden" name="Lang" value="{$data['Lang']}">
                    <input type="hidden" name="Merchanturl" value="{$data['Merchanturl']}">
                    <input type="hidden" name="FailUrl" value="{$data['FailUrl']}">
                    <input type="hidden" name="ErrorUrl" value="{$data['ErrorUrl']}">
                    <input type="hidden" name="Attach" value="{$data['Attach']}">
                    <input type="hidden" name="DispAmount" value="{$data['DispAmount']}">
                    <input type="hidden" name="OrderEncodeType" value="{$data['OrderEncodeType']}">
                    <input type="hidden" name="RetEncodeType" value="{$data['RetEncodeType']}">
                    <input type="hidden" name="Rettype" value="{$data['Rettype']}">
                    <input type="hidden" name="ServerUrl" value="{$data['ServerUrl']}">
                    <input type="hidden" name="SignMD5" value="{$data['SignMD5']}">
                </form>
                <script language="javascript">
                    document.getElementById("frm").submit();
                </script>
HTML;
            return $html;
        } else {
            return '操作失败';
        }
//		if ($back) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $this->PostUrl);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
//            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
//            curl_setopt($ch, CURLOPT_HEADER, true);//启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
//
//            $result = curl_exec($ch);
//            curl_close($ch);
//            echo $result;
//
//		}

	}

    /**
	 * 获取配置参数
	 */
	private function _getConfig() {
		$config = unserialize(C('IPS'));
		return $config;
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

	/**
	 * 获取时间
	 */
	private function _getDate() {
		$date = date('Ymd');
		return $date;
	}

	/**
	 * 获取完整url
	 */
	private function _getUrl() {
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?'https://':'http://';
		$url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['merchantUrl']);
		return $url;
	}

    /**
     * 获取完整url
     */
    private function _getServerUrl() {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?'https://':'http://';
        $url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']).U($this->_getConfig()['serverUrl']);
        return $url;
    }

	/**
	 * 获取加密信息
	 */
	private function _getSignMD5() {
		$date = md5('billno'.$this->BillNo.'currencytype'.$this->CurrencyType.'amount'.$this->Amount.'date'.$this->Date.'orderencodetype'.$this->OrderEncodeType.$this->Key);
		return $date;
	}
}