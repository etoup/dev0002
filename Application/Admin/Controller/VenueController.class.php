<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi;

/**
 * 后台场馆管理控制器
 * @author waco <etoupcom@126.com>
 */

class VenueController extends AdminController {

	/**
	 * 场馆管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		list(
			$username,
			$mobile,
			$email,
			$time_start,
			$time_end,
			$p,
			$soso
		) = array(
			I('username', ''),
			I('mobile', ''),
			I('email', ''),
			I('time_start', ''),
			I('time_end', ''),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
        if($username){
            $where['username'] = array('like', '%'.(string) $username.'%');
            $where['realname'] = array('like', '%'.(string) $username.'%');
            $where['name'] = array('like', '%'.(string) $username.'%');
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
		$mobile && $map['mobile']     = array('like', '%'.(string) $mobile.'%');
		$email && $map['email']       = array('like', '%'.(string) $email.'%');
		if ($time_start && $time_end) {
			$map['reg_time'] = array('between', [strtotime($time_start),strtotime($time_end)]);
		}
		if ($time_start && !$time_end) {
			$map['reg_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['reg_time'] = array('lt', strtotime($time_end));
		}
        $map['status'] = ['gt',-1];
		$list = $this->lists('VenueView', $map,'created_time desc',true,1);
		int_to_string($list);
//        p($list);
		$this->assign('_list', $list);
		$this->username   = $username;
		$this->mobile     = $mobile;
		$this->email      = $email;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->p          = $p;
		$this->soso       = $soso?true:false;
		$this->display();
	}

    /**
     * 添加场馆
     * @author waco <etoupcom@126.com>
     */
    public function add($username = '', $password = '', $mobile = '', $email = '',$name='') {
        if (IS_POST) {
            $username or ajaxMsg('请填写用户名', false);
            $password or ajaxMsg('请填写密码', false);
            $mobile or ajaxMsg('请填写手机号码', false);
            $email or ajaxMsg('请填写邮箱地址', false);
            $name or ajaxMsg('请填写场馆名称', false);
            /* 调用注册接口注册用户 */
            $User = new UserApi;
            $uid  = $User->register($username, $password, $email,$mobile);
            if (0 < $uid) {//注册成功
                $user = array('uid' => $uid, 'nickname' => $username, 'status' => 1);
                M('Member')->add($user);
                $data = [
                    'uid'=>$uid,
                    'name'=>$name,
                    'created_time'=>NEW_TIME
                ];
                M('Venue')->add($data);
                ajaxMsg('场馆添加成功');

            } else {//注册失败，显示错误信息
                ajaxMsg($this->showRegError($uid), false);
            }
        } else {
            $this->groups = M('MemberGroups')->where(array('type' => array('in', 'special,system')))->select();
            $this->display();
        }
    }

    /**
     * 编辑会员认证信息
     * @author waco <etoupcom@126.com>
     */
    public function edit() {
        if (IS_POST) {
            $id = I('post.id',0,'int');
            $id or ajaxMsg('请选择操作项！', false);
            $name = I('post.name','');
            $name or ajaxMsg('请填写场馆名称', false);
            $data = ['id'=>$id,'name'=>$name];
            M('Venue')->save($data);
            ajaxMsg('操作成功！');
        } else {
            $id = I('get.id', 0, 'int');
            $id || $this->error('请选择操作项');
            $Model        = D("VenueView");
            $info         = $Model->find($id);
            $this->info = $info;
            $this->display();
        }

    }

    //删除
    public function del() {
        $id = I('request.id');
        if (is_array($id)) {
            $back = M('Venue')->where(array('id' => array('in', $id)))->save(['status'=>-1]);
            $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
        } else {
            $back = M('Venue')->where(['id'=>$id])->save(['status'=>-1]);
            $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
        }
    }

    //启用
    public function enable() {
        $id = I('post.id', array());
        empty($id) && ajaxMsg('请选择操作项', false);
        $back = M('Venue')->where(array('id' => array('in', $id)))->save(array('status' => 1));
        $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
    }

    //禁用
    public function disable() {
        $id = I('post.id', array());
        empty($id) && ajaxMsg('请选择操作项', false);
        $back = M('Venue')->where(array('id' => array('in', $id)))->save(array('status' => 0));
        $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
    }

    //项目列表
    public function items() {
        $data = array();
        list(
            $keyword,
            $p,
            $soso
            ) = array(
            I('keyword', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $keyword && $data['name'] = array('like', '%'.$keyword.'%');
        $data['status'] = ['in',[0,1]];
        $list = $this->lists('Items', $data, 'sort');

        $this->list    = $list;
        $this->p       = $p;
        $this->soso    = $soso?true:false;
        $this->keyword = $keyword?$keyword:'';
        $this->display();
    }

    //添加项目
    public function additems() {
        if (IS_POST) {
            list(
                $name,
                $icon,
                $initial,
                $bgcolor,
                $days,
                $hours,
                $sort
                ) = array(
                I('post.name', ''),
                I('post.icon', ''),
                I('post.initial', ''),
                I('post.bgcolor', ''),
                I('post.days', 0, 'int'),
                I('post.hours',0, 'int'),
                I('post.sort', 0, 'int')
            );
            $name || ajaxMsg('请填写项目名称', false);
            $icon || ajaxMsg('请填写项目图标', false);
            $days || ajaxMsg('请填写预定控制', false);
            $hours || ajaxMsg('请填写撤单控制', false);
            if(!$this->passpreg($initial)){
                ajaxMsg('项目标识错误', false);
            }
            if($this->hasinitial($initial)){
                ajaxMsg('标识已经存在', false);
            }
            $data = array(
                'name' => $name,
                'icon' => $icon,
                'initial'=>$initial,
                'bgcolor'=>$bgcolor,
                'days'=>$days,
                'hours'=>$hours,
                'sort' => $sort
            );
            if (M('Items')->add($data)) {
                ajaxMsg('操作成功');
            } else {
                ajaxMsg('操作失败', false);
            }
        } else {

            $this->display();
        }

    }

    //编辑项目
    public function edititems() {
        if (IS_POST) {
            list(
                $id,
                $name,
                $icon,
                $initial,
                $bgcolor,
                $days,
                $hours,
                $sort
                ) = array(
                I('post.id', 0, 'int'),
                I('post.name', ''),
                I('post.icon', ''),
                I('post.initial', ''),
                I('post.bgcolor', ''),
                I('post.days', 0, 'int'),
                I('post.hours',0, 'int'),
                I('post.sort', 0, 'int')
            );
            $id || ajaxMsg('请选择操作项', false);
            $name || ajaxMsg('请填写项目名称', false);
            $icon || ajaxMsg('请填写项目图标', false);
            $days || ajaxMsg('请填写预定控制', false);
            $hours || ajaxMsg('请填写撤单控制', false);
            if(!$this->passpreg($initial)){
                ajaxMsg('项目标识错误', false);
            }
            if($this->hasinitial($initial,$id)){
                ajaxMsg('标识已经存在', false);
            }
            $data = array(
                'id'   => $id,
                'name' => $name,
                'icon' => $icon,
                'initial'=>$initial,
                'bgcolor'=>$bgcolor,
                'sort' => $sort
            );
            if (M('Items')->save($data)) {
                ajaxMsg('操作成功');
            } else {
                ajaxMsg('操作失败', false);
            }
        } else {
            $id = I('get.id', 0, 'int');
            $id || $this->error('请选择操作项');
            $this->info = M('Items')->find($id);
            $this->display();
        }

    }

    //删除项目
    public function delitems() {
        $id = I('request.id');
        if (is_array($id)) {
            $back = M('Items')->where(array('id' => array('in', $id)))->save(['status'=>-1]);
            $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
        } else {
            $back = M('Items')->delete($id);
            $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
        }
    }

    //启用
    public function enableitems() {
        $id = I('post.id', array());
        empty($id) && ajaxMsg('请选择操作项', false);
        $back = M('Items')->where(array('id' => array('in', $id)))->save(array('status' => 1));
        $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
    }

    //禁用
    public function disableitems() {
        $id = I('post.id', array());
        empty($id) && ajaxMsg('请选择操作项', false);
        $back = M('Items')->where(array('id' => array('in', $id)))->save(array('status' => 0));
        $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
    }

    //验证是否大小
    private function passpreg($val){
        if (preg_match ("/^[A-Z]{2,8}$/", $val)) {
            return true;
        } else {
            return false;
        }
    }

    //检查项目标识是否重复
    private function hasinitial($initial,$id=0){
        if($id){
            $info = M('Items')->where(['id'=>['neq',$id],'initial'=>$initial])->find();
        }else{
            $info = M('Items')->where(['initial'=>$initial])->find();
        }

        if($info){
            return true;
        }else{
            return false;
        }
    }
}