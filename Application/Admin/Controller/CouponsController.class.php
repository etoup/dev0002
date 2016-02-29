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
 * 管理控制器
 * @author waco <etoupcom@126.com>
 */

class CouponsController extends AdminController {

	/**
	 * 管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		list(
			$keyword,
			$items_id,
			$status,
			$time_start,
			$time_end,
			$p,
			$soso
		) = array(
			I('keyword', ''),
			I('items_id', 0,'int'),
			I('status', ''),
			I('time_start', ''),
			I('time_end', ''),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
        $keyword && $map['name']      = array('like', '%'.(string) $keyword.'%');
		$items_id && $map['items_id']     = $items_id;
        if($status){
            $status_info = $this->getStatus($status);
            $map['status']       = $status_info['val'];
        }
		if ($time_start && $time_end) {
			$map['end'] = array('between', [strtotime($time_start),strtotime($time_end)]);
		}
		if ($time_start && !$time_end) {
			$map['end'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['end'] = array('lt', strtotime($time_end));
		}
		$list = $this->lists('CouponsView', $map,'created_time desc',true,1);
		int_to_string($list);
//        p($list,0);
		$this->assign('_list', $list);
		$this->keyword   = $keyword;
		$this->items_id     = $items_id;
		$this->status      = $status;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->p          = $p;
		$this->soso       = $soso?true:false;
        $this->items = M('Items')->where(['status'=>1])->select();
        $this->coupon_status = $this->getStatus();
		$this->display();
	}

    public function regcoupon(){
        $list = M('Coupons')->where(['types'=>1,'status'=>['in',[0,1]]])->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['items_name'] = M('Items')->where(['id'=>$v['items_id']])->getField('name');
            }
        }
        $this->_list = $list;
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            $items_id = I('post.items_id',0,'int');
            if(!$items_id){
                ajaxMsg('请选择项目',false);
            }
            is_numeric(I('post.price')) or ajaxMsg('抵扣金额为数字',false);
            is_numeric(I('post.num')) or ajaxMsg('张数为数字',false);
            is_numeric(I('post.days')) or ajaxMsg('有效期为数字',false);
            $data = I('post.');
            $data['status'] = 1;
            $data['types'] = 1;
            $data['created_time'] = NOW_TIME;
            $bid = M('Coupons')->add($data);
            if($bid)
                ajaxMsg('操作成功');
            else
                ajaxMsg('操作失败',false);
        }else{
            $this->items = M('Items')->where(['status'=>1])->select();
            $this->display();
        }
    }

    public function edit(){
        if (IS_POST) {
            $id = I('post.id',0,'int');
            if(!$id){
                ajaxMsg('请选择操作项',false);
            }
            $items_id = I('post.items_id',0,'int');
            if(!$items_id){
                ajaxMsg('请选择项目',false);
            }
            is_numeric(I('post.price')) or ajaxMsg('抵扣金额为数字',false);
            is_numeric(I('post.num')) or ajaxMsg('张数为数字',false);
            is_numeric(I('post.days')) or ajaxMsg('有效期为数字',false);
            $data = I('post.');
            $data['id'] = $id;
            $data['update_time'] = NOW_TIME;
            $bid = M('Coupons')->save($data);
            if($bid)
                ajaxMsg('操作成功');
            else
                ajaxMsg('操作失败',false);
        }else{
            $id = I('get.id', 0, 'int');
            $id || $this->error('请选择操作项');
            $Model        = M("Coupons");
            $info         = $Model->find($id);
            $this->info = $info;
            $this->items = M('Items')->where(['status'=>1])->select();
            $this->display();
        }
    }

    //删除
    public function del() {
        $id = I('request.id');
        if (is_array($id)) {
            $back = M('Coupons')->where(array('id' => array('in', $id)))->save(['status'=>-1]);
            $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
        } else {
            $back = M('Coupons')->where(['id'=>$id])->save(['status'=>-1]);
            $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
        }
    }

    //启用
    public function enable() {
        $id = I('post.id', array());
        empty($id) && ajaxMsg('请选择操作项', false);
        $back = M('Coupons')->where(array('id' => array('in', $id)))->save(array('status' => 1));
        $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
    }

    //禁用
    public function disable() {
        $id = I('post.id', array());
        empty($id) && ajaxMsg('请选择操作项', false);
        $back = M('Coupons')->where(array('id' => array('in', $id)))->save(array('status' => 0));
        $back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
    }

    private function getStatus($tags=''){

        $status = [
            'pass'=>[
                'title'=>'已过期',
                'val'=>-1
            ],
            'nouse'=>[
                'title'=>'未使用',
                'val'=>0
            ],
            'use'=>[
                'title'=>'已使用',
                'val'=>1
            ]
        ];
        if($tags){
            return $status[$tags];
        }
        return $status;
    }
}