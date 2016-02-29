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
 * 统计管理控制器
 * @author waco <etoupcom@126.com>
 */

class StatisticsController extends AdminController {

  	/**
  	 * 统计管理首页
  	 * @author waco <etoupcom@126.com>
  	 */
    public function index(){
        list(
            $keyword,
            $items,
            $block,
            $typestxt,
            $statustxt,
            $ordersdate,
            $p,
            $soso
            ) = array(
            I('keyword', ''),
            I('items', 0,'int'),
            I('block', 0,'int'),
            I('typestxt', ''),
            I('statustxt', ''),
            I('ordersdate', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $keyword and $map['num'] = ['like', '%'.(string) $keyword.'%'];
        $items && $map['items_id']           = intval($items);
        $block && $map['bid']       = intval($block);
        if($typestxt){
            switch($typestxt){
                case 'up':
                    $map['types']       = 0;
                    break;
                case 'down':
                    $map['types']       = 1;
                    break;
            }
        }
        if($statustxt){
            switch($statustxt){
                case 'nopay':
                    $map['status']       = 0;
                    break;
                case 'pay':
                    $map['status']       = 1;
                    break;
                case 'use':
                    $map['status']       = 2;
                    break;
                case 'suc':
                    $map['status']       = 8;
                    break;
            }
        }
        if($ordersdate){
            $dateArr = explode(' - ',$ordersdate);
            if(trim($dateArr[0]) == trim($dateArr[1]))
                $map['nodes'] = $dateArr[0];
            else
                $map['created_time'] = ['between', [strtotime($dateArr[0]),strtotime($dateArr[1])]];
        }
        $map['vid'] = VID;
        $map['status'] = 8;
        $map['types'] = 0;
        $this->list = $this->lists('OrdersView',$map,'',true,1);
        $this->sumPrices = M('Orders')->where($map)->sum('price');
        $this->typestxtArr = $this->typestxt();
        $this->statustxtArr = $this->statustxt();
        //p($this->list);
        $this->keyword   = $keyword;
        $this->items        = $items;
        $this->block        = $block;
        $this->typestxt      = $typestxt;
        $this->statustxt = $statustxt;
        $this->ordersdate   = $ordersdate;
        $this->p          = $p;
        $this->soso       = $soso?true:false;
        $this->active = 'Statistics';
        $this->meta_title = '统计管理';
        $this->display();
    }
    /**
     * 订单统计
     */
    public function orders(){
        //按状态
        $statustxt = $this->statustxt();
        foreach($statustxt as $value){
            $statusArr[] = [
                'value' => M('Orders')->where(['vid'=>VID,'status'=>$value['value']])->count('id'),
                'label' => $value['title'],
                'color' => '#'.$this->randColor()
            ];
        }
        if(!$statusJson = S('statusJson-'.VID)){
            $statusJson = json_encode($statusArr);
            S('statusJson-'.VID,$statusJson,30*60);
        }
        $this->statusJson = $statusJson;
        //按场地
        $blocks = D('VenueBlockView')->where(['vid'=>VID])->select();
        if(!empty($blocks)){
            foreach ($blocks as $value) {
                $blocksArr[] = [
                    'value' => M('Orders')->where(['vid'=>VID,'bid'=>$value['id']])->count('id'),
                    'label' => $value['items_name'] .'-'. $value['name'],
                    'color' => '#'.$this->randColor()
                ];
            }
        }
        if(!$blocksJson = S('blocksJson-'.VID)){
            $blocksJson = json_encode($blocksArr);
            S('blocksJson-'.VID,$blocksJson,30*60);
        }
        $this->blocksJson = $blocksJson;
        //按项目
        $items = D('VenueItemsView')->where(['vid'=>VID])->select();
        if(!empty($items)){
            foreach ($items as $key => $value) {
                $itemsArr[] = [
                    'value' => M('Orders')->where(['vid'=>VID,'items_id'=>$value['items_id']])->count('id'),
                    'label' => $value['name'],
                    'color' => '#'.$this->randColor()
                ];
            }
        }
        if(!$itemsJson = S('itemsJson-'.VID)){
            $itemsJson = json_encode($itemsArr);
            S('itemsJson-'.VID,$itemsJson,30*60);
        }
        $this->itemsJson = $itemsJson;
        //按周
        for ($i=0; $i < 7; $i++) {
            $week = date('D',strtotime('+ '.$i.'day'));
            $getWeeks = $this->getWeeks();
            $labels[$i] = $getWeeks[$week].'【'.date('Y-m-d',strtotime('+ '.$i.'day')).'】';
            $nodes[$i] = date('Y-m-d',strtotime('+ '.$i.'day'));
        }


        $statustxt = $this->statustxtWeeks();
        foreach ($statustxt as $key => $value) {
          foreach ($nodes as $k => $v) {
              $data[$key][$k] = M('Orders')->where(['vid'=>VID,'nodes'=>$v,'status'=>$value['value']])->count();
          }
        }
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $randColor = $this->randColor();
                $rgb = $this->wpjam_hex2rgb($randColor);
                $rgba = 'rgba('.$rgb.', 1)';
                $weekscolor[] = ['color'=>$rgba,'title'=>$statustxt[$key]['title']];
                $weeksArr[] = [
                    'fillColor' => $rgba,
                    'strokeColor' => $rgba,
                    'pointColor' => $rgba,
                    'pointStrokeColor' => $rgba,
                    'pointHighlightFill' => '#fff',
                    'pointHighlightStroke' => $rgba,
                    'data' => $value,
                ];
            }
        }
        // S('weeksJson',null);
        // S('weekscolor',null);
        if(!$weeksJson = S('weeksJson-'.VID) or !$weekscolor = S('weekscolor-'.VID)){
            $weeksJson = json_encode($weeksArr);
            S('weeksJson-'.VID,$weeksJson,30*60);
            S('weekscolor-'.VID,$weekscolor,30*60);
        }
        $this->weeksJson = $weeksJson;
        $this->weekscolor = $weekscolor;
        $this->labelsJson = json_encode($labels);
        // p($this->weekscolor);
        // p($this->weeksJson);
        $this->active = 'Statistics';
        $this->meta_title = '订单统计';
        $this->display();
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
    /**
     * 会员统计
     */
    public function users(){
        $this->active = 'Statistics';
        $this->meta_title = '会员统计';
        $this->display();
    }
    /**
     * 资金统计
     */
    public function funds(){
        $this->active = 'Statistics';
        $this->meta_title = '资金统计';
        $this->display();
    }

    /**
     * 导出
     * @author waco <etoupcom@126.com>
     */
    public function export(){
        $request = I('request.');
        if(!empty($request)){
            Vendor('PHPExcel.PHPExcel');
            $objPHPExcel = new \PHPExcel();
            // 设置excel文档的属性
            $objPHPExcel->getProperties()->setCreator("Sam.c")
                ->setLastModifiedBy("Sam.c Test")
                ->setTitle("Microsoft Office Excel Document")
                ->setSubject("Test")
                ->setDescription("Test")
                ->setKeywords("Test")
                ->setCategory("Test result file");
            // 开始操作excel表
            // 操作第一个工作表
            $objPHPExcel->setActiveSheetIndex(0);
            // 设置工作薄名称
            $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'phpexcel导出'));
            // 设置默认字体和大小
            $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', '宋体'));
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);


            // 设置行宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

            // 设置行高度
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);

            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

            // 字体和样式
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
            $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

            // 设置水平居中
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //  合并
            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');



            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '结算统计，时间：'.date('Y-m-d'))
                ->setCellValue('A2', '时间')
                ->setCellValue('B2', '单号')
                ->setCellValue('C2', '项目')
                ->setCellValue('D2', '场地')
                ->setCellValue('E2', '价格')
                ->setCellValue('F2', '类型')
                ->setCellValue('G2', '下单时间')
                ->setCellValue('H2', '状态')
            ;
            // 内容
            $list = $this->getList();
            for ($i = 0, $len = count($list); $i < $len; $i++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($i + 3), '【'.$list[$i]['nodes'].' '.$list[$i]['start'].'-'.$list[$i]['end'].'】');
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $list[$i]['num']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $list[$i]['name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 3), $list[$i]['block_name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $list[$i]['price']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), '线上预定');
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), time_format($list[$i]['created_time']));
                $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), '已结算');
            }
            $m_exportType = 'excel';
            // 如果需要输出EXCEL格式
            if($m_exportType=="excel"){
                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                // 从浏览器直接输出$filename
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
                header("Content-Type:application/force-download");
                header("Content-Type: application/vnd.ms-excel;");
                header("Content-Type:application/octet-stream");
                header("Content-Type:application/download");
                header("Content-Disposition:attachment;filename=".date('Y-m-d h-i-s').".xls");
                header("Content-Transfer-Encoding:binary");
                $objWriter->save("php://output");
            }
            // 如果需要输出PDF格式
            if($m_exportType=="pdf"){
                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
                $objWriter->setSheetIndex(0);
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
                header("Content-Type:application/force-download");
                header("Content-Type: application/pdf");
                header("Content-Type:application/octet-stream");
                header("Content-Type:application/download");
                header("Content-Disposition:attachment;filename=".date('Y-m-d h-i-s').".pdf");
                header("Content-Transfer-Encoding:binary");
                $objWriter->save("php://output");
            }
        }else{
            $this->error('没有统计参数');
        }
    }

    public function exportOrders(){
        $cate = I('get.cate',0,'int');
        switch($cate){
            case 1:
                Vendor('PHPExcel.PHPExcel');
                $objPHPExcel = new \PHPExcel();
                // 设置excel文档的属性
                $objPHPExcel->getProperties()->setCreator("Sam.c")
                    ->setLastModifiedBy("Sam.c Test")
                    ->setTitle("Microsoft Office Excel Document")
                    ->setSubject("Test")
                    ->setDescription("Test")
                    ->setKeywords("Test")
                    ->setCategory("Test result file");
                // 开始操作excel表
                // 操作第一个工作表
                $objPHPExcel->setActiveSheetIndex(0);
                // 设置工作薄名称
                $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'phpexcel导出'));
                // 设置默认字体和大小
                $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', '宋体'));
                $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);


                // 设置行宽度
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);

                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

                $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');



                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '结算统计，时间：'.date('Y-m-d'))
                    ->setCellValue('A2', '时间')
                    ->setCellValue('B2', '单号')
                    ->setCellValue('C2', '项目')
                    ->setCellValue('D2', '场地')
                    ->setCellValue('E2', '价格')
                    ->setCellValue('F2', '类型')
                    ->setCellValue('G2', '下单时间')
                    ->setCellValue('H2', '状态')
                ;
                // 内容
                $list = $list = D('OrdersView')->where(['vid'=>VID])->order('status')->select();
                for ($i = 0, $len = count($list); $i < $len; $i++) {
                    $status = $list[$i]['status'];
                    $statusArr = $this->status();
                    $title = $statusArr[$status];
                    $objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($i + 3), '【'.$list[$i]['nodes'].' '.$list[$i]['start'].'-'.$list[$i]['end'].'】');
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $list[$i]['num']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $list[$i]['name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 3), $list[$i]['block_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $list[$i]['price']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), '线上预定');
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), time_format($list[$i]['created_time']));
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $title);
                }
                $m_exportType = 'excel';
                // 如果需要输出EXCEL格式
                if($m_exportType=="excel"){
                    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    // 从浏览器直接输出$filename
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
                    header("Content-Type:application/force-download");
                    header("Content-Type: application/vnd.ms-excel;");
                    header("Content-Type:application/octet-stream");
                    header("Content-Type:application/download");
                    header("Content-Disposition:attachment;filename=按订单状态".date('Y-m-d').".xls");
                    header("Content-Transfer-Encoding:binary");
                    $objWriter->save("php://output");
                }
                break;
        }
    }
    public function getItems(){
        $list = D('VenueItemsView')->where(['vid'=>VID])->select();
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
    public function getList(){
        list(
            $keyword,
            $items,
            $block,
            $typestxt,
            $statustxt,
            $ordersdate,
            $p,
            $soso
            ) = array(
            I('keyword', ''),
            I('items', 0,'int'),
            I('block', 0,'int'),
            I('typestxt', ''),
            I('statustxt', ''),
            I('ordersdate', ''),
            I('p', 1, 'int'),
            I('soso', 0, 'int')
        );
        $keyword and $map['num'] = ['like', '%'.(string) $keyword.'%'];
        $items && $map['items_id']           = intval($items);
        $block && $map['bid']       = intval($block);
        if($typestxt){
            switch($typestxt){
                case 'up':
                    $map['types']       = 0;
                    break;
                case 'down':
                    $map['types']       = 1;
                    break;
            }
        }
        if($statustxt){
            switch($statustxt){
                case 'nopay':
                    $map['status']       = 0;
                    break;
                case 'pay':
                    $map['status']       = 1;
                    break;
                case 'use':
                    $map['status']       = 2;
                    break;
                case 'suc':
                    $map['status']       = 8;
                    break;
            }
        }
        if($ordersdate){
            $dateArr = explode(' - ',$ordersdate);
            if(trim($dateArr[0]) == trim($dateArr[1]))
                $map['nodes'] = $dateArr[0];
            else
                $map['created_time'] = ['between', [strtotime($dateArr[0]),strtotime($dateArr[1])]];
        }
        $map['vid'] = VID;
        $map['status'] = 8;
        $map['types'] = 0;
        if($keyword or $items or $block or $typestxt or $statustxt or $ordersdate){
            $list = D('OrdersView')->where($map)->select();
        }else{
            $list = $this->lists('OrdersView', $map,'',true,1);
        }
        int_to_string($list);
        return $list;
    }

    private function typestxt(){
        $data = [
            'up'=>[
                'title'=>'线上预定',
                'value'=>0
            ],
            'down'=>[
                'title'=>'现场预定',
                'value'=>1
            ]
        ];

        return $data;
    }

    private function statustxt(){
        return [
            'del'=>[
                'title'=>'撤单',
                'value'=>-1
            ],
            'nopay'=>[
                'title'=>'未支付',
                'value'=>0
            ],
            'pay'=>[
                'title'=>'已支付',
                'value'=>1
            ],
            'use'=>[
                'title'=>'已使用',
                'value'=>2
            ],
            'suc'=>[
                'title'=>'已结算',
                'value'=>8
            ]
        ];
    }

    private function statustxtWeeks(){
        return [
            'nopay'=>[
                'title'=>'未支付',
                'value'=>0
            ],
            'pay'=>[
                'title'=>'已支付',
                'value'=>1
            ]
        ];
    }

    private function status(){
        return [
            -1=>'撤单',
            0=>'未支付',
            1=>'已支付',
            2=>'已使用',
            8=>'已结算'
        ];
    }

    private function randColor(){
        $colors = array();
        for($i = 0;$i<6;$i++){
            $colors[] = dechex(rand(0,15));
        }
        return implode('',$colors);
    }

    private function wpjam_hex2rgb($hex){
        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
          } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return implode(',',[$r, $g, $b]);
    }
}
