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
Class BookController extends RestController {

    Public function index() {
        switch ($this->_method){
            case 'get':
                $items_id = $_GET['items_id'];
                $lng = 121.10;
                $lat = 31.15;
                p($this->newVenue($items_id,$lng,$lat));
                break;
            case 'post': // post请求处理代码
                $lng = $_POST['lng'];
                $lat = $_POST['lat'];
                $items_id = $_POST['items_id'];
                $data = [
                    'status'=>true,
                    'info'=>[
                        'items'=>$this->items($items_id),
                        'lists'=>$this->newVenue($items_id,$lng,$lat)
                    ]
                ];
                $this->response($data,'json');
                break;
        }
    }

    //获取项目列表
    public function items($items_id){
        $items = M('items')->where(['id'=>['neq',$items_id],'status'=>1])->order('sort')->select();
        $info = M('items')->find($items_id);
        array_unshift($items,$info);
        return count($items)?$items:[];
    }
    //获取最新场馆
    public function newVenue($items_id,$lng,$lat){
        $venue_list = M('VenueItems')->where(['items_id'=>$items_id])->getField('vid',true);
        $venue_list = empty($venue_list)?[]:$venue_list;
        $list = M('Venue')->where(['id'=>['in',implode(',',$venue_list)],'status'=>8])->order('id desc')->limit(10)->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                //计算距离
                $dist = $this->getDistance($lng,$lat,$v['lng'],$v['lat']);
                $list[$k]['dist'] = round($dist/1000,2).'km';
                //获取图标
                $info = M('VenueAlbum')->where(['vid'=>$v['id'],'types'=>1,'status'=>1])->find();
                $root = 'http://139.196.39.46';
                if(empty($info)){
                    $list[$k]['icon'] = $root.'/Uploads/Venue/0.jpg';
                }else{
                    $list[$k]['icon'] = $root.get_cover($info['pid'],'path').'?'.NOW_TIME;
                }
                $list[$k]['items_id'] = $items_id;
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
}