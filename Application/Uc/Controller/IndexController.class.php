<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Uc\Controller;

/**
 * 会员中心首页控制器
 */
class IndexController extends UcenterController {
    /**
     * 会员中心首页
     */
    public function index() {

        //是否关闭引导页
        $this->closepage = cookie('closepagewalkthrough');
        //最新股票配资
        $this->peiziList = M('MainNeeds')->where(['uid'=>UID,'types'=>0])->order('created_time DESC')->limit(2)->select();
        //最新期货配资
        $this->qihuoList = M('MainNeeds')->where(['uid'=>UID,'types'=>1])->order('created_time DESC')->limit(2)->select();
        //最新投资
        $this->touziList = D('MainTenderView')->where(['uid'=>UID])->order('created_time DESC')->limit(2)->select();
        //预警提醒
        $this->earlyMsg = M('MessageNotices')->where(['uid'=>UID,'early'=>1])->select();
        //默认图像
        $this->iconList = $this->getIconList();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->balance = number_format($this->uc['balance'], 2);
        $this->seo     = array('title' => '会员中心');
        $mobile = I('get.mobile','');
        switch($mobile){
            case 'weixin':
                $this->display('indexweixin');
                break;
            default:
                $this->display();
        }
    }

    /**
     * 修改图像
     */
    public function editUserImg(){
        $img = I('post.img','');
        if(empty($img)){
            $icon = I('post.icon',0,'int');
            $icon or exit($this->ajaxReturn(['state'   => 'no','message'=>'请选择或者上传图片!']));
            $re = M('Member')->where(['uid'=>UID])->save(['icon'=>$icon,'img'=>'']);
            if($re){
                $pathInfo = get_cover($icon);
                $map = array(
                    'state'   => 'success',
                    'message' => '设置成功!',
                    'src'     => $pathInfo['path'].'?'.$pathInfo['md5']
                );
                exit($this->ajaxReturn($map));
            }else{
                exit($this->ajaxReturn(['state'   => 'no','message'=>'更新失败!']));
            }
        }else{
            $re = M('Member')->where(['uid'=>UID])->save(['img'=>$img,'icon'=>0]);
            if($re){
                $map = array(
                    'state'   => 'success',
                    'message' => '设置成功!',
                    'src'     => $img
                );
                exit($this->ajaxReturn($map));
            }else{
                exit($this->ajaxReturn(['state'   => 'no','message'=>'更新失败!']));
            }
        }

    }

    public function getIconList(){
        return $data = [51,52,53,54];
    }

    public function demo(){
        $this->display();
    }

    public function upload(){
        $this->display();
    }
}