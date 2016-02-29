<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Common\Api\EmailApi;
/**
 * 后台配置控制器
 * @author waco <etoupcom@126.com>
 */

class ConfigController extends AdminController {

	/**
	 * 配置管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
        S('DB_CONFIG_DATA', null);
		/* 查询条件初始化 */
		$map                          = array();
		$map                          = array('status' => 1);
		list($gids, $name, $p, $soso) = array(I('gids', 0, 'int'), I('name', ''), I('p', 1, 'int'), I('soso', 0, 'int'));
		empty($gids) || $map['group'] = intval($gids);
		empty($name) || $map['name']  = array('like', '%'.(string) $name.'%');

		$list = $this->lists('Config', $map, 'sort,id');
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);
		$this->assign('gids', C('CONFIG_GROUP_LIST'));
		$this->assign('list', $list);
		$this->groupList = empty($gids)?array():$gids;
		$this->name      = $name;
		$this->p         = $p;
		$this->soso      = $soso?true:false;
		$this->display();
	}

	/**
	 * 新增配置
	 * @author waco <etoupcom@126.com>
	 */
	public function add() {
		if (IS_POST) {
			$Config = D('Config');
			$data   = $Config->create();
			if ($data) {
				if ($Config->add()) {
					S('DB_CONFIG_DATA', null);
					$backUrl = array('referer' => U('index'));
					ajaxMsg($backUrl);
				} else {
					ajaxMsg('新增失败', false);
				}
			} else {
				ajaxMsg($Config->getError(), false);
			}
		} else {
			$this->assign('info', null);
			$this->display('edit');
		}
	}

	/**
	 * 编辑配置
	 * @author waco <etoupcom@126.com>
	 */
	public function edit($id = 0) {
		if (IS_POST) {
			$Config = D('Config');
			$data   = $Config->create();
			if ($data) {
				if ($Config->save()) {
					S('DB_CONFIG_DATA', null);
					//记录行为
					action_log('update_config', 'config', $data['id'], UID);
					ajaxMsg('更新成功');
				} else {
					ajaxMsg('更新失败', false);
				}
			} else {
				ajaxMsg($Config->getError(), false);
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = M('Config')->field(true)->find($id);

			if (false === $info) {
				$this->error('获取配置信息错误');
			}
			$this->assign('info', $info);
			$this->display();
		}
	}

	/**
	 * 删除配置
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$id = array_unique((array) I('get.id', 0));

		if (empty($id)) {
			ajaxMsg('请选择要操作的数据', false);
		}

		$map = array('id' => array('in', $id));
		if (M('Config')->where($map)->delete()) {
			S('DB_CONFIG_DATA', null);
			//记录行为
			action_log('update_config', 'config', $id, UID);
			ajaxMsg('删除成功');
		} else {
			ajaxMsg('删除失败', false);
		}
	}

	// 获取某个标签的配置参数
	public function group() {
		$id   = I('get.id', 1);
		$type = C('CONFIG_GROUP_LIST');
		$list = M("Config")->where(array('status' => 1, 'group' => $id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
		if ($list) {
			$this->assign('list', $list);
		}
		//p($list, 0);
		$this->assign('id', $id);
		$this->meta_title = $type[$id].'设置';
		$this->display();
	}

	/**
	 * 批量保存配置
	 * @author waco <etoupcom@126.com>
	 */
	public function save($config) {
		if ($config && is_array($config)) {
			$Config = M('Config');
			foreach ($config as $name => $value) {
				$map = array('name'      => $name);
				$Config->where($map)->setField('value', $value);
			}
		}
		S('DB_CONFIG_DATA', null);
		ajaxMsg('保存成功');
	}

	/**
	 * 注册配置
	 * @author waco <etoupcom@126.com>
	 */
	public function regist() {
		if (IS_POST) {
			$config = I('post.config', array());
			if (is_array($config['WELCOMETYPE'])) {
				$config['WELCOMETYPE'] = implode(',', $config['WELCOMETYPE']);
			}

			if ($config && is_array($config)) {
				$Config = M('Config');
				foreach ($config as $name => $value) {
					$map = array('name'      => $name);
					$Config->where($map)->setField('value', $value);
				}
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->welcomeTtpe = explode(',', C('WELCOMETYPE'));
			$this->display();
		}
	}

	/**
	 * 登录配置
	 * @author waco <etoupcom@126.com>
	 */
	public function login() {
		if (IS_POST) {
			$config = I('post.config', array());
			if (is_array($config['WAYS'])) {
				$config['WAYS'] = implode(',', $config['WAYS']);
			}

			if ($config && is_array($config)) {
				$Config = M('Config');
				foreach ($config as $name => $value) {
					$map = array('name'      => $name);
					$Config->where($map)->setField('value', $value);
				}
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->ways = explode(',', C('WAYS'));
			$this->display();
		}

	}

	/**
	 * 邮箱配置
	 * @author waco <etoupcom@126.com>
	 */
	public function email() {
		if (IS_POST) {
			$config = I('post.config', array());
			$this->save($config);
		} else {
			$this->display();
		}
	}

	/**
	 * 邮箱测试
	 * @author waco <etoupcom@126.com>
	 */
	public function send() {
		if (IS_POST) {
			$to = I('post.toEmail', '', 'email');
			$to || $this->error('请填写邮件地址');
			$title   = C('PLATFORMNAME').' 测试邮件';
			$content = '恭喜您，如果您收到此邮件则代表后台邮件发送设置正确！';
			$back    = EmailApi::sendHtmlMail($to, $title, $content);
			if ($back['status']) {
				ajaxMsg($back['msg']);
			} else {
				ajaxMsg($back['msg'], false);
			}
		} else {
			$this->display();
		}
	}

	/**
	 * 短信配置
	 * @author waco <etoupcom@126.com>
	 */
	public function sms() {
		if (IS_POST) {
			$config = I('post.config', array());
			$this->save($config);
		} else {
			$this->display();
		}
	}

	/**
	 * 短信测试
	 * @author waco <etoupcom@126.com>
	 */
	public function sendSms() {
		if (IS_POST) {
			$type = I('post.type', 1, 'int');
			$type || ajaxMsg('请选择验证码类型', false);
			switch ($type) {
				case 2:
					list(
						$to,
						$code,
						$times,
						$num,
						$url
					) = array(
						I('post.to', ''),
						I('post.code', ''),
						I('post.times', '3', 'int'),
						I('post.num', '4008738666'),
						I('post.url', 'http://127.0.0.1')
					);
					$back = voiceVerify($code, $times, $to, $num, $url);
					if ($back) {
						ajaxMsg('发送成功，注意查收');
					} else {
						ajaxMsg('发送失败', false);
					}
					break;

				default:
					list(
						$to,
						$code,
						$term,
						$tid
					) = array(
						I('post.to', ''),
						I('post.code', ''),
						I('post.term', '5', 'int'),
						I('post.tid', '13641')
					);
					$back = sendSms($to, array($code, $term), $tid);
					if ($back['status']) {
						ajaxMsg($back['msg']);
					} else {
						ajaxMsg($back['msg'], false);
					}
					break;
			}
		} else {
			$type = C('SMS_CAPTCHA_TYPE');
			$type || $this->error('请选择验证码类型');
			$this->type = $type;
			$this->display();
		}
	}

    /**
     * 配资配置
     * @author waco <etoupcom@126.com>
     */
    public function peizi() {
        if (IS_POST) {
            $config = I('post.config', array());
            $this->save($config);
        } else {
            $this->display();
        }
    }

	/**
	 * 积分设置
	 * @author waco <etoupcom@126.com>
	 */
	public function credit() {
		if (IS_POST) {
			$credits = I('post.credits', array());
			if ($credits && is_array($credits)) {
				$Config = M('Config');
				$map    = array('name' => 'CREDITS');
				$value  = serialize($credits);
				$Config->where($map)->setField('value', $value);
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->list    = unserialize(C('CREDITS'));
			$this->navtabs = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 删除积分设置
	 * @author waco <etoupcom@126.com>
	 */
	public function delCredit() {
		$id = I('get.id', 0, 'int');
		$id || ajaxMsg('请选择要操作的数据', false);
		$credits = unserialize(C('CREDITS'));
		unset($credits[$id]);
		if ($credits && is_array($credits)) {
			$Config = M('Config');
			$map    = array('name' => 'CREDITS');
			$value  = serialize($credits);
			$Config->where($map)->setField('value', $value);
		}
		S('DB_CONFIG_DATA', null);
		ajaxMsg('操作成功');
	}

	/**
	 * 行为设置
	 * @author waco <etoupcom@126.com>
	 */
	public function behavior() {
		if (IS_POST) {
			$behavior = I('post.behavior', array());
			if ($behavior && is_array($behavior)) {
				$Config = M('Config');
				$map    = array('name' => 'USERBEHAVIOR');
				$value  = serialize($behavior);
				$Config->where($map)->setField('value', $value);
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$this->list = unserialize(C('USERBEHAVIOR'));
            //p($this->list);
			$this->navtabs = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 删除用户行为
	 * @author waco <etoupcom@126.com>
	 */
	public function delBehavior() {
		$id = I('get.id', 0, 'int');
		$id || ajaxMsg('请选择要操作的数据', false);
		$behavior = unserialize(C('USERBEHAVIOR'));
		unset($behavior[$id]);
		if ($behavior && is_array($behavior)) {
			$Config = M('Config');
			$map    = array('name' => 'USERBEHAVIOR');
			$value  = serialize($behavior);
			$Config->where($map)->setField('value', $value);
		}
		S('DB_CONFIG_DATA', null);
		ajaxMsg('操作成功');
	}

	/**
	 * 积分策略
	 * @author waco <etoupcom@126.com>
	 */
	public function strategy() {
		if (IS_POST) {
            $strategy_c = C('STRATEGY');
			$strategy = empty($strategy_c)?array():unserialize($strategy_c);
			$info     = I('post.info', array());
			empty($info) && ajaxMsg('请编辑数据后提交', false);
			$strategy = array_merge($strategy, $info);
			if ($strategy && is_array($strategy)) {
				$Config = M('Config');
				$map    = array('name' => 'STRATEGY');
				$value  = serialize($strategy);
				$Config->where($map)->setField('value', $value);
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$behaviorGroup = C('USERBEHAVIORGROUP');

			$behavior = unserialize(C('USERBEHAVIOR'));

			if (is_array($behaviorGroup) && !empty($behaviorGroup)) {
				foreach ($behaviorGroup as $key => $value) {
					if (is_array($behavior) && !empty($behavior)) {
						foreach ($behavior as $k => $v) {
							if ($key == $v['group']) {
								$values[$key][$k] = $v;
							}
						}
					}
					$behaviorList[$key]['title'] = $value;
					$behaviorList[$key]['value'] = $values[$key];
				}
			}
			$this->behaviorList = $behaviorList;
			$this->credit       = unserialize(C('CREDITS'));
			$this->strategy     = unserialize(C('STRATEGY'));
			$this->navtabs      = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 积分充值
	 * @author waco <etoupcom@126.com>
	 */
	public function recharge() {
		if (IS_POST) {
			list($recharge, $ctype, $rate, $min) = array(
				I('post.recharge', array()),
				I('post.ctype', array()),
				I('post.rate', array()),
				I('post.min', array())
			);
			is_array($recharge) || $recharge = array();
			is_array($ctype) || $ctype       = array();
			foreach ($ctype as $key => $value) {
				if ($rate[$key] && !isset($recharge[$value])) {
					$recharge[$value] = array(
						'rate' => intval($rate[$key]),
						'min'  => $min[$key]?$min[$key]:'');
				}
			}
			if ($recharge && is_array($recharge)) {
				$Config = M('Config');
				$map    = array('name' => 'RECHARGE');
				$value  = serialize($recharge);
				$Config->where($map)->setField('value', $value);
			}
			S('DB_CONFIG_DATA', null);
			ajaxMsg('保存成功');
		} else {
			$credit   = unserialize(C('CREDITS'));
			$recharge = unserialize(C('RECHARGE'));
			if (is_array($credit) && !empty($credit)) {
				foreach ($credit as $key => $value) {
					if (empty($recharge[$key])) {
						$lassCredit[$key] = $credit[$key];
						unset($credit[$key]);
					}
				}
			}
			$this->credit     = $credit;
			$this->lassCredit = $lassCredit;
			$this->recharge   = $recharge;
			$this->navtabs    = $this->navtabs();
			$this->display();
		}

	}

	/**
	 * 导航列表
	 *
	 * @author waco <etoupcom@126.com>
	 */
	private function navtabs() {
		$tabs = array(
			array(
				'title' => '积分设置',
				'tag'   => 'credit',
				'url'   => U('credit')
			),
			array(
				'title' => '行为设置',
				'tag'   => 'behavior',
				'url'   => U('behavior')
			),
			array(
				'title' => '积分策略',
				'tag'   => 'strategy',
				'url'   => U('strategy')
			),
			array(
				'title' => '积分充值',
				'tag'   => 'recharge',
				'url'   => U('recharge')
			),
			array(
				'title' => '积分日志',
				'tag'   => 'log',
				'url'   => U('log')
			)
		);
		foreach ($tabs as $key => $value) {
			if ($value['tag'] == ACTION_NAME) {
				$tabs[$key]['current'] = 'current';
			}
		}

		return $tabs;
	}
}