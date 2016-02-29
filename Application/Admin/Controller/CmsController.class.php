<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 内容管理控制器
 * @author waco <etoupcom@126.com>
 */
class CmsController extends AdminController {

	/**
	 * 文章管理
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		if (IS_POST) {
			# code...
		} else {
			$this->display();
		}
	}

	/**
	 * 获取分类
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function getCategorys() {
		$list = D('Category')->getList();
		if (is_array($list) && !empty($list)) {
			foreach ($list as $key => $value) {
				$list[$key]['catname'] = $value['title'];
				$list[$key]['url']     = U('articleList', array('id' => $value['id']));
				$list[$key]['target']  = 'right';
			}
		}
		$this->json = json_encode($list);
		$this->display();
	}

	/**
	 * 文章分类
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function category() {
		if (IS_POST) {
			$this->error('非法操作');
		} else {
			$this->model = M('Model')->where(array('extend' => 1))->field('id,title')->select();
			$map         = D('Category')->getMap();
			$catedb      = $map[0];
			foreach ($catedb as $key => $value) {
				$catedb[$key]['modelArr'] = empty($value['model'])?array():explode(',', $value['model']);
				$list[$value['id']]       = D('Category')->getNodeByLevel($value['id'], $map);
			}
			$this->catedb = $catedb;
			$this->list   = $list;
			$this->display();
		}
	}

	/**
	 * 添加新分类、修改分类排序、修改分类等操作
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function dorunCategory() {
		if (IS_POST) {
			//修改文章分类
			list($sort, $description, $name, $model, $check) = array(
				I('post.sort'),
				I('post.description'),
				I('post.name'),
				I('post.model'),
				I('post.check')
			);
			if (is_array($sort) && !empty($sort)) {
				foreach ($sort as $key => $value) {
					$map = array(
						'id'          => $key,
						'sort'        => $value,
						'description' => $description[$key],
						'name'        => $name[$key],
						'model'       => $model[$key],
						'check'       => $check[$key]?$check[$key]:0,
					);
					M('Category')->save($map);
				}
			}
			//在真实节点下，添加子节点
			list($new_sort, $new_title, $new_description, $new_name, $new_model, $new_check) = array(
				I('post.new_sort'),
				I('post.new_title'),
				I('post.new_description'),
				I('post.new_name'),
				I('post.new_model'),
				I('post.new_check'),
				I('post.tempid')
			);
			is_array($new_sort) || $new_sort = array();
			foreach ($new_sort as $parentid => $value) {
				foreach ($value as $k          => $v) {
					if ($new_title[$parentid][$k] && $new_name[$parentid][$k]) {
						$newMap = array(
							'sort'        => $v,
							'title'       => $new_title[$parentid][$k],
							'description' => $new_description[$parentid][$k],
							'name'        => $new_name[$parentid][$k],
							'model'       => $new_model[$parentid][$k]?explode(',', $new_model[$parentid][$k]):array(),
							'check'       => $new_check[$parentid][$k]?$new_check[$parentid][$k]:0,
							'mold'        => 'category',
						);
						$newrid = D('Category')->update($newMap);
					}
				}
			}
			//更新分类缓存
			S('sys_category_list', null);
			ajaxMsg('操作成功');
		} else {
			$this->error('非法操作');
		}

	}

	/**
	 * 添加文章分类
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function addCategory() {
		if (IS_POST) {
			$Category = D('Category');
			if (false !== $Category->update()) {
				ajaxMsg('操作成功');
			} else {
				$error = $Category->getError();
				ajaxMsg($error, false);
			}
		} else {
			$pid = I('get.pid', 0, 'int');
			$pid || $this->error('请选择操作项');
			$info = M('Category')->field('id,title,pid,mold,status')->find($pid);
			if (!($info && 1 == $info['status'])) {
				$this->error('指定的上级分类不存在或被禁用！');
			}
			switch ($info['mold']) {
				case 'category':
					$this->mold = 'forum';
					break;
				case 'forum':
					$this->mold = 'sub';
					break;
				default:
					$this->mold = 'forum';
					break;
			}
			$this->info = $info;
			$this->display();
		}
	}

	/**
	 * 编辑文章分类
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function editCategory() {
		if (IS_POST) {
			$Category = D('Category');
			$pid      = I('post.pid', 0, 'int');
			$data     = $Category->create();
            if($pid){
                $pinfo    = M('Category')->field('id,title,pid,mold,status')->find($pid);
                switch ($pinfo['mold']) {
                    case 'category':
                        $data['mold'] = 'forum';
                        break;
                    case 'forum':
                        $data['mold'] = 'sub';
                        break;
                    default:
                        $data['mold'] = 'forum';
                        break;
                }
            }
			if (false !== $Category->update($data)) {
				ajaxMsg('操作成功');
			} else {
				$error = $Category->getError();
				ajaxMsg($Category->getError(), false);
			}
		} else {
			$id = I('get.id', 0, 'int');
			$id || $this->error('请选择操作项');
			$info          = M('Category')->find($id);
			$info['model'] = explode(',', $info['model']);
			$info['type']  = explode(',', $info['type']);
			$this->info    = $info;
			$where         = array('id' => array('neq', $id), 'mold' => array('neq', 'sub'));
			$map           = D('Category')->getMap($where);
			$catedb        = $map[0];
			foreach ($catedb as $key => $value) {
				$catedb[$key]['modelArr'] = empty($value['model'])?array():explode(',', $value['model']);
				$list[$value['id']]       = D('Category')->getNodeByLevel($value['id'], $map);
			}
			$this->catedb = $catedb;
			$this->list   = $list;
			$this->display();
		}

	}

	/**
	 * 删除分类
	 * @author waco <etoupcom@126.com>
	 */
	public function delCategory() {
		$id = I('get.id', 0, 'int');
		$id || ajaxMsg('请选择操作项', false);
		$info = M('Category')->where(array('pid' => $id))->find();
		empty($info) || ajaxMsg('请先删除子节点后再操作', false);
		if (M('Category')->delete($id)) {
			// S('DB_CONFIG_DATA',null);
			//记录行为
			action_log('update_category', 'Category', $id, UID);
			ajaxMsg('删除成功');
		} else {
			ajaxMsg('删除失败', false);
		}
	}

	/**
	 * 编辑分类名称
	 * @author waco <etoupcom@126.com>
	 */
	public function editTitle() {
		if (IS_AJAX) {
			$id = I('post.id', 0, 'int');
			$id || ajaxMsg('请选择操作项', false);
			$title = I('post.title', '');
			$title || ajaxMsg('请填写节点名称', false);
			$map = array('id' => $id, 'title' => $title);
			M('Category')->save($map);
			ajaxMsg('操作成功');
		} else {
			ajaxMsg('非法操作', false);
		}
	}

	/**
	 * 搜索分类
	 * @author waco <etoupcom@126.com>
	 */
	public function searchCategory() {
		if (IS_AJAX) {
			$keyword = I('post.keyword', '');
			if ($keyword) {
				$where = array(
					'title' => array('like', '%'.$keyword.'%')
				);
				$list = M('Category')->where($where)->select();
				if (!$list || !is_array($list)) {
					ajaxMsg('没有符合条件的内容', false);
				} else {
					$this->data = $list;
					$map        = array(
						'refresh' => false,
						'state'   => 'success',
						'data'    => $list,
					);
					echo json_encode($map);
				}
			}
		} else {
			ajaxMsg('非法操作', false);
		}
	}

	/**
	 * 获取文章列表
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function articleList() {
		$cate_id = I('get.id', 0, 'int');
		$cate_id || $this->error('请选择操作项');
		$info             = M('Category')->find($cate_id);
		$info['modelArr'] = empty($info['model'])?array():explode(',', $info['model']);
		$this->info       = $info;
		//$this->model = M('Model')->where(array('extend'=>1))->field('id,title')->select();
		//获取模型信息
		$model = M('Model')->getByName('document');
		//解析列表规则
		$fields = array();
		$grids  = preg_split('/[;\r\n]+/s', $model['list_grid']);


		foreach ($grids as &$value) {
			// 字段:标题:链接
			$val = explode(':', $value);
			// 支持多个字段显示
			$field = explode(',', $val[0]);
			$value = array('field' => $field, 'title' => $val[1]);
			if (isset($val[2])) {
				// 链接信息
				$value['href'] = $val[2];
				// 搜索链接信息中的字段信息
				preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use (&$fields) {$fields[] = $match[1];}, $value['href']);

			}
			if (strpos($val[1], '|')) {
				// 显示格式定义
				list($value['title'], $value['format']) = explode('|', $val[1]);
			}
			foreach ($field as $val) {
				$array    = explode('|', $val);
				$fields[] = $array[0];
			}
		}
		//获取对应分类下的模型
		if (!empty($cate_id)) {//没有权限则不查询数据
			//获取分类绑定的模型
			$models      = get_category($cate_id, 'model');
			$allow_reply = get_category($cate_id, 'reply');//分类文档允许回复
			$pid         = I('pid');
			if ($pid == 0) {
				//开发者可根据分类绑定的模型,按需定制分类文档列表
				$template = $this->indexOfArticle($cate_id);//转入默认文档列表方法
				$this->assign('model', explode(',', $models));
			} else {
				//开发者可根据父文档的模型类型,按需定制子文档列表
				$doc_model = M('Document')->where(array('id' => $pid))->find();

				switch ($doc_model['model_id']) {
					default:
						if ($doc_model['type'] == 2 && $allow_reply) {
							$this->assign('model', array(2));
							$template = $this->indexOfReply($cate_id);//转入子文档列表方法
						} else {
							$this->assign('model', explode(',', $models));
							$template = $this->indexOfArticle($cate_id);//转入默认文档列表方法
						}
				}
			}
			Cookie('__forward__', $_SERVER['REQUEST_URI']);
			$this->cate_id = $cate_id;
			$this->assign('list_grids', $grids);
			$this->assign('model_list', $model);
			$this->display();
		} else {
			$this->error('非法的文档分类');
		}
	}

	/**
	 * 获取待审文章列表
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function examine() {
		$map['status'] = 2;
		if (!IS_ROOT) {
			$cate_auth = AuthGroupModel::getAuthCategories(UID);
			if ($cate_auth) {
				$map['category_id'] = array('IN', $cate_auth);
			} else {
				$map['category_id'] = -1;
			}
		}
		$list = $this->lists(M('Document'), $map, 'update_time desc');
		//处理列表数据
		if (is_array($list)) {
			foreach ($list as $k => &$v) {
				$v['username'] = get_nickname($v['uid']);
			}
		}

		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 通过审核
	 * @author waco <etoupcom@126.com>
	 */
	public function pass() {
		$id = I('get.id', 0, 'int');
		$id || ajaxMsg('请选择操作项', false);
		$cate_id = M('Document')->where(array('id' => $id))->getField('category_id');
		$rid     = M('Document')->save(array('id'  => $id, 'status'  => 1));
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 还原
	 * @author waco <etoupcom@126.com>
	 */
	public function permit() {
		$id = I('request.id');
		$id || ajaxMsg('请选择操作项', false);
		/*拼接参数并修改状态*/
		$Model = 'Document';
		$map   = array();
		if (is_array($id)) {
			$map['id'] = array('in', $id);
		} elseif (is_numeric($id)) {
			$map['id'] = $id;
		}
		$rid = M('Document')->where($map)->save(array('status' => 1));
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 清除
	 * @author waco <etoupcom@126.com>
	 */
	public function clear() {
		$id = I('id');
		$id || ajaxMsg('请选择操作项', false);
		/*拼接参数并修改状态*/
		$Model = 'Document';
		$map   = array();
		if (is_array($id)) {
			$map['id'] = array('in', $id);
		} elseif (is_numeric($id)) {
			$map['id'] = $id;
		}
		$rid = M('Document')->where($map)->delete();
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 草稿箱
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function draftBox() {
		$Document = D('Document');
		$map      = array('status' => 3, 'uid' => UID);
		$list     = $this->lists($Document, $map);
		//获取状态文字
		//int_to_string($list);

		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 回收站
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function recycle() {
		$map['status'] = -1;
		if (!IS_ROOT) {
			$cate_auth = AuthGroupModel::getAuthCategories(UID);
			if ($cate_auth) {
				$map['category_id'] = array('IN', $cate_auth);
			} else {
				$map['category_id'] = -1;
			}
		}
		$list = $this->lists(M('Document'), $map, 'update_time desc');

		//处理列表数据
		if (is_array($list)) {
			foreach ($list as $k => &$v) {
				$v['username'] = get_nickname($v['uid']);
			}
		}

		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 发布文章
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function add() {
		if (IS_POST) {
			# code...
		} else {
			$cate_id = I('get.cate_id', 0, 'int');
			$cate_id || $this->error('请选择操作项');
			$model_id = I('get.model_id', 0, 'int');
			$model_id || $this->error('请绑定模型');
			//检查该分类是否允许发布
			$allow_publish = D('Document')->checkCategory($cate_id);
			$allow_publish || $this->error('该分类不允许发布内容');
			//获取要编辑的扩展模型模板
			$model               = get_document_model($model_id);
			$info['pid']         = $_GET['pid']?$_GET['pid']:0;
			$info['model_id']    = $model_id;
			$info['category_id'] = $cate_id;

			if ($info['pid']) {
				// 获取上级文档
				$article = M('Document')->field('id,title,type')->find($info['pid']);
				$this->assign('article', $article);
			}

			//获取表单字段排序
			$fields = get_model_attribute($model['id']);
			$this->assign('info', $info);
			$this->assign('fields', $fields);
			$this->assign('type_list', get_type_bycate($cate_id));
			$this->assign('model', $model);

			$this->title = M('Category')->where(array('id' => $cate_id))->getField('title');
			$this->list  = M('Category')->where(array('id' => array('neq', $cate_id)))->field('id,title')->select();
			$this->display();
		}
	}

	/**
	 * 编辑文章
	 *
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			# code...
		} else {
			$id = I('get.id', '');
			if (empty($id)) {
				$this->error('参数不能为空！');
			}

			/*获取一条记录的详细数据*/
			$Document = D('Document');
			$data     = $Document->detail($id);
			if (!$data) {
				$this->error($Document->getError());
			}

			if ($data['pid']) {
				// 获取上级文档
				$article = M('Document')->field('id,title,type')->find($data['pid']);
				$this->assign('article', $article);
			}

			$this->assign('data', $data);
			$this->assign('model_id', $data['model_id']);

			/* 获取要编辑的扩展模型模板 */
			$model = get_document_model($data['model_id']);
			$this->assign('model', $model);

			//获取表单字段排序
			$fields = get_model_attribute($model['id']);
			$this->assign('fields', $fields);

			//获取当前分类的文档类型
			$this->assign('type_list', get_type_bycate($data['category_id']));

			$this->title = M('Category')->where(array('id' => $data['category_id']))->getField('title');
			$this->list  = M('Category')->where(array('id' => array('neq', $data['category_id'])))->field('id,title')->select();
			$this->display();
		}
	}

	/**
	 * 更新一条数据
	 * @author waco <etoupcom@126.com>
	 */
	public function update() {
		$res = D('Document')->update();
		if (!$res) {
			ajaxMsg(D('Document')->getError(), false);
		} else {
			ajaxMsg($res['id']?'更新成功':'新增成功');
		}
	}

	/**
	 * 删除数据
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$id = I('request.ids');
		$id || ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$rid = M('Document')->where(array('id' => array('in', $id)))->save(array('status' => -1));
		} else {
			$rid = M('Document')->save(array('id' => $id, 'status' => -1));
		}
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 删除数据
	 * @author waco <etoupcom@126.com>
	 */
	public function delete() {
		$id = I('request.ids');
		$id || ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$rid = M('Document')->where(array('id' => array('in', $id)))->save(array('status' => -1));
		} else {
			$rid = M('Document')->save(array('id' => $id, 'status' => -1));
		}
		$backUrl = array('referer' => Cookie('__forward__'));
		$rid?ajaxMsg($backUrl):ajaxMsg('操作失败', false);
	}

	/**
	 * 设为热门
	 * @author waco <etoupcom@126.com>
	 */
	public function sethot() {
		$id = I('request.ids');
		$id || ajaxMsg('请选择操作项', false);
		/*拼接参数并修改状态*/
		$map = array();
		if (is_array($id)) {
			$map['id'] = array('in', $id);
		} elseif (is_numeric($id)) {
			$map['id'] = $id;
		}
		$rid = M('DocumentArticle')->where($map)->save(array('ifhot' => 1));
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 删除热门
	 * @author waco <etoupcom@126.com>
	 */
	public function delhot() {
		$id = I('request.ids');
		$id || ajaxMsg('请选择操作项', false);
		/*拼接参数并修改状态*/
		$map = array();
		if (is_array($id)) {
			$map['id'] = array('in', $id);
		} elseif (is_numeric($id)) {
			$map['id'] = $id;
		}
		$rid = M('DocumentArticle')->where($map)->save(array('ifhot' => 0));
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 默认文档列表方法
	 * @param $cate_id 分类id
	 * @author waco <etoupcom@126.com>
	 */
	protected function indexOfArticle($cate_id) {
		/* 查询条件初始化 */
		$map                                                              = array();
		list($p, $soso, $title, $status, $timeStart, $timeEnd, $nickname) = array(
			I('p', 1, 'int'),
			I('soso', 0, 'int'),
			I('title', ''),
			I('status', 1, 'int'),
			I('time-start', ''),
			I('time-end', ''),
			I('nickname', '')
		);
		$title && $map['title'] = array('like', '%'.(string) $title.'%');

		$timeStart && $map['update_time'][] = array('egt', strtotime($timeStart));
		$timeEnd && $map['update_time'][]   = array('elt', 24*60*60+strtotime($timeEnd));
		$nickname && $map['uid']            = M('Member')->where(array('nickname' => I('nickname')))->getField('uid');
		$$status >= 0 && $map['status']     = $status;
		$map['pid']                         = 0;
		// 构建列表数据
		$Document           = M('Document');
		$map['category_id'] = $cate_id;
		$map['pid']         = I('pid', 0);
		if ($map['pid']) {// 子文档列表忽略分类
			unset($map['category_id']);
		}

		$list = $this->lists($Document, $map, 'level DESC,create_time DESC');
		int_to_string($list);
		if ($map['pid']) {
			// 获取上级文档
			$article = $Document->field('id,title,type')->find($map['pid']);
			$this->assign('article', $article);
		}
		//检查该分类是否允许发布内容
		$allow_publish = get_category($cate_id, 'allow_publish');

		$this->assign('status', $status);
		$this->assign('list', $list);
		$this->assign('allow', $allow_publish);
		$this->assign('pid', $map['pid']);
		$this->status = $status;
		$this->p      = $p;
		$this->soso   = $soso?true:false;
		return 'index';
	}

    /**
     * 批量处理数据 临时使用
     * @author waco <etoupcom@126.com>
     */
//    public function up(){
//        $list = M('Article')->where(['status'=>1])->select();
//        if(!empty($list)){
//            foreach($list as $k => $v){
//                //处理基础模型表数据
//                $id = M('Document')->add([
//                    'uid'=>1,
//                    'title'=>$v['name'],
//                    'category_id'=>2,
//                    'description'=>$v['summary'],
//                    'model_id'=>2,
//                    'type'=>2,
//                    'create_time'=>$v['addtime'],
//                    'update_time'=>$v['addtime'],
//                    'status' =>1,
//                    'isold'=>1,
//                    'url'=>$v['litpic']
//                ]);
//                //处理文章表数据
//                M('DocumentArticle')->add([
//                    'id'=>$id,
//                    'content'=>$v['content']
//                ]);
//            }
//        }
//        ajaxMsg('批量操作成功！');
//    }
}
