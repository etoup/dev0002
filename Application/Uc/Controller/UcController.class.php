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
use Common\Api\EmailApi;
use User\Api\UserApi;
/**
 * 用户中心公共控制器
 * @author waco <etoupcom@126.com>
 */
class UcController extends Controller {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
        define('ROOT',C('HTTPHOST'));
    }
    /**
     * 前台用户登录
     * @author waco <etoupcom@126.com>
     */
    public function login($username = null, $password = null, $code = null, $rememberme = null) {
        $mobile = I('get.mobile','');
        if (IS_POST) {
            if (C('CHECK_VERIFY_LOGIN')) {
                /* 检测验证码 TODO: */
                if (!check_verify($code)) {
                    ajaxMsg('验证码输入错误', false);
                }
            }
            $type = I('post.type',1,'int');
            /* 调用UC登录接口登录 */
            $User = new UserApi;
            $uid  = $User->login(trim($username), $password,$type);
            if (0 < $uid) {
                /* 登录用户 */
                $Member = D('Member');
                if ($Member->login($uid)) {//登录用户
                    switch($type){
                        case 3:
                            $username = M('Member')->where(['uid'=>$uid])->getField('nickname');
                            break;
                    }
                    $coder = authcode($username, 'ENCODE');
                    $rememberme && cookie('username', $coder, $rememberme);//记住账户
                    switch($mobile){
                        case 'weixin':
                            $backUrl = array('referer' => cookie('__forward__')?cookie('__forward__'):U('Uc/Index/index/mobile/weixin'));
                            break;
                        default:
                            $backUrl = array('referer' => cookie('__forward__')?cookie('__forward__'):U('Uc/Index/index'));
                    }
                    ajaxMsg($backUrl);
                } else {
                    ajaxMsg($Member->getError(), false);
                }

            } else {//登录失败
                switch ($uid) {
                    case -1:
                        $error = '用户不存在或被禁用！';
                        break;
                    //系统级别禁用
                    case -2:
                        $error = '密码错误！';
                        break;

                    default:
                        $error = '未知错误！';
                        break;
                    // 0-接口参数错误（调试阶段使用）
                }
                ajaxMsg($error, false);
            }
        } else {
            if (is_login()) {
                switch($mobile){
                    case 'weixin':
                        $this->redirect('Uc/Index/index/mobile/weixin');
                        break;
                    default:
                        $this->redirect('Index/Index/index');
                }

            } else {

                /* 读取数据库中的配置 */
                $config = S('DB_CONFIG_DATA');
                if (!$config) {
                    $config = api('Config/lists');
                    S('DB_CONFIG_DATA', $config);
                }
                C($config);//添加配置

                $username = I('get.username', '');

                $seo      = array(
                    'title' => '用户登录',
                );
                $this->seo      = $seo;
                $this->username = $username?$username:authcode(cookie('username'), 'DECODE');
                $this->new = I('get.new', '');
                switch($mobile){
                    case 'weixin':
                        $this->display('loginweixin');
                        break;
                    default:
                        $this->display();
                }
            }
        }
    }
    /**
     * 前台用户注册
     * @author waco <etoupcom@126.com>
     */
    public function register($username = '', $email = '', $emailCode = '', $mobile = '', $mobileCode = '', $password = '', $repassword = '', $verify = '') {
        if (!C('REGIST_CONTROL')) {
            ajaxMsg(C('CLOSEMSG'), false);
        }
        $mobiles = I('get.mobile','');
        if (is_login()) {
            switch($mobiles){
                case 'weixin':
                    $this->redirect('Uc/Index/index',['mobile'=>'weixin']);
                    break;
                default:
                    $this->redirect('Home/Index/index');
            }
        }
        if (IS_POST) {
            $username = trim($username);
            //必填项检查
            $this->checkName($username);
            if (C('ACTIVEPHONE')) {
                $this->checkMobile($mobile);
            }
            if($email)
                $this->checkEmail($email);

            //初始化状态
            $pass_phone = $pass_email = false;

            //检测验证码
//            if (C('CHECK_VERIFY_REG')) {
//                if (!check_verify($verify)) {
//                    ajaxMsg('验证码输入错误', false);
//                }
//            }

            //开启邮箱验证
            if (C('ACTIVEMAIL')) {
                $emailCode or ajaxMsg('平台开启邮箱验证，请立即验证', false);
            }

            //开启短信验证
            if (C('ACTIVEPHONE')) {
                $mobileCode or ajaxMsg('平台开启短信验证，请立即验证', false);
            }

            // 检测密码
            if ($password != $repassword) {
                ajaxMsg('密码和重复密码不一致', false);
            }

            // 检测邮箱验证码
            if (C('ACTIVEMAIL') && $emailCode) {
                if (NOW_TIME > session($username.'_email_expire')) {
                    session($username.'_email_code', null);
                    session($username.'_email_expire', null);
                    ajaxMsg('邮箱验证码过期，请重新获取', false);
                }
                (session($username.'_email_code') == trim($emailCode)) or ajaxMsg('邮箱验证码错误', false);
                $pass_email = true;
            }

            // 检测短信验证码
            if (C('ACTIVEPHONE') && $mobileCode) {
                if (NOW_TIME > session($mobile.'_mobile_expire')) {
                    session($mobile, null);
                    session($mobile.'_mobile_expire', null);
                    ajaxMsg('短信验证码过期，请重新获取', false);
                }
                (session($mobile) == trim($mobileCode)) or ajaxMsg('短信验证码错误', false);
                $pass_phone = true;
            }

            // 调用注册接口注册用户
            $User = new UserApi;
            $uid = $User->register($username, $password, $email, $mobile);

            if (0 < $uid) {
                $userBack = array('uid' => $uid, 'nickname' => $username, 'status' => 1);
                M('Member')->add($userBack);
                //开启注册审核
                if (C('ACTIVECHECK')) {
                    $User->setFields($uid, 'status', 0);
                    $backUrl = array('referer' => U('Uc/Uc/login'), 'message' => array('注册成功，请等待管理员审核'));
                    ajaxMsg($backUrl);
                    return;
                }
                //通过邮箱验证
                $pass_email and $User->setFields($uid, 'pass_email');
                //通过手机验证
                $pass_phone and $User->setFields($uid, 'pass_phone');
                //处理推广
                $tags = I('post.tags')?I('post.tags','',false):'';
                $cookieTags = cookie('tags');
                if($cookieTags == $tags){
                    $tags = $tags?$tags:$cookieTags;
                    if(!empty($tags)){
                        $prevUsername = think_decrypt($tags);
                        $prevInfo = M('Member')->where(['uid'=>$prevUsername])->find();
                        if(!empty($prevInfo)){
                            $map = [
                                'uid'=>$prevInfo['uid'],
                                'tid'=>$uid,
                                'username'=>$username,
                                'reg_time'=>NOW_TIME,
                                'remark'=>'用户推广',
                                'created_time'=>NOW_TIME
                            ];
                            M('Extension')->add($map);
                            //客户经理推广的客户直接为该客户的客户经理
                            switch($prevInfo['groupid']){
                                case 8:
                                    M('UcenterMember')->where(['id'=>$uid])->setField('custom_service',$prevInfo['nickname']);
                                    break;
                            }
                        }
                    }
                }

                //发送站内信
                switch(C('WELCOMETYPE')){
                    case 1:
                        //站内信
                        $user_con = C('WELCOMECONTENT');
                        $admin_con = time_format().'新用户注册，用户名：'.$username.'，<a href="'.C('HTTPHOST').'/admin.php/Authentication/index">立即查看</a>';
                        D('MessageNotices')->saveData($uid,C('WELCOMETITLE'),$user_con,1,$admin_con);
                        break;
                    case 2:
                        break;
                }
                cookie('tags',null);
                switch($mobiles){
                    case 'weixin':
                        $backUrl = array('referer' => U('Uc/Uc/login/mobile/weixin', array('username' => $username,'new'=>'yes')), 'message' => array('注册成功，请立即登录'));
                        break;
                    default:
                        $backUrl = array('referer' => U('Uc/Uc/login', array('username' => $username,'new'=>'yes')), 'message' => array('注册成功，请立即登录'));
                }

                ajaxMsg($backUrl);

            } else {//注册失败，显示错误信息
                ajaxMsg($this->showRegError($uid), false);
            }

        } else {//显示注册表单
            $tags = I('get.tags','',false)?I('get.tags','',false):cookie('tags');
            if($tags){
                $this->tags = $tags;
            }
            $seo = array(
                'title' => '用户注册',
            );
            $this->seo = $seo;
            switch($mobiles){
                case 'weixin':
                    $this->display('registerweixin');
                    break;
                default:
                    $this->display();
            }
        }
    }

    /**
     * 忘记密码
     * @author waco <etoupcom@126.com>
     */
    public function forgotPwd($username = '', $mobile = '', $mobileCode = '', $password = '', $repassword = '') {
        $mobiles = I('get.mobile','');
        if (is_login()) {
            switch($mobiles){
                case 'weixin':
                    $this->redirect('Uc/Index/index');
                    break;
                default:
                    $this->redirect('Home/Index/index');
            }
        }
        if (IS_POST) {
            //检查必填项
            $username or ajaxMsg('用户名不能为空', false);
            $mobile or ajaxMsg('手机号码不能为空', false);
            $mobileCode or ajaxMsg('手机验证码不能为空', false);
            $password or ajaxMsg('新密码不能为空', false);
            $repassword or ajaxMsg('重复密码不能为空', false);
            //检测密码
            if ($password != $repassword) {
                ajaxMsg('密码和重复密码不一致', false);
            }
            //用户名+手机 获取用户信息
            $User = new UserApi;
            $info = $User->userInfo($username, true);
            if (empty($info)) {
                ajaxMsg('用户不存在或被禁用', false);
            }
            if ($info['mobile'] != $mobile) {
                ajaxMsg('手机号码错误', false);
            }
            //验证短信验证码
            if (NOW_TIME > session($mobile.'_mobile_expire')) {
                session($mobile, null);
                session($mobile.'_mobile_expire', null);
                ajaxMsg('短信验证码过期，请重新获取', false);
            }
            (session($mobile) == trim($mobileCode)) or ajaxMsg('短信验证码错误', false);
            //重置密码
            $data = array(
                'password' => trim($password)
            );
            $back = $User->updatePwd($info['id'], $data);
            if ($back) {
                $User->updateFields($info['id'], ['isold'=>0]);
                //站内信
                $title = '您的登录密码重置成功';
                $user_con = time_format().'您成功重置了登录密码';
                D('MessageNotices')->saveData(UID,$title,$user_con);
                switch($mobiles){
                    case 'weixin':
                        $backUrl = array('referer' => U('Uc/Uc/login/mobile/weixin', array('username' => $username)), 'message' => array('操作成功，请重新登录'));
                        break;
                    default:
                        $backUrl = array('referer' => U('Uc/Uc/login', array('username' => $username)), 'message' => array('操作成功，请重新登录'));
                }
                ajaxMsg($backUrl);
            } else {
                ajaxMsg('操作失败', false);
            }
        } else {
            $seo = array(
                'title' => '找回密码',
            );
            $this->seo = $seo;
            switch($mobiles){
                case 'weixin':
                    $this->display('forgotpwdweixin');
                    break;
                default:
                    $this->display();
            }
        }

    }

    /**
     * 发送邮件验证码
     * @author waco <etoupcom@126.com>
     */
    public function sendEmailCode() {
        list(
            $username,
            $email
            ) = array(
            I('post.username', ''),
            I('post.email', '')
        );
        $this->checkName($username);
        $this->checkEmail($email);
        $title          = C('ACTIVETITLE');
        $this->sitename = C('WEB_SITE_TITLE');
        $this->username = $username;
        $this->code     = getCode();
        $this->time     = date('Y-m-d H:i', NOW_TIME);
        $this->email    = $email;
        $content        = $this->fetch(T('Tpl/emailCode'));
        $back           = EmailApi::sendHtmlMail($email, $title, $content);
        if ($back['status']) {
            // 1小时过期
            $expire = (NOW_TIME+60*60);
            session($username.'_email_code', $this->code);
            session($username.'_email_expire', $expire);
            ajaxMsg('发送成功，请注意查收');
        } else {
            ajaxMsg($back['msg'], false);
        }
    }

    /**
     * 发送短信验证码
     * @author waco <etoupcom@126.com>
     */
    public function sendMobileCode() {
        if (IS_POST) {

            $mobile = I('post.mobile', '');
            $mobile or ajaxMsg('请填写手机号码', false);
            check_mobile($mobile) or ajaxMsg('请正确填写手机号码', false);
            $term = 5;
            //$tid  = C('SMSTAMA')?C('SMSTAMA'):1;
            //$code = getCode();
            //$back = sendSms($mobile, array($code, $term), $tid);
            $code = '1234';
            $back['status'] = true;

            if ($back['status']) {
                // 5分钟过期
                $expire = (NOW_TIME+$term*60);
                session($mobile, $code);
                session($mobile.'_mobile_expire', $expire);
                ajaxMsg('发送成功');
            } else {
                ajaxMsg('网络错误，请重新填写图形验证码后获取语音验证码', false);
            }
        } else {
            ajaxMsg('非法操作', false);
        }
    }

    /**
     * 发送语音验证码
     * @author waco <etoupcom@126.com>
     */
    public function sendMobileVoiceCode() {
        if (IS_POST) {
            $verify = I('post.verify', '');
            if(empty($verify)){
                ajaxMsg('请填写验证码!',false);
            }else{
                if (!check_verify($verify)) {
                    ajaxMsg('验证码输入错误', false);
                }
            }

            $mobile = I('post.mobile', '');
            $mobile or ajaxMsg('请填写手机号码', false);
            check_mobile($mobile) or ajaxMsg('请正确填写手机号码', false);
            $times = 3;
            $term =5;
            $num  = '4008738666';
            $code = getCode();
            $back = voiceVerify($code, $times, $mobile, $num, ROOT);

            if ($back) {
                // 5分钟过期
                $expire = (NOW_TIME+$term*60);
                session($mobile, $code);
                session($mobile.'_mobile_expire', $expire);
                ajaxMsg($back['msg']);
            } else {
                ajaxMsg('请确认手机信号强度并关闭拦截后重新获取', false);
            }
        } else {
            ajaxMsg('非法操作', false);
        }
    }

    /**
     * 检查图形验证码
     * @author waco <etoupcom@126.com>
     */
    public function checkVerifyBack(){
        if (IS_POST) {
            if (C('CHECK_VERIFY_REG')) {
                $verify = I('post.verify', '');
                if(empty($verify)){
                    ajaxMsg('请填写图形验证码!',false);
                }else{
                    if (!check_verify($verify)) {
                        ajaxMsg('图形验证码输入错误', false);
                    }else{
                        ajaxMsg('图形验证码验证通过');
                    }
                }
            }
        } else {
            ajaxMsg('非法操作', false);
        }
    }

    /**
     * 前台用户登出
     * @author waco <etoupcom@126.com>
     */
    public function logout() {
        $mobile = I('get.mobile','');
        if (is_login()) {
            D('Member')->logout();
            session('[destroy]');
            switch($mobile){
                case 'weixin':
                    $this->success('退出成功！', U('login',['mobile'=>'weixin']));
                    break;
                default:
                    $this->success('退出成功！', U('login'));
            }
        } else {
            switch($mobile){
                case 'weixin':
                    $this->redirect('login/mobile/weixin');
                    break;
                default:
                    $this->redirect('login');
            }
        }
    }

    /**
     * 登录检查用户名
     * @author waco <etoupcom@126.com>
     */
    public function hasName($username) {
        $User = new UserApi;
        $back = $User->info($username, true);
        if (is_array($back)) {
            return ajaxMsg('存在该用户');
        } else {
            return ajaxMsg('用户不存在或被禁用', false);
        }

    }

    /**
     * 检查支付返回状态（异步）
     * @author waco <etoupcom@126.com>
     */
    public function dopay(){
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
        //MD5签名格式
        $WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$Md5key);
        if ($Md5Sign == $WaitSign) {
            //校验通过开始处理订单
            //查找是否存在
            $info = M('MainRecord')->where(array('billno' => $TransID))->find();
            if($info){
                //修改状态
                $data = array(
                    'status'      => 1,
                    'info'        => serialize(I('request.')),
                    'update_time' => NOW_TIME
                );
                $back = M('MainRecord')->where(array('id' => $info['id'],'status'=>0))->save($data);
                if ($back) {
                    //操作资金日志
                    D('MainFunds')->addData($FactMoney/100, 1, '', $back,$info['uid']);
                    //站内信
                    $title = '您在线充值成功';
                    $user_con = time_format() . '您在线充值成功，金额：' . money($FactMoney/100) . '，单号：' . $TransID;
                    $admin_con = time_format() . '用户在线充值成功，用户名：' . $info['username'] . '，金额：' . money($FactMoney/100) . '，单号：' . $TransID . '，<a href="'.C('HTTPHOST').'/admin.php/Record/all">立即查看</a>';
                    D('MessageNotices')->saveData($info['uid'], $title, $user_con, 1, $admin_con);
                }
            }
        }
    }
    public function dowappay(){
        Vendor('Baofu.BaofooSdk');
        $path = VENDOR_PATH.'Baofu/cer/';
        $baofooSdk = new \BaofooSdk($this->_getWapConfig()['MemberID'], $this->_getWapConfig()['TerminalID'],'json',$path.'bfkey_100000178@@100000916.pfx',$path.'bfkey_100000178@@100000916.cer',$this->_getWapConfig()['Key']);
        $endata_content = $_POST["data_content"];
        $endata_content = $baofooSdk->decryptByPublicKey($endata_content);
        switch($endata_content['resp_code']){
            case '0000':
                //查找是否存在
                $TransID = $endata_content['trans_no'];
                $info = M('MainRecord')->where(array('billno' => $TransID))->find();
                if($info){
                    //修改状态
                    $data = array(
                        'status'      => 1,
                        'info'        => serialize(I('request.')),
                        'update_time' => NOW_TIME
                    );
                    $back = M('MainRecord')->where(array('id' => $info['id'],'status'=>0))->save($data);
                    if ($back) {
                        //操作资金日志
                        D('MainFunds')->addData($endata_content['succ_amt']/100, 1, '', $back,$info['uid']);
                        //站内信
                        $title = '您在线充值成功';
                        $user_con = time_format() . '您在线充值成功，金额：' . money($endata_content['succ_amt']/100) . '，单号：' . $TransID;
                        $admin_con = time_format() . '用户在线充值成功，用户名：' . $info['username'] . '，金额：' . money($endata_content['succ_amt']/100) . '，单号：' . $TransID . '，<a href="'.C('HTTPHOST').'/admin.php/Record/all">立即查看</a>';
                        D('MessageNotices')->saveData($info['uid'], $title, $user_con, 1, $admin_con);
                        echo "OK";
                    }
                }
                break;
            default:
                $this->error('操作失败',U('index',['mobile'=>'weixin']));
        }
    }

    /**
     * 获取配置参数
     */
    private function _getConfig() {
        $config = unserialize(C('BAOFOO'));
        return $config;
    }

    /**
     * 检查验证码
     * @author waco <etoupcom@126.com>
     */
    public function checkVerify($code) {
        if (check_verify($code)) {
            ajaxMsg('验证码输入错误', false);
        }
        ajaxMsg('验证码正确');
    }

    /**
     * 注册检查用户名
     * @author waco <etoupcom@126.com>
     */
    public function checkName($username) {
        if (empty($username)) {
            return ajaxMsg('用户名不能为空', false);
        }
        $User = new UserApi;
        $back = $User->checkUsername($username);
        switch ($back) {
            case -1:
                return ajaxMsg('用户名长度不合法', false);
                break;

            case -2:
                return ajaxMsg('用户名禁止注册', false);
                break;

            case -3:
                return ajaxMsg('用户名被占用', false);
                break;
        }
    }

    /**
     * 检查邮箱地址
     * @author waco <etoupcom@126.com>
     */
    public function checkEmail($email) {
        if (empty($email)) {
            return ajaxMsg('邮箱地址不能为空', false);
        }
        $User = new UserApi;
        $back = $User->checkEmail($email);
        switch ($back) {
            case -5:
                return ajaxMsg('邮箱格式不正确', false);
                break;

            case -6:
                return ajaxMsg('邮箱长度不合法', false);
                break;

            case -7:
                return ajaxMsg('邮箱禁止注册', false);
                break;

            case -8:
                return ajaxMsg('邮箱被占用', false);
                break;
        }
    }

    /**
     * 检查手机号码
     * @author waco <etoupcom@126.com>
     */
    public function checkMobile($mobile) {
        if (empty($mobile)) {
            return ajaxMsg('手机号码不能为空', false);
        }
        $User = new UserApi;
        $back = $User->checkMobile($mobile);
        switch ($back) {
            case -9:
                return ajaxMsg('手机格式不正确', false);
                break;

            case -10:
                return ajaxMsg('手机禁止注册', false);
                break;

            case -11:
                return ajaxMsg('手机号被占用', false);
                break;
        }
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0) {
        switch ($code) {
            case -1:$error = '用户名长度必须在16个字符以内！';
                break;

            case -2:$error = '用户名被禁止注册！';
                break;

            case -3:$error = '用户名被占用！';
                break;

            case -4:$error = '密码长度必须在6-30个字符之间！';
                break;

            case -5:$error = '邮箱格式不正确！';
                break;

            case -6:$error = '邮箱长度必须在1-32个字符之间！';
                break;

            case -7:$error = '邮箱被禁止注册！';
                break;

            case -8:$error = '邮箱被占用！';
                break;

            case -9:$error = '手机格式不正确！';
                break;

            case -10:$error = '手机被禁止注册！';
                break;

            case -11:$error = '手机号被占用！';
                break;

            case -12:$error = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
                break;

            default:$error = '未知错误';
        }
        return $error;
    }

    /**
     * 检查密码
     * @author waco <etoupcom@126.com>
     */
    public function checkPwd($pwd) {
        $User = new UserApi;
        $back = $User->checkPwd($pwd, 4);
        switch ($back) {
            case -4:
                return ajaxMsg('密码长度'.C('PASSWORDMIN').'-'.C('PASSWORDMAX').'个字符', false);
                break;

            default:
                $data['message']['rank'] = checkPwdStrong($pwd);
                return ajaxMsg($data);
                break;
        }
    }
    /**
     * 检查密码强度
     * @author waco <etoupcom@126.com>
     */
    public function checkPwdStrong($pwd) {
        $data['message']['rank'] = checkPwdStrong($pwd);
        ajaxMsg($data);
    }

    /**
     * 检查手机短信验证码
     * @author waco <etoupcom@126.com>
     */
    public function checkMobileCode($mobile, $mobileCode) {
        if (NOW_TIME > session($mobile.'_expire')) {
            session($mobile, null);
            session($mobile.'_expire', null);
            ajaxMsg('验证码过期，请重新获取', false);
        }
        if (session($mobile) == $mobileCode) {
            ajaxMsg('验证码正确');
        } else {
            ajaxMsg('验证码错误', false);
        }
    }

    public function verify() {
        $config =    array(
            'fontSize'    =>    20,    // 验证码字体大小
            'imageH'      =>    35,
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
        );
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }
}