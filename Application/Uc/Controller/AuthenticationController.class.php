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
 * 认证管理控制器
 */

class AuthenticationController extends UcenterController {
    private static $mobile_code = 'mobile_code_';//定义短信验证码标识
    private static $mobile_do = 'mobile_do_';//定义短信发送时间标识
    private static $mobile_expire = 'mobile_expire_';//定义短信验证码过期时间标识
    private static $mobile_next = 'mobile_next_';//定义新手机验证页标识
    private static $email_code = 'email_code_';//定义邮件验证码标识
    private static $email_do = 'email_do_';//定义邮件发送时间标识
    private static $email_expire = 'email_expire_';//定义邮件验证码过期时间标识
    private static $email_next = 'email_next_';//定义新邮箱验证页标识
	/**
	 * 认证管理
	 */
	public function index() {
		Cookie('__forward__', $_SERVER['REQUEST_URI']);
		$this->seo = ['title' => '认证管理'];
		$this->display();
	}

	/**
	 * 实名认证
	 */
	public function realname() {
		if (IS_POST) {
			$data        = I('post.');
			$data['uid'] = UID;
            $data['realname'] = _safe($data['realname']);
            $data['card_front'] = $data['icon'][0];
            $data['card_back'] = $data['icon'][1];
			$mod = D('Userdata');
			if ($mod->create($data)) {
				//控制重新提交
				$info = $mod->hasInfo(UID);
				if (is_array($info)) {
					$back = $mod->save();
				} else {
					$back = $mod->add();
				}
				if ($back) {
					M('UcenterMember')->save(array('id' => UID, 'pass_realname' => -1,'realname'=>$data['realname']));
				}
                //站内信
                $title = '您提交了实名认证';
                $user_con = time_format().'您提交了实名认证，请等待审核';
                $admin_con = time_format().'用户提交了实名认证，请及时处理，用户名：'.USERNAME.'，<a href="'.C('HTTPHOST').'/admin.php/Authentication/index">立即查看</a>';
                D('MessageNotices')->saveData(UID,$title,$user_con,1,$admin_con);

				$this->success('实名认证提交成功，请等待审核', U('Uc/Index/index'));
			} else {
				$this->error($mod->getError());
			}
		} else {
			$this->seo    = ['title' => '实名认证'];
			$this->crumbs = $this->_get_crumbs();
            $mobile = I('get.mobile','');
            switch($mobile){
                case 'weixin':
                    $this->display('realnameweixin');
                    break;
                default:
                    $this->display();
            }
		}
	}

	/**
	 * 手机绑定
	 */
	public function phone() {
        $mobile = $this->get_phone(false);
        if($mobile){
            $this->mobile = hide_mobile($mobile);
        }
		$this->seo    = ['title' => '手机绑定'];
		$this->crumbs = $this->_get_crumbs();
		$this->display();
	}

	/**
	 * 绑定手机 pass_phone = 0
	 */
	public function phoneVerify() {
		$verifycode = I('post.verifycode','', 'trim');
		$verifycode or ajaxMsg('请填写验证码', false);
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
		//调整状态
		$User = new UserApi;
		$User->setFields(UID, 'pass_phone');
		$this->clearMobileCode();
		$ajaxData = array('message' => array('验证成功'), 'referer' => U('index'));
		ajaxMsg($ajaxData);
	}

	/**
	 * 绑定手机 next
	 */
	public function phoneNext() {
        if(IS_POST){
            list(
                $verifycode,
                $mobile
                ) = array(
                I('post.verifycode', ''),
                I('post.mobile', '')
            );
            $mobile or ajaxMsg('请填写新手机号码', false);
            $verifycode or ajaxMsg('请填写验证码', false);
            $this->has_phone($mobile) or ajaxMsg('手机号码已经存在，请更换',false);
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
            //修改资料
            $User = new UserApi;
            $map  = array(
                'mobile' => $mobile,
            );
            $User->setFields(UID, $map);
            session(self::$mobile_next.UID, null);
            $this->clearMobileCode();
            $ajaxData = array('message' => array('手机验证成功'), 'referer' => U('index'));
            ajaxMsg($ajaxData);
        }else{
            $mobile = $this->get_phone();
            if (!session(self::$mobile_next.UID)) {
                $this->error('请先完成原始手机验证', U('phone'));
            }
            $this->seo = ['title' => '新手机绑定'];
            $this->crumbs = $this->_get_crumbs();
            $this->display();
        }
	}

	/**
	 * 绑定手机 pass_phone = 1
	 */
	public function phoneEdit() {
        $this->get_phone();
        $verifycode = I('post.verifycode', '', 'trim');
        $verifycode or ajaxMsg('请填写验证码', false);
        if (session(self::$mobile_code.UID)) {
            //验证验证码
            if ($verifycode != session(self::$mobile_code.UID)) {
                ajaxMsg('验证码填写错误', false);
            }
            //验证验证码是否过期
            if (NOW_TIME > session(self::$mobile_expire.UID)) {
                ajaxMsg('验证码过期，请重新获取', false);
            }
        } else {
            ajaxMsg('验证码不存在，请重新获取验证码', false);
        }
        session(self::$mobile_next.UID, 1);
        $this->clearMobileCode();
        $ajaxData = array('message' => array('原始手机验证成功'), 'referer' => U('phoneNext'));
        ajaxMsg($ajaxData);
	}

	/**
	 * 发送短信验证码
	 * @author waco <etoupcom@126.com>
	 */
	public function sendMobileCode() {
		$phone  = I('post.mobile', '');
		$mobile = $phone?$phone:$this->get_phone(false);
        $check = check_mobile($mobile);
        $check or ajaxMsg('手机号码不正确，请联系客服确认', false);
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
     * 判断是否存在手机
     */
    public function has_phone($mobile) {
        $info = M('UcenterMember')->where(array('mobile'=>$mobile))->field('pass_phone,mobile')->find();
        return empty($info)?true:false;
    }

	/**
	 * 邮箱验证 pass_email =1
	 */
	public function email() {
        $this->email = $this->get_email(false);
		$this->seo    = ['title' => '邮箱验证'];
		$this->crumbs = $this->_get_crumbs();
		$this->display();

	}

	/**
	 * 邮箱验证 pass_email =0
	 */
	public function emailVerify() {
		$verifycode = I('post.verifycode', '', 'trim');
		$verifycode or ajaxMsg('请填写验证码', false);
        $email = I('post.email')?I('post.email','','validate_email'):$this->get_email(false);
        $email or ajaxMsg('请填写邮箱地址', false);
        if (session(self::$email_code.UID)) {
            //验证验证码
            if ($verifycode != session(self::$email_code.UID)) {
                ajaxMsg('验证码填写错误', false);
            }
            //验证验证码是否过期
            if (NOW_TIME > session(self::$email_expire.UID)) {
                ajaxMsg('验证码过期', false);
            }
        } else {
            ajaxMsg('验证码不存在，请重新获取验证码', false);
        }

		$User  = new UserApi;
        //调整邮件认证状态
        $User->setFields(UID, ['pass_email'=>1,'email'=>$email]);
        $this->clearEmailCode();
        $ajaxData = array('message' => array('验证成功'), 'referer' => U('index'));
        ajaxMsg($ajaxData);
	}

    /**
     * 邮箱验证 pass_email =1
     */
    public function emailEdit() {
        $this->get_phone();
        $this->get_email();
        $verifycode = I('post.verifycode', '','trim');
        $verifycode or ajaxMsg('请填写验证码', false);
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
        session(self::$email_next.UID, 1);
        $this->clearMobileCode();
        $ajaxData = array('message' => array('原始手机验证成功'), 'referer' => U('emailNext'));
        ajaxMsg($ajaxData);
    }

    public function emailNext(){
        if(IS_POST){
            list(
                $email,
                $verifycode
                ) = array(
                I('post.email', ''),
                I('post.verifycode', '','trim')
            );
            $email or ajaxMsg('请填写新邮箱地址', false);
            $verifycode or ajaxMsg('请填写验证码', false);
            $User       = new UserApi;
            $infoFemail = $User->hasUser(array('email' => $email));
            empty($infoFemail) || ajaxMsg('邮箱地址已经存在，请更换', false);
            if (session(self::$email_code.UID)) {
                //验证验证码
                if ($verifycode != session(self::$email_code.UID)) {
                    ajaxMsg('验证码填写错误', false);
                }
                //验证验证码是否过期
                if (NOW_TIME > session(self::$email_expire.UID)) {
                    ajaxMsg('验证码过期', false);
                }
            } else {
                ajaxMsg('验证码不存在，请重新获取验证码', false);
            }
            //修改资料
            $User = new UserApi;
            $map  = array(
                'email' => $email,
            );
            $User->setFields(UID, $map);
            session(self::$email_next.UID, null);
            $this->clearEmailCode();
            $ajaxData = array('message' => array('邮箱验证成功'), 'referer' => U('index'));
            ajaxMsg($ajaxData);
        }else{
            if (!session(self::$email_next.UID)) {
                $this->error('请先完成手机验证', U('email'));
            }
            $this->seo = ['title' => '新邮箱绑定'];
            $this->crumbs = $this->_get_crumbs();
            $this->display();
        }
    }

	/**
	 * 发送邮件验证码
	 */
	public function sendEmailCode() {
        $mail  = I('post.email', '');
        $email = $mail?$mail:$this->get_email(false);
        $email or ajaxMsg('邮箱地址没有填写或者不存在', false);
        $id = M('UcenterMember')->where(['email'=>$email,'id'=>['neq',UID]])->getField('id');
        if($id){
            ajaxMsg('邮箱已经存在，请更换', false);
        }
        //频繁操作控制
        if ($dotime = session(self::$email_do.UID)) {
            if ((NOW_TIME-$dotime) < 60) {
                ajaxMsg('操作太频繁，我累了', false);
            }
        }
		//发送验证邮件
		$title          = '来自六合资本邮箱验证码邮件';
		$this->sitename = C('WEB_SITE_TITLE');
		$this->username = USERNAME;
		$this->code     = $code = getCode();
		$this->time     = date('Y-m-d H:i', NOW_TIME);
		$content        = $this->fetch(T('Tpl/emailVerify'));
		$back           = EmailApi::sendHtmlMail($email, $title, $content);
		if ($back['status']) {
            $term = 10;
            $expire = (NOW_TIME+($term*60));//10分钟过期
            session(self::$email_code.UID, $code);
            session(self::$email_do.UID, NOW_TIME);
            session(self::$email_expire.UID, $expire);

			$url  = $this->getEmailUrl($mail);
			$data = array(
				'message' => array('邮件已经发送成功，请注意查收'),
				'data'    => $url
			);
			ajaxMsg($data);
		} else {
			ajaxMsg($back['msg'], false);
		}
	}

    /**
     * 获取邮箱登录地址
     */
    public function getEmailUrl($email){
        $mailList     = array('gmail.com' => 'google.com');
        list(, $mail) = explode('@', $email, 2);
        $gotoEmail    = 'http://mail.'.(isset($mailList[$mail])?$mailList[$mail]:$mail);
        return $gotoEmail;
    }

    /**
     * 清除邮箱验证码
     */
    public function clearEmailCode() {
        session(self::$email_code.UID, null);
        session(self::$email_do.UID, null);
        session(self::$email_expire.UID, null);
    }

    /**
     * 判断是否验证邮箱
     */
    public function get_email($chk = true) {
        $get_email = M('UcenterMember')->field('pass_email,email')->find(UID);
        if ($chk) {
            $get_email['pass_email'] or ajaxMsg('请先验证邮箱', false);
        }

        return $get_email['email'];
    }

    /**
     * 判断是否存在邮箱
     */
    public function has_email($email) {
        $info = M('UcenterMember')->where(array('email'=>$email))->field('pass_email,email')->find();
        return empty($info)?true:false;
    }

	private function _get_crumbs() {
		return ['url' => U('index'), 'title' => '认证管理'];
	}
}
