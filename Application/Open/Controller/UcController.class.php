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
use Common\Api\WAPApi;
Class UcController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    public function login(){
        switch ($this->_method){
            case 'post': // post请求处理代码

                $username = $_POST['username']?$_POST['username']:'';
                $password = $_POST['password']?$_POST['password']:'';

                if(empty($username)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请填写用户名或手机号码'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(empty($password)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请填写登录密码'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                /* 调用UC登录接口登录 */
                $User = new UserApi;
                $uid  = $User->login(trim($username), $password,5);
                if($uid > 0){
                    /* 登录用户 */
                    $Member = D('Member');
                    if ($Member->login($uid)) {
                        $userinfo = M('UcenterMember')->field('id,username')->find($uid);
                        //$member = M('Member')->where(['uid'=>$uid])->field('img')->find();
                        $data = [
                            'status'=>true,
                            'info'=>[
                                'token'=> think_encrypt($userinfo['mobile']),
                                'uid' =>$userinfo['id'],
                                //'img'=> $member['img'],
                                'username'=>$userinfo['username']
                            ]
                        ];
                        $this->response($data,'json');
                        return;
                    }else{
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> $Member->getError()
                            ]
                        ];
                        $this->response($data,'json');
                        return;
                    }
                }else{
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
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> $error
                        ]
                    ];
                    $this->response($data,'json');
                }
                break;
        }
    }
    //发送验证码
    public function sendcode(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $mobile = $_POST['mobile']?trim($_POST['mobile']):'';
                if(empty($mobile)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请填写手机号码'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                switch ($this->checkMobile($mobile)) {
                    case -9:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '手机格式不正确'
                            ]
                        ];
                        $this->response($data,'json');
                        break;

                    case -10:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '手机禁止注册'
                            ]
                        ];
                        $this->response($data,'json');
                        break;

                    case -11:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '手机号被占用'
                            ]
                        ];
                        $this->response($data,'json');
                        break;
                }
                //处理验证码缓存
                $code = $this->sendMobileCode($mobile);
                if(!$code){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '验证码发送失败'
                        ]
                    ];
                    $this->response($data,'json');
                    break;
                }
                $data = [
                    'status'=>true
                ];
                $this->response($data,'json');
                break;
        }
    }
    //注册接口
    public function doregpost(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $mobile = $_POST['mobile']?trim($_POST['mobile']):'';
                $code = $_POST['code']?trim($_POST['code']):'';
                $username = $_POST['username']?_safe(trim($_POST['username'])):'';
                $password = $_POST['password']?_safe($_POST['password']):'';

                if(empty($mobile)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请填写手机号码'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                switch ($this->checkMobile($mobile)) {
                    case -9:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '手机格式不正确'
                            ]
                        ];
                        $this->response($data,'json');
                        break;

                    case -10:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '手机禁止注册'
                            ]
                        ];
                        $this->response($data,'json');
                        break;

                    case -11:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '手机号被占用'
                            ]
                        ];
                        $this->response($data,'json');
                        break;
                }
                if(empty($username)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请填写用户名'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                switch ($this->checkName($username)) {
                    case -1:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '用户名长度不合法'
                            ]
                        ];
                        $this->response($data,'json');
                        break;

                    case -2:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '用户名禁止注册'
                            ]
                        ];
                        $this->response($data,'json');
                        break;

                    case -3:
                        $data = [
                            'status'=>false,
                            'info'=>[
                                'msg'=> '用户名被占用'
                            ]
                        ];
                        $this->response($data,'json');
                        break;
                }

                if(empty($password)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请填写登录密码'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                if (NOW_TIME > session($mobile.'_mobile_expire')) {
                    $this->clearMobileCode($mobile);
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '验证码过期，请重新获取'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(session($mobile) != trim($code)){
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> '请正确填写验证码'
                        ]
                    ];
                    $this->response($data,'json');
                    return;
                }
                $uid = $this->register($username,$password,$mobile);
                if($uid > 0){
                    $data = [
                        'status'=>true,
                        'info'=>[
                            'username'=> $username
                        ]
                    ];
                }else{
                    $data = [
                        'status'=>false,
                        'info'=>[
                            'msg'=> $this->showRegError($uid)
                        ]
                    ];
                }
                $this->response($data,'json');
                break;
        }
    }

    public function dopwdpost(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                if(empty($uid)){
                    $data = [
                        'msg'=>'请登录后操作'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $userinfo = M('UcenterMember')->field('id,mobile,username')->find($uid);
                $mobile = $userinfo['mobile'];
                if(empty($mobile)){
                    $data = [
                        'msg'=>'确少手机号码信息'
                    ];
                    $this->response($data,'json');
                    return;
                }
                switch ($this->checkMobile($mobile)) {
                    case -9:
                        $data = [
                            'msg'=>'手机格式不正确'
                        ];
                        $this->response($data,'json');
                        break;
                }
                if($code = $this->sendMobileCode($mobile)){
                    $data = [
                        'regcode'=>$code,
                        'mobileTxt'=>substr($mobile,-4)
                    ];
                    $this->response($data,'json');
                }else{
                    $data = [
                        'msg'=>'短信发送失败'
                    ];
                    $this->response($data,'json');
                    return;
                }
                break;
        }
    }
    public function editpwd(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                if(empty($uid)){
                    $data = [
                        'msg'=>'请登录后操作'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $userinfo = M('UcenterMember')->field('id,mobile,username')->find($uid);
                $mobile = $userinfo['mobile'];
                $password = $_POST['password']?_safe($_POST['password']):'';
                $mobileCode = $_POST['code']?trim($_POST['code']):'';
                if (NOW_TIME > session($mobile.'_mobile_expire')) {
                    session($mobile, null);
                    session($mobile.'_mobile_expire', null);
                    $data = [
                        'msg'=>'验证码过期。请重新获取'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(session($mobile) != trim($mobileCode)){
                    $data = [
                        'msg'=>'请正确填写验证码'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $rules = array(
                    array('password', 'require', '重置密码必须填写'),
                    array('password', '6,15', '密码长度必须（6-15）之间，', 0, 'length')
                );
                $UC = M("UcenterMember");
                if ($UC->validate($rules)->create()) {
                    $User = new UserApi;
                    $data = array(
                        'password'      => trim($password)
                    );
                    if ($User->updatePwd($uid, $data)) {
                        $this->clearMobileCode($mobile);
                        $User->updateFields($uid, ['isold'=>0]);
                        //站内信
                        $title = '您成功修改了登录密码';
                        $user_con = time_format().'您成功修改了登录密码，请牢记';
                        D('MessageNotices')->saveData($uid,$title,$user_con);

                        $data = [
                            'uid'=>$uid,
                            'username'=>$userinfo['username']
                        ];
                        $this->response($data,'json');
                    } else {
                        $data = [
                            'msg'=>'操作失败'
                        ];
                        $this->response($data,'json');
                    }
                } else {
                    $data = [
                        'msg'=>$UC->getError()
                    ];
                    $this->response($data,'json');
                }
                break;
        }
    }
    public function editpaypwd(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                if(empty($uid)){
                    $data = [
                        'msg'=>'请登录后操作'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $userinfo = M('UcenterMember')->field('id,mobile,username')->find($uid);
                $mobile = $userinfo['mobile'];
                $password = $_POST['password']?_safe($_POST['password']):'';
                $mobileCode = $_POST['code']?trim($_POST['code']):'';
                if (NOW_TIME > session($mobile.'_mobile_expire')) {
                    session($mobile, null);
                    session($mobile.'_mobile_expire', null);
                    $data = [
                        'msg'=>'验证码过期。请重新获取'
                    ];
                    $this->response($data,'json');
                    return;
                }
                if(session($mobile) != trim($mobileCode)){
                    $data = [
                        'msg'=>'请正确填写验证码'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $rules = array(
                    array('password', 'require', '重置密码必须填写'),
                    array('password', '6,15', '密码长度必须（6-15）之间，', 0, 'length')
                );
                $UC = M("UcenterMember");
                if ($UC->validate($rules)->create()) {
                    $User = new UserApi;
                    $data = array(
                        'paypwd'      => trim($password),
                        'pass_paypwd' => 1
                    );
                    if ($User->savePaypwd($uid, true, $data, false)) {
                        $this->clearMobileCode($mobile);
                        $User->updateFields($uid, ['isold'=>0]);
                        //站内信
                        $title = '您成功设置了支付密码';
                        $user_con = time_format().'您成功设置了支付密码，请牢记';
                        D('MessageNotices')->saveData($uid,$title,$user_con);

                        $data = [
                            'uid'=>$uid,
                            'username'=>$userinfo['username']
                        ];
                        $this->response($data,'json');
                    } else {
                        $data = [
                            'msg'=>'操作失败'
                        ];
                        $this->response($data,'json');
                    }
                } else {
                    $data = [
                        'msg'=>$UC->getError()
                    ];
                    $this->response($data,'json');
                }
                break;
        }
    }
    public function setpwdqa(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                if(empty($uid)){
                    $data = [
                        'msg'=>'请登录后操作'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $userinfo = M('UcenterMember')->field('id,mobile,username,pass_paypwd')->find($uid);
                if(!$userinfo['pass_paypwd']){
                    $data = [
                        'msg'=>'请先设置支付密码'
                    ];
                    $this->response($data,'json');
                    return;
                }
                $qid = $_POST['qid']?intval($_POST['qid']):0;
                $answer = $_POST['answer']?_safe($_POST['answer']):'';
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                $rules = array(
                    array('qid', 'require', '请选择密保问题'),
                    array('answer', 'require', '密保答案必须填写'),
                    array('paypwd', 'require', '支付密码必须填写')
                );
                $UC = M("UcenterMember");
                if ($UC->validate($rules)->create()) {
                    //检查密码
                    if (!$this->checkPwd($uid,$paypwd,true)) {
                        $data = [
                            'msg'=>'支付密码错误'
                        ];
                        $this->response($data,'json');
                        return;
                    }
                    $User = new UserApi;
                    $data = array(
                        'pass_qa' => $qid,
                        'answer' => think_encrypt($answer)
                    );
                    if ($User->setFields($uid, $data)) {
                        //站内信
                        $title = '您成功设置了密保问题';
                        $user_con = time_format().'您成功设置了密保问题，请牢记';
                        D('MessageNotices')->saveData($uid,$title,$user_con);
                        $data = [
                            'uid'=>$uid,
                            'username'=>$userinfo['username']
                        ];
                        $this->response($data,'json');
                    } else {
                        $data = [
                            'msg'=>'操作失败'
                        ];
                        $this->response($data,'json');
                    }
                }else{
                    $data = [
                        'msg'=>$UC->getError()
                    ];
                    $this->response($data,'json');
                }

                break;
        }
    }
    public function realname(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $data['uid'] = $_POST['uid']?intval($_POST['uid']):0;
                $data['realname'] = $_POST['realname']?_safe($_POST['realname']):'';
                $data['card_number'] = $_POST['card_number']?_safe($_POST['card_number']):'';
                $data['card_front'] = $_POST['card_front']?intval($_POST['card_front']):0;
                $data['card_back'] = $_POST['card_back']?intval($_POST['card_back']):0;
                $mod = D('Userdata');
                if ($mod->create($data)) {
                    $user = M('UcenterMember')->field('id,username')->find($data['uid']);
                    //控制重新提交
                    $info = $mod->hasInfo($user['id']);
                    if (is_array($info)) {
                        $back = $mod->save();
                    } else {
                        $back = $mod->add();
                    }
                    if ($back) {
                        M('UcenterMember')->save(array('id' => $data['uid'], 'pass_realname' => -1,'realname'=>$data['realname']));
                    }
                    //站内信
                    $title = '您提交了实名认证';
                    $user_con = time_format().'您提交了实名认证，请等待审核';
                    $admin_con = time_format().'用户提交了实名认证，请及时处理，用户名：'.$user['username'].'，<a href="'.C('HTTPHOST').'/admin.php/Authentication/index">立即查看</a>';
                    D('MessageNotices')->saveData($user['id'],$title,$user_con,1,$admin_con);

                    $data = [
                        'uid'=>$user['id'],
                        'username'=>$user['username']
                    ];
                    $this->response($data,'json');
                } else {

                    $data = [
                        'msg'=>$mod->getError()
                    ];
                    $this->response($data,'json');
                }
                break;
        }
    }
    public function register($username,$password,$mobile){
        // 调用注册接口注册用户
        $User = new UserApi;
        $uid = $User->register($username, $password, '', $mobile);
        if (0 < $uid) {
            $userBack = array('uid' => $uid, 'nickname' => $username, 'status' => 1);
            M('Member')->add($userBack);
            $User->setFields($uid, 'pass_phone');
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
        }

        return $uid;
    }
    public function getUserInfo(){
        switch ($this->_method){
            case 'post':
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                if(empty($uid)){
                    $data = [
                        'msg'=>'没有登录，无法获取数据'
                    ];
                    $this->response($data,'json');
                }
                $userinfo = M('UcenterMember')->field('id,username,balance,mobile,pass_realname,custom_service')->find($uid);
                $userinfo['mobile'] = $userinfo['mobile']?hide_mobile($userinfo['mobile']):'手机号码';
                $userinfo['custom_service'] = $userinfo['custom_service']?$userinfo['custom_service']:'在线客服';
                $this->response($userinfo,'json');
                break;
        }
    }
    public function getRechargeUserInfo(){
        switch ($this->_method){
            case 'post':
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $bid = $_POST['bid']?intval($_POST['bid']):0;
                if(empty($uid)){
                    $data = [
                        'msg'=>'没有登录，无法获取数据'
                    ];
                    $this->response($data,'json');
                }
                $userinfo = M('UcenterMember')->field('id,username,balance,pass_realname,realname,mobile')->find($uid);
                if($userinfo[pass_realname]){
                    $userinfo['my_number'] = M('Userdata')->where(['uid'=>$uid])->getField('card_number');
                }
                $userinfo['pay_code'] = M('Banks')->where(['id'=>$bid])->getField('tags');
                $this->response($userinfo,'json');
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
     * 注册检查用户名
     * @author waco <etoupcom@126.com>
     */
    public function checkName($username) {
        $User = new UserApi;
        $back = $User->checkUsername($username);
        return $back;
    }
    /**
     * 检查手机号码
     * @author waco <etoupcom@126.com>
     */
    public function checkMobile($mobile) {
        $User = new UserApi;
        $back = $User->checkMobile($mobile);
        return $back;
    }
    /**
     * 发送短信验证码
     * @author waco <etoupcom@126.com>
     */
    public function sendMobileCode($mobile) {
        if (isset($mobile)) {
            $term = 5;
//            $tid  = C('SMSTAMA')?C('SMSTAMA'):1;
//            $code = getCode();
//            $back = sendSms($mobile, array($code, $term), $tid);
            //模拟验证码发送
            $code = 1234;
            $back['status'] = true;

            if ($back['status']) {
                $expire = (NOW_TIME+$term*60);
                session($mobile, $code);
                session($mobile.'_mobile_expire', $expire);
                return $code;
            } else {
                return false;
            }
        }
    }
    /**
     * 清除手机验证码
     */
    public function clearMobileCode($mobile) {
        session($mobile, null);
        session($mobile.'_mobile_expire', null);
    }

    /**
     * 验证密码
     * @param  string $password 密码
     * @param  integer $id 验证码ID
     * @return boolean     检测结果
     */
    public function checkPwd($uid,$password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser($uid, $password,$paypwd);
    }

}