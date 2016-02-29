<?php
namespace Addons\DoLogScore;
use Common\Controller\Addon;

/**
 * 积分日志插件插件
 * @author waco
 */

    class DoLogScoreAddon extends Addon{

        public $info = array(
            'name'=>'DoLogScore',
            'title'=>'积分日志插件',
            'description'=>'处理积分日志的记录',
            'status'=>1,
            'author'=>'waco',
            'version'=>'0.1'
        );
        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

	    //实现的doLogScore钩子方法
        public function doLogScore($param){
            M('LogScore')->update($param);
        }}