<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Ground\Controller;

/**
 * 场馆管理首页控制器
 * @author waco <etoupcom@126.com>
 */

class IndexController extends AdminController {
    const DAYS = 28;
	/**
	 * 场馆管理首页
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
        $venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
        $this->venue_items = $venue_items;

        if(!empty($venue_items)){
            $week = date('D');
            $getweeks = $this->getWeeks();
            foreach($venue_items as $k => $v){
                //时间
                $venue_items[$k]['date_week'] = date('Y-m-d').' '.$getweeks[$week];
                //获取时间节点
                $venue_items[$k]['nodes'] = $this->getTimeNode($v['start'],$v['end'],$v['duration']);
                //获取场地信息
                $blocks = M('VenueBlock')->where(['vid'=>VID,'items_id'=>$v['items_id']])->select();
                $venue_items[$k]['blocks'] = $blocks;
                if(!empty($blocks)){
                    foreach($blocks as $key=>$val){
                        //获取价格
                        $prices = M('OrdersTemp')->where(['vid'=>VID,'items_id'=>$v['items_id'],'bid'=>$val['id'],'week'=>$week])->select();
                        if(!empty($prices)){
                            foreach($prices as $kp=>$vp){
                                if($info = M('Orders')->where(['tid'=>$vp['id'],'nodes'=>date('Y-m-d'),'status'=>['in',[1,2]]])->find()){
                                    $prices[$kp]['has_order'] = true;
                                    $prices[$kp]['order_id'] = $info['id'];
                                }
                                if($lockinfo = M('Locks')->where(['tid'=>$vp['id'],'nodes'=>date('Y-m-d'),'status'=>1])->find()){
                                    $prices[$kp]['status'] = 0;
                                }
                            }
                        }
                        $venue_items[$k]['blocks'][$key]['prices'] = $prices;
                    }
                }
            }
        }
        $this->list = $venue_items;
        $this->albums = D('VenueAlbumView')->where(['vid'=>VID,'status'=>1,'types'=>0])->select();
        $this->active = 'Index';
		$this->meta_title = '场馆管理首页';
		$this->display();
	}
    //评论管理
    public function comment(){
        if(IS_POST){
            $reply = I('post.reply','');
            $id = I('post.id',0,'int');
            $reply or $this->error('请填写回复内容');
            $id or $this->error('请选择操作项');
            M('Comments')->save(['id'=>$id,'reply'=>$reply,'update_time'=>NOW_TIME]);
            $this->success('操作成功');
        }else{
            $map = ['vid'=>VID];
            $count = M('Comments')->where($map)->count('id');
            $this->score = round(STARS/($count*5)*100,2);
            $this->_list = $this->lists('CommentsView',$map,'',true,1);
            $this->albums = D('VenueAlbumView')->where(['vid'=>VID,'status'=>1])->select();
            $venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
            $this->venue_items = $venue_items;
            $this->active = 'Index';
            $this->meta_title = '场馆管理首页';
            $this->display();
        }
    }

    //预定管理
    public function reserve(){
        if(IS_POST){
            list(
                $items,
                $block,
                $ordersdate,
                $weeks,
                $start,
                $end,
                $paytypes,
                $paystatus,
                $mobile,
                $money
                ) = array(
                I('items', 0,'int'),
                I('block', 0,'int'),
                I('ordersdate', ''),
                I('weeks', ''),
                I('start', ''),
                I('end', ''),
                I('paytypes', 0, 'int'),
                I('paystatus', 0, 'int'),
                I('mobile', ''),
                I('money', '')
            );
            $items ? $data['items_id'] = $items : $this->error('请选择项目');
            $block ? $data['bid'] = $block : $this->error('请选择场地');

            if($ordersdate){
                $dateArr = explode(' - ',$ordersdate);
                if(strtotime($dateArr[0]) < NOW_TIME){
                    $this->error('起始日期必须选择大于当前时间的日期');
                }
                $days = round((strtotime($dateArr[1])-strtotime($dateArr[0]))/3600/24);
                //获取项目对应控制周期
                $info = M('Items')->find($items);
                if($days < $info['days']){
                    $this->error('项目'.$info['name'].'批量预定的最小天数为'.$info['days'].'天');
                }
                $data['startdate'] = strtotime($dateArr[0]);
                $data['enddate'] = strtotime($dateArr[1]);
            }else{
                $this->error('请选择时间');
            }
            $weeks ? $data['week'] = $weeks : $this->error('请选择星期');
            $start ? $data['starttime'] = $start : $this->error('请选择开始时间');
            $end ? $data['endtime'] = $end : $this->error('请选择结束时间');

            $venue_items = M('VenueItems')->where(['vid'=>VID,'items_id'=>$items])->find();
            if($start < $venue_items['start']){
                $this->error('该项目的开放时间：'.$venue_items['start'].'-'.$venue_items['end']);
            }
            if($end > $venue_items['end']){
                $this->error('该项目的开放时间：'.$venue_items['start'].'-'.$venue_items['end']);
            }

            $paytypes ? $data['paytypes'] = $paytypes : $this->error('请选择支付方式');
            $paystatus ? $data['paystatus'] = $paystatus : $this->error('请选择是否支付');

            $money ? $data['money'] = $money : $this->error('请填写支付金额');
            is_numeric($mobile) or $this->error('请正确填写支付金额');

            $mobile ? $data['mobile'] = $mobile : $this->error('请填写预定手机');
            check_mobile($mobile) or $this->error('请正确填写预定手机');
            $user_info = M('UcenterMember')->where(['mobile'=>$mobile])->field('id,username')->find();
            $user_info or $this->error('该手机没有完成注册，无法预定');

            $data['vid'] = VID;
            $data['created_time'] = NOW_TIME;
            $log_id = M('ReserveLog')->add($data);
            if($log_id){
                //根据星期获取日期节点
                $i = 1;
                do {
                    $time = strtotime('+'.$i.' week last '.$weeks);
                    if($time <= $data['enddate'])
                        $nodes[] = date('Y-m-d',$time);
                    $i++;
                } while ($time <= $data['enddate']);

                $time_nodes = $this->getTimeNode($start,$end,$venue_items['duration']);
                if(!empty($nodes)){
                    foreach($nodes as $k=>$v){
                        if(!empty($time_nodes)){
                            foreach($time_nodes as $key=>$val){
                                //取模板
                                $map = [
                                    'vid'=>VID,
                                    'items_id'=>$items,
                                    'bid'=>$block,
                                    'week'=>$weeks,
                                    'start'=>$val['start'],
                                    'end'=>$val['end'],
                                    'status'=>1
                                ];
                                $temp_info = M('OrdersTemp')->where($map)->find();
                                if($temp_info){
                                    $orderinfo = M('Orders')->where(['tid'=>$temp_info['id'],'nodes'=>$v])->find();
                                    if(empty($orderinfo)){
                                        $mod = D('Orders');
                                        $date_time = $v.' '.$val['end'];
                                        $end_time = strtotime($date_time);
                                        $mod->addData([
                                            'uid'=>UID,
                                            'vid'=>VID,
                                            'items_id'=>$items,
                                            'bid'=>$block,
                                            'tid'=>$temp_info['id'],
                                            'nodes'=>$v,
                                            'start'=>$val['start'],
                                            'end'=>$val['end'],
                                            'price'=>$temp_info['price'],
                                            'status'=>$paystatus,
                                            'types'=>1,
                                            'reserve_id'=>$user_info['id'],
                                            'end_time'=>$end_time,
                                            'log_id'=>$log_id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }

        }else{
//           $week = 'Sat';
//            p(date('Y-m-d',strtotime('+1 week last '.$week)));
//            p(strtotime('+1 week last '.$week));
            $list = $this->lists('ReserveLogView',['vid'=>VID],'',true,1);
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $weeks = $this->getWeeks();
                    $week = $v['week'];
                    $list[$k]['week'] = $weeks[$week];
                }
            }
            $this->list = $list;
            $this->startDate = date('Y-m-d',strtotime('+1 day'));
            $this->endDate = date('Y-m-d',strtotime('+ '.self::DAYS.' day',strtotime($this->startDate)));
            $this->weeks = $this->getWeeks();
            $this->paytypes = $this->getPayTypes();
            $this->albums = D('VenueAlbumView')->where(['vid'=>VID,'status'=>1])->select();
            $venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
            $this->venue_items = $venue_items;
            $this->active = 'Index';
            $this->meta_title = '场馆管理首页';
            $this->display();
        }
    }

    //锁定管理
    public function lock(){
        if(IS_POST){
            list(
                $items,
                $block,
                $ordersdate,
                $weeks,
                $start,
                $end
                ) = array(
                I('items', 0,'int'),
                I('block', 0,'int'),
                I('ordersdate', ''),
                I('weeks', ''),
                I('start', ''),
                I('end', '')
            );
            $items ? $data['items_id'] = $items : $this->error('请选择项目');
            $block ? $data['bid'] = $block : $this->error('请选择场地');

            if($ordersdate){
                $dateArr = explode(' - ',$ordersdate);
                if(strtotime($dateArr[0]) < NOW_TIME){
                    $this->error('起始日期必须选择大于当前时间的日期');
                }
                $days = round((strtotime($dateArr[1])-strtotime($dateArr[0]))/3600/24);
                //获取项目对应控制周期
                $info = M('Items')->find($items);
                if($days < $info['days']){
                    $this->error('项目'.$info['name'].'批量预定的最小天数为'.$info['days'].'天');
                }
                $data['startdate'] = strtotime($dateArr[0]);
                $data['enddate'] = strtotime($dateArr[1]);
            }else{
                $this->error('请选择时间');
            }
            $weeks ? $data['week'] = $weeks : $this->error('请选择星期');
            $start ? $data['starttime'] = $start : $this->error('请选择开始时间');
            $end ? $data['endtime'] = $end : $this->error('请选择结束时间');

            $venue_items = M('VenueItems')->where(['vid'=>VID,'items_id'=>$items])->find();
            if($start < $venue_items['start']){
                $this->error('该项目的开放时间：'.$venue_items['start'].'-'.$venue_items['end']);
            }
            if($end > $venue_items['end']){
                $this->error('该项目的开放时间：'.$venue_items['start'].'-'.$venue_items['end']);
            }

            $data['vid'] = VID;
            $data['created_time'] = NOW_TIME;
            $log_id = M('LockLog')->add($data);
            if($log_id){
                //根据星期获取日期节点
                $i = 1;
                do {
                    $time = strtotime('+'.$i.' week last '.$weeks);
                    if($time <= $data['enddate'])
                        $nodes[] = date('Y-m-d',$time);
                    $i++;
                } while ($time <= $data['enddate']);

                $time_nodes = $this->getTimeNode($start,$end,$venue_items['duration']);
                if(!empty($nodes)){
                    foreach($nodes as $k=>$v){
                        if(!empty($time_nodes)){
                            foreach($time_nodes as $key=>$val){
                                //取模板
                                $map = [
                                    'vid'=>VID,
                                    'items_id'=>$items,
                                    'bid'=>$block,
                                    'week'=>$weeks,
                                    'start'=>$val['start'],
                                    'end'=>$val['end'],
                                    'status'=>1
                                ];
                                $temp_info = M('OrdersTemp')->where($map)->find();
                                if($temp_info){
                                    $orderinfo = M('Locks')->where(['tid'=>$temp_info['id'],'nodes'=>$v])->find();
                                    if(empty($orderinfo)){
                                        $mod = D('Locks');
                                        $mod->addData([
                                            'vid'=>VID,
                                            'items_id'=>$items,
                                            'bid'=>$block,
                                            'tid'=>$temp_info['id'],
                                            'nodes'=>$v,
                                            'start'=>$val['start'],
                                            'end'=>$val['end'],
                                            'status'=>1,
                                            'log_id'=>$log_id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            $list = $this->lists('LockLogView',['vid'=>VID,'status'=>1],'',true,1);
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $weeks = $this->getWeeks();
                    $week = $v['week'];
                    $list[$k]['week'] = $weeks[$week];
                }
            }
            $this->list = $list;
            $this->startDate = date('Y-m-d',strtotime('+1 day'));
            $this->endDate = date('Y-m-d',strtotime('+ '.self::DAYS.' day',strtotime($this->startDate)));
            $this->weeks = $this->getWeeks();
            $this->albums = D('VenueAlbumView')->where(['vid'=>VID,'status'=>1])->select();
            $venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
            $this->venue_items = $venue_items;
            $this->active = 'Index';
            $this->meta_title = '场馆管理首页';
            $this->display();
        }
    }

    public function lockdel(){
        $id = I('post.id',0,'int');
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        $bid = M('LockLog')->save(['id'=>$id,'status'=>-1]);
        if($bid){
            M('Locks')->where(['log_id'=>$id])->save(['status'=>-1]);
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
    }

    public function ordersview(){
        $id = I('get.id',0,'int');
        $id or $this->error('请选择操作项');
        $this->info = D('OrdersView')->find($id);
        //p($this->info,0);
        $this->display();
    }

    public function reserves(){
        $id = I('post.id',[]);
        $id or $data_id = ['status'=>false,'msg'=>'请选择操作项'];
        if($data_id){
            $this->ajaxReturn($data_id);
        }
        $mobile = I('post.mobile','');
        $mobile or $data_mobile = ['status'=>false,'msg'=>'请填写预定人手机号码'];
        if(!check_mobile($mobile)){
            $data_mobile = ['status'=>false,'msg'=>'请正确填写预定人手机号码'];
        }
        if($data_mobile){
            $this->ajaxReturn($data_mobile);
        }
        if(!$mid = M('UcenterMember')->where(['mobile'=>$mobile])->getField('id')){
            $this->ajaxReturn(['status'=>false,'msg'=>'该号码没有在平台注册，无法预定']);
        }
        $bid = M('OrdersTemp')->where(['id'=>['in',$id],'vid'=>VID])->select();
        if($bid){
            $date = date('Y-m-d');
            foreach($bid as $k=>$v){
                $mod = D('Orders');
                $date_time = $date.' '.$v['end'];
                $end_time = strtotime($date_time);
                $mod->addData([
                    'uid'=>UID,
                    'items_id'=>$v['items_id'],
                    'vid'=>$v['vid'],
                    'bid'=>$v['bid'],
                    'tid'=>$v['id'],
                    'nodes'=>$date,
                    'start'=>$v['start'],
                    'end'=>$v['end'],
                    'price'=>$v['price'],
                    'status'=>1,
                    'types'=>1,
                    'reserve_id'=>$mid,
                    'end_time'=>$end_time
                ]);
            }
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
    }

    private function getTimeNode($start,$end,$duration){
        $node = ceil((strtotime($end)-strtotime($start))/($duration*60));
        for($i=0;$i<$node;$i++){
            $date_time_start = strtotime('+'.$duration*$i.' minutes',strtotime($start));
            $date_time_end = strtotime('+'.$duration*($i+1).' minutes',strtotime($start));
            $time_node[] = [
                'start'=>date('H:i',$date_time_start),
                'end'=>date('H:i',$date_time_end)
            ];
        }
        return $time_node;
    }
    //获取周
    private function getWeeks(){
        return [
            'Mon'=>'星期一',
            'Tue'=>'星期二',
            'Wed'=>'星期三',
            'Thu'=>'星期四',
            'Fri'=>'星期五',
            'Sat'=>'星期六',
            'Sun'=>'星期日'
        ];
    }

    //获取支付方式
    private function getPayTypes(){
        return [
            '1'=>'现金支付',
            '2'=>'刷卡支付',
            '3'=>'网银转账',
            '4'=>'支付宝',
            '5'=>'微信',
            '6'=>'其他'
        ];
    }

    public function bmap(){
        $this->display();
    }

    public function getItems(){
        $list = D('VenueItemsView')->where(['vid'=>VID,'status'=>1])->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $date[] = [
                    'name'=>$v['name'],
                    'value'=>$v['items_id']
                ];
            }
        }
        $this->ajaxReturn($date);
    }
    public function getBlocks(){
        $items = I('get.items',0,'int');
        $list = M('VenueBlock')->where(['vid'=>VID,'items_id'=>$items])->select();
        $data['state'] = 'success';
        $data['data'] = [];
        if(!empty($list)){
            foreach($list as $k=>$v){
                $d[] = [
                    'name'=>$v['name'],
                    'value'=>$v['id']
                ];
            }
            $data['data'] = $d;
        }
        $this->ajaxReturn($data);
    }

    //上传场馆图标
    public function uploadimg(){
        $base64_image_content = I('post.img','',false);
        if($base64_image_content){
            $imgname = $this->base64_upload($base64_image_content);
            if($imgname){
                $md5_name = md5($imgname);
                $sha1_name = sha1($imgname);

                $info = M('Picture')->where(['md5'=>$md5_name,'sha1'=>$sha1_name])->find();
                if($info){
                    $path = $info['path'];
                    $pid = $info['id'];
                }else{
                    $path = '/Uploads/Venue/'.$imgname;
                    $pid = M('Picture')->add(['path'=>$path,'md5'=>$md5_name,'sha1'=>$sha1_name,'status'=>1,'create_time'=>NOW_TIME]);
                }
                $ainfo = M('VenueAlbum')->where(['vid'=>VID,'types'=>1])->find();
                if($ainfo){
                    M('VenueAlbum')->save(['id'=>$ainfo['id'],'pid'=>$pid]);
                }else{
                    M('VenueAlbum')->add(['vid'=>VID,'pid'=>$pid,'types'=>1]);
                }
                $data = [
                    'status' => true,
                    'path'  => $path,
                    'time'  => NOW_TIME
                ];
                $this->ajaxReturn($data);
            }else{

            }

        }
    }

    public function base64_upload($base64) {
        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            if($result[2] == 'jpeg'){
                //$image_name = uniqid().'.jpg';
                $image_name = VID.'.jpg';
                //纯粹是看jpeg不爽才替换的
            }else{
                $image_name = VID.'.'.$result[2];
            }
            $image_file = "./Uploads/Venue/{$image_name}";
            //服务器文件存储路径
            if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
                return $image_name;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
