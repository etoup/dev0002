<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;
use Common\Api\EmailApi;
use User\Api\UserApi;
/**
 * 密码管理控制器
 */

class PasswordController extends UcenterController {
    private static $mobile_code = 'mobile_code_';//定义短信验证码标识
    private static $mobile_do = 'mobile_do_';//定义短信发送时间标识
    private static $mobile_expire = 'mobile_expire_';//定义短信验证码过期时间标识

	/**
	 * 密码管理
	 */
	public function index() {
		Cookie('__forward__', $_SERVER['REQUEST_URI']);
		$this->seo = ['title' => '密码管理'];
		$this->display();
	}

    /**
     * 支付密码
     */
    public function paypwd(){
        $this->seo = ['title' => '支付密码'];
        $this->crumbs = $this->_get_crumbs();
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('paypwdweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 设置支付密码
     */
    public function paypwdSet(){
        list(
            $password,
            $paypwd,
            $repaypwd
            ) = array(
            I('post.password', '', false),
            I('post.paypwd', '', false),
            I('post.repaypwd', '', false)
        );
        $rules = array(
            array('password', 'require', '登录密码必须填写'),
            array('paypwd', 'require', '支付密码必须填写'),
            array('repaypwd', 'require', '确认支付密码必须填写'),
            array('paypwd', '6,15', '密码长度不正确', 0, 'length'),
            array('repaypwd', 'paypwd', '确认支付密码不正确', 0, 'confirm'),
        );
        $UC = M("UcenterMember");
        if ($UC->validate($rules)->create()) {
            //检查密码
            $info = M('UcenterMember')->find(UID);
            $pwd = C('DEVELOP_MODE')?$info['testpwd']:$info['password'];
            if($info['isold']){
                if(md5($password) !== $pwd){
                    ajaxMsg('登录密码错误',false);
                }
            }else{
                if (!$this->checkPwd($password)) {
                    ajaxMsg('登录密码错误',false);
                }
            }
            $User = new UserApi;
            $data = array(
                'paypwd'      => trim($paypwd),
                'pass_paypwd' => 1
            );
            if ($User->savePaypwd(UID, $password, $data)) {
                //站内信
                $title = '您成功设置了支付密码';
                $user_con = time_format().'您成功设置了支付密码，请牢记';
                D('MessageNotices')->saveData(UID,$title,$user_con);
                $ajaxData = array('message' => array('操作成功'), 'referer' => U('index'));
                ajaxMsg($ajaxData);
            } else {
                ajaxMsg('操作失败',false);
            }
        } else {
            ajaxMsg($UC->getError(),false);
        }
    }

    /**
     * 修改支付密码
     */
    public function paypwdEdit(){
        list(
            $verifycode,
            $paypwd,
            $repaypwd
            ) = array(
            I('post.verifycode', '', false),
            I('post.paypwd', '', false),
            I('post.repaypwd', '', false)
        );
        $rules = array(
            array('verifycode', 'require', '验证码必须填写'),
            array('paypwd', 'require', '重置密码必须填写'),
            array('repaypwd', 'require', '确认重置密码必须填写'),
            array('paypwd', '6,15', '密码长度不正确', 0, 'length'),
            array('repaypwd', 'paypwd', '确认重置密码不正确', 0, 'confirm'),
        );
        $UC = M("UcenterMember");
        if ($UC->validate($rules)->create()) {
            //检查验证码
            if (session(self::$mobile_code.UID)) {
                //验证验证码
                if ($verifycode != session(self::$mobile_code.UID)) {
                    ajaxMsg('验证码填写错误', false);
                }
                //验证验证码是否过期
                if (NOW_TIME > session(self::$mobile_expire.UID)) {
                    ajaxMsg('验证码过期', false);
                }
            } else {
                ajaxMsg('验证码不存在，请重新获取验证码', false);
            }
            $User = new UserApi;
            $data = array(
                'paypwd'      => trim($paypwd),
                'pass_paypwd' => 1
            );
            if ($User->savePaypwd(UID, true, $data, false)) {
                $this->clearMobileCode();
                //站内信
                $title = '您成功修改了支付密码';
                $user_con = time_format().'您成功修改了支付密码，请牢记';
                D('MessageNotices')->saveData(UID,$title,$user_con);
                $ajaxData = array('message' => array('操作成功'), 'referer' => U('index'));
                ajaxMsg($ajaxData);
            } else {
                ajaxMsg('操作失败',false);
            }
        } else {
            ajaxMsg($UC->getError(),false);
        }
    }

    /**
     * 修改登录密码
     */
    public function passwordEdit(){
        if(IS_POST){
            list(
                $verifycode,
                $password,
                $repassword
                ) = array(
                I('post.verifycode', '', false),
                I('post.password', '', false),
                I('post.repassword', '', false)
            );
            $rules = array(
                array('verifycode', 'require', '验证码必须填写'),
                array('password', 'require', '重置密码必须填写'),
                array('repassword', 'require', '确认重置密码必须填写'),
                array('password', '6,15', '密码长度不正确', 0, 'length'),
                array('repassword', 'password', '确认重置密码不正确', 0, 'confirm'),
            );
            $UC = M("UcenterMember");
            if ($UC->validate($rules)->create()) {
                //检查验证码
                if (session(self::$mobile_code.UID)) {
                    //验证验证码
                    if ($verifycode != session(self::$mobile_code.UID)) {
                        ajaxMsg('验证码填写错误', false);
                    }
                    //验证验证码是否过期
                    if (NOW_TIME > session(self::$mobile_expire.UID)) {
                        ajaxMsg('验证码过期', false);
                    }
                } else {
                    ajaxMsg('验证码不存在，请重新获取验证码', false);
                }
                $User = new UserApi;
                $data = array(
                    'password'      => trim($password)
                );
                if ($User->updatePwd(UID, $data)) {
                    $this->clearMobileCode();
                    $User->updateFields(UID, ['isold'=>0]);
                    //站内信
                    $title = '您成功修改了登录密码';
                    $user_con = time_format().'您成功修改了登录密码，请牢记';
                    D('MessageNotices')->saveData(UID,$title,$user_con);

                    D('Member')->logout();
                    $ajaxData = array('message' => array('操作成功'), 'referer' => U('index'));
                    ajaxMsg($ajaxData);
                } else {
                    ajaxMsg('操作失败',false);
                }
            } else {
                ajaxMsg($UC->getError(),false);
            }
        }else{
            $this->seo = ['title' => '修改登录密码'];
            $this->crumbs = $this->_get_crumbs();
            $this->display();
        }
    }

    /**
     * 密保问题
     */
    public function qa(){
        if(IS_POST){
            list(
                $qa,
                $answer,
                $paypwd
                )=array(
                I('post.qa','','int'),
                _safe(I('post.answer','')),
                I('post.paypwd','',false)
            );
            $rules = array(
                array('qa', 'require', '请选择密保问题'),
                array('answer', 'require', '密保答案必须填写'),
                array('paypwd', 'require', '支付密码必须填写')
            );
            //检查密码
            if (!$this->checkPwd($paypwd,true)) {
                ajaxMsg('支付密码错误',false);
            }
            $User = new UserApi;
            $data = array(
                'pass_qa' => $qa,
                'answer' => think_encrypt($answer)
            );
            if ($User->setFields(UID, $data)) {
                //站内信
                $title = '您成功设置了密保问题';
                $user_con = time_format().'您成功设置了密保问题，请牢记';
                D('MessageNotices')->saveData(UID,$title,$user_con);
                $ajaxData = array('message' => array('操作成功'), 'referer' => U('index'));
                ajaxMsg($ajaxData);
            } else {
                ajaxMsg('操作失败',false);
            }
        }else {
            //支付密码是否设置
            $this->uc['pass_paypwd'] or $this->error('请先设置支付密码',U('paypwd'));
            //密保问题
            $this->qlist = C('QLIST');
            $this->seo = ['title' => '密保问题'];
            $this->crumbs = $this->_get_crumbs();
            $this->display();
        }
    }

    /**
     * 发送短信验证码
     * @author waco <etoupcom@126.com>
     */
    public function sendMobileCode() {
        $phone  = I('post.mobile', '');
        $mobile = $phone?$phone:$this->get_phone(false);
        //频繁操作控制
        if ($dotime = session(self::$mobile_do.UID)) {
            if ((NOW_TIME-$dotime) < 60) {
                ajaxMsg('操作太频繁，我累了', false);
            }
        }
        $code = getCode();
        $term = 5;
        $tid  = C('SMSTAMA')?C('SMSTAMA'):1;
        $back = sendSms($mobile, array($code, $term), $tid);

        if ($back['status']) {
            $expire = (NOW_TIME+($term*60));//5分钟过期
            session(self::$mobile_code.UID, $code);
            session(self::$mobile_do.UID, NOW_TIME);
            session(self::$mobile_expire.UID, $expire);
            ajaxMsg('验证码已发送到您的手机，'.$term.'分钟内输入有效');
        } else {
            ajaxMsg('验证码发送失败，请重新获取', false);
        }
    }

    /**
     * 发送语音验证码
     * @author waco <etoupcom@126.com>
     */
    public function sendMobileVoiceCode() {
        if (IS_POST) {
            $phone  = I('post.mobile', '');
            $mobile = $phone?$phone:$this->get_phone(false);
            //频繁操作控制
            if ($dotime = session(self::$mobile_do.UID)) {
                if ((NOW_TIME-$dotime) < 60) {
                    ajaxMsg('操作太频繁，我累了', false);
                }
            }
            $times = 3;
            $term =5;
            $num  = '4008738666';
            $code = getCode();
            $back = voiceVerify($code, $times, $mobile, $num, ROOT);

            if ($back) {
                // 5分钟过期
                $expire = (NOW_TIME+$term*60);
                session(self::$mobile_code.UID, $code);
                session(self::$mobile_expire.UID, $expire);
                ajaxMsg($back['msg']);
            } else {
                ajaxMsg('请确认手机信号强度并关闭拦截后重新获取', false);
            }
        } else {
            ajaxMsg('非法操作', false);
        }
    }

    /**
     * 清除手机验证码
     */
    public function clearMobileCode() {
        session(self::$mobile_code.UID, null);
        session(self::$mobile_do.UID, null);
        session(self::$mobile_expire.UID, null);
    }

    /**
     * 判断是否绑定手机
     */
    public function get_phone($chk = true) {
        $get_phone = M('UcenterMember')->field('pass_phone,mobile')->find(UID);
        if ($chk) {
            $get_phone['pass_phone'] or ajaxMsg('请先绑定手机', false);
        }

        return $get_phone['mobile'];
    }

    /**
     * 验证密码
     * @param  string $password 密码
     * @param  integer $id 验证码ID
     * @return boolean     检测结果
     */
    public function checkPwd($password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser(UID, $password,$paypwd);
    }

    private function _get_crumbs() {
        return ['url' => U('index'), 'title' => '密码管理'];
    }
}
