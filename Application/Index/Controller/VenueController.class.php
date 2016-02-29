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
 * 场馆控制器
 *
 */

class VenueController extends HomeController {

	/**
	 * 场馆列表页
	 */
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
        $info = M('District')->where(['name'=>session('city')])->find();
        $this->districts = M('District')->where(['upid'=>$info['id'],'level'=>3])->select();
        $this->circles = M('Circle')->where(['city'=>session('city')])->select();
        $this->all_services = ['免费停车场','VIP休息室','柜子租赁','活动区'];

        $venues = $this->lists('Venue',['city'=>session('city'),'status'=>1]);
        if(!empty($venues)){
            foreach($venues as $k=>$v){
                $venues[$k]['services'] = explode(',',$v['services']);
            }
        }
        $this->venues = $venues;
        $this->seo = array('title' => '场馆列表页');
        $this->display();
	}

    public function details(){
        $this->seo = array('title' => '场馆详情页');
        $this->display();
    }

}