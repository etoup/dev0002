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
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */

class FileController extends RestController {
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
        define('ROOT',C('HTTPHOST'));
    }
	/* 文件上传 */
	public function upload() {
        switch ($this->_method) {
            case 'post': // post请求处理代码
                $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
                /* 调用文件上传组件上传文件 */
                $File        = D('File');
                $file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
                $info        = $File->upload(
                    $_FILES,
                    C('DOWNLOAD_UPLOAD'),
                    C('DOWNLOAD_UPLOAD_DRIVER'),
                    C("UPLOAD_{$file_driver}_CONFIG")
                );

                /* 记录附件信息 */
                if ($info) {
                    $return['path'] = $info['file']['path'];
                } else {
                    $return['status'] = 0;
                    $return['info']   = $File->getError();
                }
                $this->response($return,'json');
                break;
        }
	}
    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPictures() {
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture    = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info       = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        );//TODO:上传到远程服务器

        /* 记录图片信息 */
        if ($info) {
            $return['status'] = 1;
            $return           = array_merge($info['file'], $return);
            $return['imgpath'] = ROOT.$info['file']['path'];
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
        /* 返回JSON数据 */
        $this->response($return,'json');
    }
}
