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
Class HomeController extends RestController {

    Public function index() {
        switch ($this->_method){
            case 'get':
                p('get res');
                break;
            case 'post': // post请求处理代码
                $lng = $_POST['lng'];
                $lat = $_POST['lat'];
                $data = [
                    'status'=>true,
                    'info'=>[
                        'items'=>$this->items(),
                        'lists'=>$this->newVenue($lng,$lat)
                    ]
                ];
                $this->response($data,'json');
                break;
        }
    }
    //获取项目列表
    public function items(){
        $items = M('items')->where(['status'=>1])->order('sort')->limit(7)->select();
        return count($items)?$items:[];
    }
    //获取最新场馆
    public function newVenue($lng,$lat){
        $list = M('Venue')->where(['status'=>1])->order('id desc')->limit(10)->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $items_id = M('VenueItems')->where(['vid'=>$v['id'],'status'=>1])->getField('items_id');
                //计算距离
                $dist = $this->getDistance($lng,$lat,$v['lng'],$v['lat']);
                $list[$k]['dist'] = round($dist/1000,2).'km';
                //获取图标
                $info = M('VenueAlbum')->where(['vid'=>$v['id'],'types'=>1,'status'=>1])->find();
                $root = 'http://139.196.39.46';
                if(empty($info)){
                    $list[$k]['icon'] = $root.'/Uploads/Venue/0.jpg';
                }else{
                    $list[$k]['icon'] = $root.get_cover($info['pid'],'path');
                }
                $list[$k]['items_id'] = $items_id?$items_id:0;
            }
        }
        return $list ? $list : [];
    }

    //两点间的距离
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        /*
          Convert these degrees to radians
          to work with the formula
        */

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        /*
          Using the
          Haversine formula

          http://en.wikipedia.org/wiki/Haversine_formula

          calculate the distance
        */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }




    public function touzi(){
        switch ($this->_method){
            case 'get': // get请求处理代码
                $id = $_GET['id']?intval($_GET['id']):0;
                $list = M('MainBids')->field('id,own_funds,with_funds,profit,time_limit')->find($id);
                if(!empty($list)){
                    $list['id'] = intval($list['id']);
                    $list['funds'] = intval($list['own_funds'] + $list['with_funds']);
                    $list['income'] = 10000*$list['profit']/100/12;
                    $tender = M('MainTender')->where(['bid'=>$list['id']])->sum('money');
                    $list['speed'] = round($tender/($list['with_funds']*10000)*100,2);
                    $investor = M('MainTender')->where(['bid'=>$list['id']])->count();
                    $list['investor'] = $investor;
                }
                $this->response($list,'json');
                break;
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainBids')->field('id,own_funds,with_funds,profit,time_limit')->find($id);
                if(!empty($list)){
                    $list['id'] = intval($list['id']);
                    $list['funds'] = intval($list['own_funds'] + $list['with_funds']);
                    $list['income'] = 10000*$list['profit']/100/12;
                    $tender = M('MainTender')->where(['bid'=>$list['id']])->sum('money');
                    $list['speed'] = round($tender/($list['with_funds']*10000)*100,2);
                    $investor = M('MainTender')->where(['bid'=>$list['id']])->count();
                    $list['investor'] = $investor;
                }
                $this->response($list,'json');
                break;
        }
    }

    public function touzidetails(){
        switch ($this->_method){
            case 'get': // get请求处理代码
                $id = $_GET['id']?intval($_GET['id']):0;
                $list = M('MainBids')->field('id,uid,own_funds,with_funds,profit,time_limit,orders,status,time_end,interest_sum')->find($id);
                if(!empty($list)){
                    $userinfo = M('UcenterMember')->field(['mobile,reg_time,realname'])->find($list['uid']);
                    $realname = '**'.cut_str($userinfo['realname'],1,-1);
                    $mobile = substr_replace($userinfo['mobile'],'*****',3,5);

                    $data['orders'] = $list['orders'];
                    $data['statusText'] = $list['status']?'投标完成':'投标中';
                    $data['timeEnd'] = time_format($list['time_end']);

                    $data['realname'] = $realname;
                    $data['mobile'] = $mobile;
                    $data['regTime'] = time_format($userinfo['reg_time']);

                    $data['withFunds'] = $list['with_funds'].'万元';
                    $data['profit'] = $list['profit'].'%';
                    $data['timeLimit'] = $list['time_limit'].'个月';
                    $data['interest'] = money($list['interest_sum']);
                }
                $this->response($data,'json');
                break;
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainBids')->field('id,uid,own_funds,with_funds,profit,time_limit,orders,status,time_end,interest_sum')->find($id);
                if(!empty($list)){
                    $userinfo = M('UcenterMember')->field(['mobile,reg_time,realname'])->find($list['uid']);
                    $realname = '**'.cut_str($userinfo['realname'],1,-1);
                    $mobile = substr_replace($userinfo['mobile'],'*****',3,5);

                    $data['orders'] = $list['orders'];
                    $data['statusText'] = $list['status']?'投标完成':'投标中';
                    $data['timeEnd'] = time_format($list['time_end']);

                    $data['realname'] = $realname;
                    $data['mobile'] = $mobile;
                    $data['regTime'] = time_format($userinfo['reg_time']);

                    $data['withFunds'] = $list['with_funds'].'万元';
                    $data['profit'] = $list['profit'].'%';
                    $data['timeLimit'] = $list['time_limit'].'个月';
                    $data['interest'] = money($list['interest_sum']);
                }
                $this->response($data,'json');
                break;
        }
    }

    public function touziplan(){
        switch ($this->_method){
            case 'get': // get请求处理代码
                $id = $_GET['id']?intval($_GET['id']):0;
                $list = M('MainBids')->field('id,own_funds,with_funds')->find($id);
                if(!empty($list)){
                    $data['funds'] = money($list['own_funds']*10000+$list['with_funds']*10000);
                    $data['ownFunds'] = money($list['own_funds']*10000);
                    $data['withFunds'] = money($list['with_funds']*10000);
                }
                $this->response($data,'json');
                break;
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainBids')->field('id,own_funds,with_funds')->find($id);
                if(!empty($list)){
                    $data['funds'] = money($list['own_funds']*10000+$list['with_funds']*10000);
                    $data['ownFunds'] = money($list['own_funds']*10000);
                    $data['withFunds'] = money($list['with_funds']*10000);
                }
                $this->response($data,'json');
                break;
        }
    }

    public function touzibidlist(){
        switch ($this->_method){
            case 'get': // get请求处理代码
                $id = $_GET['id']?intval($_GET['id']):0;
                $list = M('MainTender')->where(['bid'=>$id])->field('id,money,total_revenue,username')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $data[$k]['money'] = money($v['money']);
                        $data[$k]['total_revenue'] = money($v['total_revenue']);
                        $data[$k]['username'] = $v['username'];
                    }
                }
                $this->response($data,'json');
                break;
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainTender')->where(['bid'=>$id])->field('id,money,total_revenue,username')->order('created_time desc')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $data[$k]['money'] = money($v['money']);
                        $data[$k]['total_revenue'] = money($v['total_revenue']);
                        $data[$k]['username'] = $v['username'];
                    }
                }
                $this->response($data,'json');
                break;
        }
    }

    public function touzinewinfo(){
        switch ($this->_method){
            case 'get': // get请求处理代码
                $id = $_GET['id']?intval($_GET['id']):0;
                $list = M('MainBids')->field('id,nid,own_funds,with_funds')->find($id);
                if(!empty($list)){
                    $trader = M('TraderChart')->where(['nid'=>$list['nid']])->field('id,total_assets,pre_value,operators')->order('created_time desc')->find();
                    $funds = money($list['own_funds']*10000+$list['with_funds']*10000);
                    $data['total_assets'] = $trader['total_assets']?money($trader['total_assets']):$funds;
                    $data['pre_value'] = $trader['pre_value']?money($trader['pre_value']):'暂无记录';
                    $data['operators'] = $funds;
                    $data['early'] = money($list['with_funds']*10000*112/100);
                    $data['unwinding'] = money($list['with_funds']*10000*108/100);
                }
                $this->response($data,'json');
                break;
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MainBids')->field('id,nid,own_funds,with_funds')->find($id);
                if(!empty($list)){
                    $trader = M('TraderChart')->where(['nid'=>$list['nid']])->field('id,total_assets,pre_value,operators')->order('created_time desc')->find();
                    $funds = money($list['own_funds']*10000+$list['with_funds']*10000);
                    $data['total_assets'] = $trader['total_assets']?money($trader['total_assets']):$funds;
                    $data['pre_value'] = $trader['pre_value']?money($trader['pre_value']):'暂无记录';
                    $data['operators'] = $funds;
                    $data['early'] = money($list['with_funds']*10000*112/100);
                    $data['unwinding'] = money($list['with_funds']*10000*108/100);
                }
                $this->response($data,'json');
                break;
        }
    }

    public function integrallists(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $list = M('LogScore')->where(['uid'=>$uid])->field('id,getval,score,created_time')->order('created_time desc')->page($pages.',10')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $list[$k]['datetime'] = time_format($v['created_time']);
                    }
                }
                $this->response($list,'json');
                break;
        }
    }

    public function messagelists(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $list = M('MessageNotices')->where(['uid'=>$uid])->field('id,is_read,title,created_time')->order('is_read,created_time desc')->page($pages.',10')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $list[$k]['datetime'] = time_format($v['created_time']);
                        switch($v['is_read']){
                            case 0:
                                $list[$k]['read'] = false;
                                break;
                            default:
                                $list[$k]['read'] = true;
                        }
                    }
                }
                $this->response($list,'json');
                break;
        }
    }

    public function messageread(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $id = $_POST['id']?intval($_POST['id']):0;
                $list = M('MessageNotices')->field('id,is_read,title,user_con,created_time')->find($id);
                if(!empty($list)){
                    $list['datetime'] = time_format($list['created_time']);
                    $list['user_con'] = strip_tags($list['user_con']);
                    M('MessageNotices')->save(['id'=>$id,'is_read'=>1]);
                }
                $this->response($list,'json');
                break;
        }
    }

    public function getassets(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $balance = M('UcenterMember')->where(['id'=>$uid])->getField('balance');
                $list['balance'] = money($balance);
                $spreadMoney = M('ExtensionLog')->where(['uid'=>$uid])->sum('yield');
                $countNum = M('MyCard')->where(['uid'=>$uid,['status'=>1]])->count('id');
                $list = [
                    'balance' => $balance?money($balance,'%s'):0.00,
                    'spreadMoney' => $spreadMoney?money($spreadMoney):'￥0.00',
                    'countNum' => $countNum?$countNum:0
                ];
                $this->response($list,'json');
                break;
        }
    }

    public function detaillists(){
        switch ($this->_method){
            case 'post': // post请求处理代码
                $uid = $_POST['uid']?intval($_POST['uid']):0;
                $pages = $_POST['pages']?intval($_POST['pages']):1;
                $list = M('MainFunds')->where(['uid'=>$uid])->field('id,amount,balance,remark,created_time')->order('id desc')->page($pages.',10')->select();
                if(!empty($list)){
                    foreach($list as $k => $v){
                        $list[$k]['datetime'] = time_format($v['created_time']);
                        $list[$k]['balance'] = money($v['balance'],'%s');
                        if($v['amount']>0){
                            $list[$k]['amount'] = money($v['amount'],'+%s');
                        }else{
                            $list[$k]['amount'] = money(abs($v['amount']),'-%s');
                        }
                    }
                }
                $this->response($list,'json');
                break;
        }
    }
}