<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;


/**
 * 后台银行卡管理控制器
 * @author waco <etoupcom@126.com>
 */

class CardController extends AdminController {

	/**
	 * 银行卡管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
        $list = $this->getList();
		$this->assign('_list', $list);
        //p($list);
        $this->keyword    = trim(I('keyword', ''));
        $this->card_number = I('card_number', '');
        $this->bank_name   = I('bank_name', '');
        $this->full_name   = I('full_name', '');
        $this->p          = I('p');
        $this->soso       = I('soso')?true:false;
		$this->display();
	}

    public function getList(){
        list(
            $keyword,
            $card_number,
            $bank_name,
            $full_name,
            $p,
            $soso
            ) = array(
            trim(I('keyword', '')),
            I('card_number', ''),
            I('bank_name', ''),
            I('full_name', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        if($keyword){
            $map['UcenterMember.username'] = array('like', '%'.(string) $keyword.'%');
            $map['realname'] = array('like', '%'.(string) $keyword.'%');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        if($card_number){
            $where['card_number'] = array('like', '%'.(string) $card_number.'%');
        }
        if($bank_name){
            $where['bank_name'] = array('like', '%'.(string) $bank_name.'%');
        }
        if($full_name){
            $where['full_name'] = array('like', '%'.(string) $full_name.'%');
        }

        $list = $this->lists('MyCardView', $where,'',true,1);
        int_to_string($list);
        return $list;
    }

    /**
     * 查看
     * @author waco <etoupcom@126.com>
     */
    public function view(){
        $id = I('get.id',0,'int');
        $id or $this->error('请选择操作项');
        $info = D('MyCardView')->find($id);
        $this->info = $info;
        $this->display();
    }

}