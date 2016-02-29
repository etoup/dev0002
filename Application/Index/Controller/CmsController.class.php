<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Index\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class CmsController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category(1);
		$this->assign('category', $category);
        $cid = I('get.cid',0,'int')?I('get.cid',0,'int'):2;
        $list = $this->lists('Document',['category_id'=>$cid,'status'=>1],'create_time desc');
        $this->assign('list', $list);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = array('title' => '全部');
        $this->crumbs = $this->_get_crumbs();
		$this->display();
	}

    /* 文档模型频道页 */
    public function notice(){
        /* 分类信息 */
        $category = $this->category(1);
        $this->assign('category', $category);
        $cid = I('get.cid',0,'int')?I('get.cid',0,'int'):45;
        $list = $this->lists('Document',['category_id'=>$cid,'status'=>1]);
        $this->assign('list', $list);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->seo = array('title' => '全部');
        $this->crumbs = $this->_get_crumbs(1);
        $this->display();
    }

	/* 文档模型详情页 */
	public function detail(){
        $id = I('get.id',0,'int');
        $id or $this->error('无效文章');
        $category = $this->category(1);
        $this->assign('category', $category);
		/* 获取详细信息 */
		$Document = D('DocumentView');
		$info = $Document->find($id);
        $this->info = $info;
        $this->seo = array('title' => $info['title']);
        $this->crumbs = $this->_get_crumbs();
		$this->display();
	}

	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->where(['pid'=>$id])->select();
        return $category;
	}

    private function _get_crumbs($type=0) {
        if($type){
            return [
                ['url' => U('Index/index'), 'title' => '首页'],
                ['url' => U('notice'), 'title' => '最新公告'],
            ];
        }else{
            return [
                ['url' => U('Index/index'), 'title' => '首页'],
                ['url' => U('index'), 'title' => '最新资讯'],
            ];
        }
    }

}
