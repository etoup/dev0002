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
Class MycardController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
    }
    public function cardlists(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $info = M('MyCard')->where(['uid'=>$uid,'status'=>1])->select();
                if($info){
                    $list = $info;
                }
                $this->response($list,'json');
                break;
        }
    }
    public function remove(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $cid = $_POST['cid']?intval($_POST['cid']):0;
                $info = M('MyCard')->where(['id'=>$cid,'status'=>1])->find();
                if($info){
                    $list['num'] = substr($info['card_number'],-4);
                }
                $this->response($list,'json');
                break;
        }
    }
    public function doremove(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $cid = $_POST['cid']?intval($_POST['cid']):0;
                $paypwd = $_POST['paypwd']?$_POST['paypwd']:'';
                if($this->_checkPwd($uid,$paypwd,true)){
                    //调整卡状态到禁用
                    M('MyCard')->save(['id'=>$cid,'status'=>0]);
                    //站内信
                    $info = M('MyCard')->find($cid);
                    $title = '您删除了一张银行卡';
                    $user_con = time_format().'您删除了一张银行卡，银行：'.$info['bank_name'].'，卡号：'.$info['card_number_format'];
                    D('MessageNotices')->saveData($uid,$title,$user_con);
                    $list['num'] = substr($info['card_number'],-4);
                }else{
                    $list['msg'] = '支付密码错误';
                }
                $this->response($list,'json');
                break;
        }
    }
    public function userinfo(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $info = M('UcenterMember')->field('id,realname')->find($uid);
                if($info){
                    $list['realname'] = $info['realname'];
                }
                $this->response($list,'json');
                break;
        }
    }
    public function banklists(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $info = M('Banks')->field('id,name,code')->order('sort,id')->page($pages.',10')->select();
                if($info){
                    $list = $info;
                }
                $this->response($list,'json');
                break;
        }
    }
    public function banktagslists(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $info = M('Banks')->where('tags IS NOT NULL')->field('id,name,code')->order('sort,id')->page($pages.',10')->select();
                if($info){
                    $list = $info;
                }
                $this->response($list,'json');
                break;
        }
    }
    public function getbank(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $bid = $_POST['bid']?intval($_POST['bid']):0;
                $info = M('Banks')->find($bid);
                $selectTxt = $info['name'];
                $data = [
                    'selectTxt' => $selectTxt
                ];
                $this->response($data,'json');
                break;
        }
    }
    public function addcard(){
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $bid = $_POST['bid']?intval($_POST['bid']):0;
                $full_name = $_POST['full_name']?$_POST['full_name']:'';
                $card_number = $_POST['card_number']?$this->trimall($_POST['card_number']):'';
                if(empty($uid)){
                    $list['msg'] = '请登录';
                    $this->response($list,'json');
                }
                if(empty($bid)){
                    $list['msg'] = '请选择所属银行';
                    $this->response($list,'json');
                }
                if(empty($full_name)){
                    $list['msg'] = '请填写支行全称';
                    $this->response($list,'json');
                }
                if(empty($card_number)){
                    $list['msg'] = '请填写银行卡号';
                    $this->response($list,'json');
                }
                if(!is_numeric($card_number)){
                    $list['msg'] = '银行卡号必须是数字';
                    $this->response($list,'json');
                }
                $data['uid'] = $uid;
                $data['bid'] = $bid;
                $data['card_number'] = $card_number;
                $bankinfo = M('Banks')->find($bid);
                $data['bank_name'] = $bankinfo['name'];
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
                $list['num'] = substr($card_number,-4);
                $this->response($list,'json');
                break;
        }
    }
    //删除空格
    public function trimall($str)
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }
    /**
     * 验证是否设置支付密码
     * @return boolean     检测结果
     */
    private function _checkHasPwd($uid) {
        $info = M('UcenterMember')->find($uid);
        return $info['pass_paypwd'];
    }

    /**
     * 验证密码
     * @param  string $password 密码
     * @param  boolean $paypwd 是否验证支付密码
     * @return boolean     检测结果
     */
    private function _checkPwd($uid,$password,$paypwd = false) {
        $User = new UserApi;
        return $User->verifyUser($uid, $password,$paypwd);
    }
}