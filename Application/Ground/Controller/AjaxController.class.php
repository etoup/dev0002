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
 * 异步处理控制器
 * @author waco <etoupcom@126.com>
 */

class AjaxController extends AdminController {

	/**
	 * 开启关闭项目
	 * @author waco <etoupcom@126.com>
	 */
    public function ocVenueItems(){
        $id = I('post.id',0,'int');
        $status = I('post.status',0,'int');
        $id or $data = ['status'=>false,'msg'=>'请选择操作项'];
        $bid = M('VenueItems')->save(['id'=>$id,'status'=>$status,'update_time'=>NOW_TIME]);
        if($bid){
            $this->ajaxReturn(['status'=>true,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['status'=>false,'msg'=>'操作失败']);
        }
    }
}
