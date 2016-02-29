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
Class VenueController extends RestController {

    Public function index() {
        switch ($this->_method){
            case 'get':
//                p($_SERVER);
                p($this->getItems(1));
                break;
            case 'post': // post请求处理代码
                $vid = $_POST['vid'];
                $items_id = $_POST['items_id'];
                $data = [
                    'status'=>true,
                    'info'=>[
                        'venue' => $this->getVenue($vid,$items_id),
                        'pathinfo' => $this->getAlbumInfo($vid),
                        'comments' => $this->getComments($vid),
                        'weeks' => $this->getWeeks($vid),
                        'items' => $this->getItems($vid)
                    ]
                ];
                $this->response($data,'json');
                break;
        }
    }
    // 获取项目
    public function getItems($vid){
        $list = D('VenueItemsView')->where(['vid'=>$vid])->select();
        return $list?$list:[];
    }
    // 获取场馆信息
    public function getVenue($vid,$items_id=0){
        $info = M('Venue')->find($vid);
        if($info['material']){
            $config = $this->getConfig();
            $info['material'] = $config['MATERIAL'][$info['material']];
        }
        if($info['light']){
            $config = $this->getConfig();
            $info['light'] = $config['LIGHT'][$info['light']];
        }

        if($items_id){
            $info['items_id'] = $items_id;
        }else{
            $info['items_id'] = M('VenueItems')->where(['vid'=>$vid])->order('id')->find();
        }
        return $info;
    }

    // 获取场馆相册信息
    public function getAlbumInfo($vid){
        $paths = $this->getAlbums($vid);
        $config = $this->getConfig();
        $data = [
            'num' => count($paths),
            'path'=> $paths[0]['path']['path']?$config['HTTPHOST'].$paths[0]['path']['path']:''
        ];
        return $data?$data:[];
    }
    // 获取场馆相册
    public function getAlbums($vid){
        $list = M('VenueAlbum')->where(['vid'=>$vid,'status'=>1,'types'=>0])->select();
        if(!empty($list)){
            foreach($list as $k => $v){
                $list[$k]['path'] =get_cover($v['pid']);
            }
        }

        return $list?$list:[];
    }

    // 获取评论
    public function getComments($vid){
        $list = M('Comments')->where(['vid'=>$vid,'status'=>1])->select();
        if(!empty($list)){
            foreach($list as $k =>$v){
                $list[$k]['datetime'] = time_format($v['created_time']);
            }
        }
        return $list?$list:[];
    }

    //获取日期列表
    public function getWeeks($items_id){
        for($i=1;$i<7;$i++){
            $week = date('N',strtotime('+'.$i.'days'));
            $date = date('y/m/d',strtotime('+'.$i.'days'));
            $d = date('Y-m-d',strtotime('+'.$i.'days'));
            $items[$i] = [
                'week' => $this->getWeek($week),
                'date' => $date,
                'd'    => $d
            ];
        }
        $today = [
            'week' => '今天',
            'date' => date('y/m/d'),
            'd'    => date('Y-m-d')
        ];
        array_unshift($items,$today);
        return count($items)?$items:[];
    }

    // 获取配置
    private function getConfig(){
        $config = api('Config/lists');
        return $config;
    }

    private function getWeek($num){
        switch($num){
            case 1:
                $week = '周一';
                break;
            case 2:
                $week = '周二';
                break;
            case 3:
                $week = '周三';
                break;
            case 4:
                $week = '周四';
                break;
            case 5:
                $week = '周五';
                break;
            case 6:
                $week = '周六';
                break;
            case 7:
                $week = '周日';
                break;
            default:
                $week = '无效';
        }
        return $week;
    }
}