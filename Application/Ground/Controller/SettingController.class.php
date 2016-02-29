<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Ground\Controller;
use Common\Api\BaiduApi;
/**
 * 场馆管理设置控制器
 * @author waco <etoupcom@126.com>
 */

class SettingController extends AdminController {

	/**
	 * 场馆管理基础设置
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
        if (IS_POST) {
            $rules = array(
                array('address','require','地址必须填写'),
                array('city','require','城市必须填写'),
                array('district','require','区县必须填写'),
                array('lng','require','经度必须填写'),
                array('lat','require','纬度必须填写'),
                array('summary','require','简介必须填写'),
                array('tel','require','电话必须填写'),
                array('bus','require','公交必须填写')
            );

            $Venue = M("Venue"); // 实例化User对象
            if (!$Venue->validate($rules)->create()){
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($Venue->getError());
            }else{
                $services = I('post.services/a',[]);
                $data = I('post.');
                $data['services'] = implode($services,',');
                $data['update_time']= NOW_TIME;
                if ($Venue->where(['uid'=>UID])->save($data)) {
                    //操作商圈信息
                    $circle = I('post.circle','');
                    if($circle){
                        $where = ['name'=>$circle,'city'=>I('post.city')];
                        $info = M('Circle')->where($where)->find();
                        if(empty($info)){
                            M('Circle')->add($where);
                        }
                    }
                    $this->success('操作成功');
                } else {
                    $this->error('操作失败');
                }
            }

        }else {
            //$ip = get_client_ip();
//            $ip = '211.137.76.143';
//            $BaiduApi = new BaiduApi();
//            $info = $BaiduApi->locationByIP($ip);
//            $this->info = $info;
            //p($info);
            $this->albums = D('VenueAlbumView')->where(['vid'=>VID,'status'=>1,'types'=>0])->select();
            //p($this->albums);
            $this->venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
            $this->material = C('MATERIAL');
            $this->light = C('LIGHT');
            $this->active = 'Setting_index';
            $this->meta_title = '基础设置';
            $this->display();
        }
	}

    public function items(){
        if (IS_POST) {
            $rules = array(
                array('items_id','require','请选择项目'),
                array('start','require','开始时间必须填写'),
                array('end','require','结束时间必须填写'),
                array('duration','require','运动时长必须填写'),
                array('price','require','初始价格必须填写')
            );

            $VenueItems = M("VenueItems"); // 实例化User对象
            if (!$VenueItems->validate($rules)->create()){
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($VenueItems->getError());

            }else{
                $data = I('post.');
                is_numeric($data['items_id']) or $this->error('请选择项目');
                is_numeric($data['duration']) or $this->error('运动时长必须是数字');
                is_numeric($data['price']) or $this->error('初始价格必须是数字');

                if(strtotime($data['start']) >= strtotime($data['end'])){
                    $this->error('结束时间必须大于开始时间');
                }
                $venue = M('Venue')->where(['uid'=>UID])->find();
                $data['vid'] = $venue['id'];
                $data['created_time']= NOW_TIME;
                $has_item = $VenueItems->where(['items_id'=>$data['items_id'],'vid'=>$venue['id']])->find();
                if(!$has_item){
                    if ($VenueItems->add($data)) {
                        //TODO:在此处理初始化还是在场地设置处理？
                        //生成场地
                        for($i=0;$i<$data['num'];$i++){
                            $n = $i+1;
                            $block_data = [
                                'name'=>'场地'.$n,
                                'items_id'=>$data['items_id'],
                                'vid'=>VID,
                                'created_time'=>NOW_TIME
                            ];
                            $bid = M('VenueBlock')->add($block_data);
                            $this->getWeeksNodes($bid,$data);
                        }
                        M('Venue')->save(['id'=>$venue['id'],'status'=>8]);
                        $this->success('操作成功');
                    } else {
                        $this->error('操作失败');
                    }
                }else{
                    $this->error('请不要重复添加项目');
                }
            }

        }else {
//            $start = '06:30';
//            $date_time_start = $start;
//            $date_time_end = strtotime('+90 minutes',strtotime($date_time_start));
//            p(date('h:i',$date_time_end));


            $this->items = M('Items')->where(['status'=>1])->select();
            $this->venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
            $this->active = 'Setting_items';
            $this->meta_title = '项目设置';
            $this->display();
        }
    }

    public function block(){
        if (IS_POST) {

        }else{
            $items_id = I('get.items_id',0,'int');
            $this->items_id = $items_id;
            $venue_items = D('VenueItemsView')->where(['vid'=>VID])->select();
            if(!empty($venue_items)){
                foreach($venue_items as $key=>$val){
                    $venue_items[$key]['nums']=M('VenueBlock')->where(['vid'=>VID,'status'=>1,'items_id'=>$val['items_id']])->count('id');
                }
            }
            $this->venue_items = $venue_items;
            //$venue_block = M('VenueBlock')->where(['vid'=>VID,'items_id'=>$items_id,'status'=>1])->select();
            $venue_block = $this->lists('VenueBlock',['vid'=>VID,'items_id'=>$items_id,'status'=>1],'id');
            $weeks = $this->getWeeks();
            $info = M('VenueItems')->where(['vid'=>VID,'items_id'=>$items_id])->find();
            $this->time_node = $this->getTimeNode($info['start'],$info['end'],$info['duration']);
            if(!empty($venue_block)){
                foreach($venue_block as $k=>$v){
                    foreach($weeks as $kv=>$vv){
                        $nodes = M('OrdersTemp')->where(['bid'=>$v['id'],'week'=>$kv])->select();
                        $duration = M('VenueItems')->where(['vid'=>VID,'items_id'=>$v['items_id']])->getField('duration');
                        $week[$kv] =  [
                            'week'=>$vv,
                            'duration'=>$duration,
                            'nodes'=>$nodes
                        ];
                        $list[$k] = [
                            'id'=>$v['id'],
                            'name'=>$v['name'],
                            'remark'=>$v['remark'],
                            'created_time'=>time_format($v['created_time']),
                            'weeks' => $week
                        ];
                    }
                }
                $this->list = $list;
            }
            $this->active = 'Setting_block';
            $this->meta_title = '场地设置';
            $this->display();
        }
    }

    public function blockadd(){
        if(IS_POST){
            $items_id = I('post.items_id',0,'int');
            $items_id or $this->error('请选择操作项');
            $name = I('post.name','');
            $name or $this->error('请填写场地名称');
            $bid = M('VenueBlock')->add(['name'=>$name,'vid'=>VID,'items_id'=>$items_id,'remark'=>I('post.remark',''),'created_time'=>NOW_TIME]);
            if($bid){
                $items_info = M('VenueItems')->where(['vid'=>VID,'items_id'=>$items_id])->find();
                $this->getWeeksNodes($bid,$items_info);
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }else{
            $items_id = I('get.items_id',0,'int');
            $items_id or $this->error('请选择操作项目');
            $this->info = M('Items')->find($items_id);
            $this->items_id = $items_id;
            $this->title = '新增场地';
            $this->display();
        }
    }

    public function blockedit(){
        if(IS_POST){
            $id = I('post.id',0,'int');
            $id or $this->error('请选择操作项');
            $name = I('post.name','');
            $name or $this->error('请填写调整后的场地名称');
            $bid = M('VenueBlock')->save(['id'=>$id,'name'=>$name,'remark'=>I('post.remark',''),'update_time'=>NOW_TIME]);
            if($bid)
                $this->success('操作成功');
            else
                $this->error('操作失败');
        }else{
            $id = I('get.id',0,'int');
            $id or $this->error('请选择操作项');
            $this->info = M('VenueBlock')->find($id);
            $this->title = '编辑场地信息';
            $this->display();
        }
    }

    public function blockdel(){
        $id = I('post.id',0,'int');
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        $bid = M('VenueBlock')->save(['id'=>$id,'status'=>-1]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
    }

    public function tempedit(){
        if(IS_POST){
            $id = I('post.id',0,'int');
            $id or $this->error('请选择操作项');
            $price = I('post.price',0,'int');
            $price or $this->error('请填写调整后的价格');
            $bid = M('OrdersTemp')->where(['id'=>$id,'vid'=>VID])->save(['price'=>$price,'update_time'=>NOW_TIME]);
            if($bid)
                $this->success('操作成功');
            else
                $this->error('操作失败');
        }else{
            $id = I('get.id',0,'int');
            $id or $this->error('请选择操作项');
            $this->info = M('OrdersTemp')->find($id);
            $this->title = '修改价格';
            $this->display();
        }
    }
    //批量修改选中项
    public function tempEditArr(){
        $id = I('post.id',[]);
        $id or $data_id = ['status'=>false,'msg'=>'请选择操作项'];
        if($data_id){
            $this->ajaxReturn($data_id);
        }
        $price = I('post.price',0,'int');
        $price or $data_price = ['status'=>false,'msg'=>'请填写调整后的价格'];
        if($data_price){
            $this->ajaxReturn($data_price);
        }
        $bid = M('OrdersTemp')->where(['id'=>['in',$id],'vid'=>VID])->save(['price'=>$price,'update_time'=>NOW_TIME]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
    }

    public function tempeditprice(){
        if(IS_POST){
            $id = I('post.id',0,'int');
            $id or $this->error('请选择操作项');
            $weeks = I('post.weeks',[]);
            $price = I('post.price',0,'int');
            $price or $this->error('请填写调整后的价格');
            if(empty($weeks)){
                $where = ['bid'=>$id,'vid'=>VID];
            }else{
                $where = ['bid'=>$id,'vid'=>VID,'week'=>['in',$weeks]];
            }
            $bid = M('OrdersTemp')->where($where)->save(['price'=>$price,'update_time'=>NOW_TIME]);
            if($bid)
                $this->success('操作成功');
            else
                $this->error('操作失败');
        }else{
            $id = I('get.id',0,'int');
            $id or $this->error('请选择操作项');
            $this->id = $id;
            $this->weeks = $this->getWeeks();
            $this->title = '批量修改价格';
            $this->display();
        }
    }

    public function tempdel(){
        $id = I('post.id',0,'int');
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        $bid = M('OrdersTemp')->save(['id'=>$id,'status'=>-1]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }

    }

    //批量删除选中项
    public function tempDelArr(){
        $id = I('post.id',[]);
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        if($data){
            $this->ajaxReturn($data);
        }
        $bid = M('OrdersTemp')->where(['id'=>['in',$id]])->save(['status'=>-1]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }

    }

    //批量锁定选中项
    public function tempLockArr(){
        $id = I('post.id',[]);
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        if($data){
            $this->ajaxReturn($data);
        }
        $bid = M('OrdersTemp')->where(['id'=>['in',$id]])->save(['status'=>0,'update_time'=>NOW_TIME]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }

    }

    //批量锁定选中项
    public function tempUnlockArr(){
        $id = I('post.id',[]);
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        if($data){
            $this->ajaxReturn($data);
        }
        $bid = M('OrdersTemp')->where(['id'=>['in',$id]])->save(['status'=>1,'update_time'=>NOW_TIME]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }

    }

    public function uploadimg(){
        if(IS_POST){
            $icon = I('post.icon',0,'int');
            $icon or $this->ajaxReturn(['status'=>false]);
            $where = ['vid'=>VID,'pid'=>$icon,'status'=>1];
            if(!$info = M('VenueAlbum')->where($where)->find()){
                $map = ['vid'=>VID,'pid'=>$icon];
                M('VenueAlbum')->add($map);
            }
            $this->ajaxReturn(['status'=>true]);
        }else{
            $this->title = '新增场馆相册';
            $this->display();
        }
    }

    public function imgdel(){
        $bid = M('VenueAlbum')->where(['vid'=>VID,'types'=>0])->save(['status'=>-1]);
        if($bid){
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

    private function getWeeksNodes($bid,$data){
        //生成周每天
        $weeks = $this->getWeeks();
        foreach($weeks as $k=>$w){
            $time_node = $this->getTimeNode($data['start'],$data['end'],$data['duration']);
            if(!empty($time_node)){
                foreach($time_node as $v){
                    $temp_data = [
                        'vid'=>VID,
                        'bid'=>$bid,
                        'items_id'=>$data['items_id'],
                        'week'=>$k,
                        'start'=>$v['start'],
                        'end'=>$v['end'],
                        'price'=>$data['price'],
                        'created_time'=>NOW_TIME
                    ];
                    M('OrdersTemp')->add($temp_data);
                }
            }
        }
    }
}
