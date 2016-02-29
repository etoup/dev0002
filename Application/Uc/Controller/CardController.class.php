<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;
use User\Api\UserApi;

/**
 * 银行卡管理控制器
 */

class CardController extends UcenterController {

	/**
	 * 银行卡管理
	 */
	public function index() {
        if(!$this->uc['pass_realname'] && !$this->uc['realname']){
            $this->error('请先完成实名认证',U('Uc/Authentication/realname'));
        }
        if(IS_POST){
            list(
                $card_number,
                $bid,
                $bank_name,
                $full_name
                )=[
                I('post.card_number',''),
                I('post.bid',0,'int'),
                _safe(I('post.bank_name','')),
                _safe(I('post.full_name',''))
            ];
            UID ? $data['uid']=UID : ajaxMsg('请先登录',false);
            is_numeric($card_number) ? $data['card_number']=trim($card_number) : ajaxMsg('银行卡卡号只能是数字',false);
            (strlen($card_number)<=24 && strlen($card_number)>4) ? $data['card_number']=trim($card_number) : ajaxMsg('银行卡卡号为4-24位数字',false);
            $bank_name ? $data['bank_name']=$bank_name : ajaxMsg('请选择或者填写银行名称',false);
            $full_name ? $data['full_name']=$full_name : ajaxMsg('请填写支行全称',false);
            $bid and $data['bid']=$bid;
            $card_number_format_arr = str_split(trim($card_number),4);
            switch(count($card_number_format_arr)){
                case 2:
                    $rarr = ['****'];
                    array_splice($card_number_format_arr,0,1,$rarr);
                    break;
                case 3:
                    $rarr = ['****'];
                    array_splice($card_number_format_arr,1,1,$rarr);
                    break;
                case 4:
                    $rarr = ['****','****'];
                    array_splice($card_number_format_arr,1,2,$rarr);
                    break;
                case 5:
                case 6:
                    $rarr = ['****','****'];
                    array_splice($card_number_format_arr,2,2,$rarr);
                    break;
            }
            $data['card_number_format'] = implode(' ',$card_number_format_arr);
            $data['created_time'] = NOW_TIME;
            M('MyCard')->add($data);
            $backUrl = array('referer' => U('Uc/Card/index'), 'message' => array('操作成功'));
            ajaxMsg($backUrl);
        }else{
            $this->realname = $this->uc['realname'];
            $banks = M('Banks')->order('sort,id')->select();
            if(is_array($banks) and !empty($banks)){
                $banksList = [];
                foreach($banks as $k =>$v){
                    if($v['icon']){
                        $banksList['logoList'][] = $v;
                    }else{
                        $banksList['titleList'][] = $v;
                    }
                }
            }
            $this->banks = $banksList;
            $this->myCard = D('MyCardView')->where(['uid'=>UID,'status'=>1])->select();
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->seo = ['title' => '我的银行卡'];
            $this->display();
        }
	}

    /**
     * 银行卡删除
     */
    public function del(){
        if(IS_POST){
            list(
                $id,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.paypwd','')
            ];
            $id or ajaxMsg('请选择操作项',false);
            $paypwd or $this->error('请填写支付密码');
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                //调整卡状态到禁用
                M('MyCard')->save(['id'=>$id,'status'=>0]);
                //站内信
                $info = M('MyCard')->find($id);
                $title = '您禁用了一张银行卡';
                $user_con = time_format().'您禁用了一张银行卡，银行：'.$info['bank_name'].'，卡号：'.$info['card_number_format'];
                D('MessageNotices')->saveData(UID,$title,$user_con);
                $this->success('操作成功');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or $this->error('请选择操作项');
            $this->info = M('MyCard')->find($id);
            $this->display();
        }
    }

    /**
     * 银行卡编辑
     */
    public function edit(){
        if(IS_POST){
            list(
                $id,
                $card_number,
                $full_name,
                $paypwd
                )=[
                I('post.id',0,'int'),
                I('post.card_number',''),
                _safe(I('post.full_name','')),
                I('post.paypwd','')
            ];
            $id or ajaxMsg('请选择操作项',false);
            $card_number or $this->error('请填写银行卡号');
            $full_name or $this->error('请填写支行全称');
            $paypwd or $this->error('请填写支付密码');
            if(!$this->_checkHasPwd()){
                $this->error('请先设置支付密码',U('Uc/Password/paypwd'));
            }
            if($this->_checkPwd($paypwd,true)){
                $card_number_format_arr = str_split(trim($card_number),4);
                switch(count($card_number_format_arr)){
                    case 2:
                        $rarr = ['****'];
                        array_splice($card_number_format_arr,0,1,$rarr);
                        break;
                    case 3:
                        $rarr = ['****'];
                        array_splice($card_number_format_arr,1,1,$rarr);
                        break;
                    case 4:
                        $rarr = ['****','****'];
                        array_splice($card_number_format_arr,1,2,$rarr);
                        break;
                    case 5:
                    case 6:
                        $rarr = ['****','****'];
                        array_splice($card_number_format_arr,2,2,$rarr);
                        break;
                }
                //编辑信息
                M('MyCard')->save(['id'=>$id,'card_number'=>$card_number,'full_name'=>$full_name,'update_time'=>NOW_TIME,'card_number_format'=>implode(' ',$card_number_format_arr)]);
                //站内信
                $info = M('MyCard')->find($id);
                $title = '您修改了一张银行卡';
                $user_con = time_format().'您修改了一张银行卡，银行：'.$info['bank_name'].'，卡号：'.$info['card_number_format'];
                D('MessageNotices')->saveData(UID,$title,$user_con);
                $this->success('操作成功');
            }else{
                $this->error('支付密码错误');
            }
        }else{
            $id = I('get.id',0,'int');
            $id or $this->error('请选择操作项');
            $this->info = M('MyCard')->find($id);
            $this->display();
        }
    }

    /**
     * 验证是否设置支付密码
     * @return boolean     检测结果
     */
    private function _checkHasPwd() {
        $info = M('UcenterMember')->find(UID);
        return $info['pass_paypwd'];
    }

    /**
     * 验证密码
     * @param  string $password 密码
     * @param  boolean $paypwd 是否验证支付密码
     * @return boolean     检测结果
     */
    private function _checkPwd($password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser(UID, $password,$paypwd);
    }
}
