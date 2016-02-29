<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------
namespace Index\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {
    //系统首页
    public function index() {
        $this->items = M('Items')->where(['status'=>1])->select();
        //获取当天日期
        $weekarray=array("日","一","二","三","四","五","六");
        for($i=0;$i<7;$i++){
            $date = strtotime('+'.$i.' day');
            $data[$i] = [
                'date'=>date('Y-m-d',$date),
                'week'=>"星期".$weekarray[date("w",$date)]
            ];
        }
        $this->date_week = $data;
        $this->times = ['05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00'];
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $seo = array('title' => '首页');
        $this->seo = $seo;
        $this->display();
    }

    private function week(){

    }
}