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
 * 后台版块控制器
 * @author waco <etoupcom@126.com>
 */

class ForumController extends AdminController {

	/**
	 * 版块管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		$data       = D('Forum')->getForumByStatus(2);
		$this->list = $this->_arrayValueSort($data);
		$this->display();
	}

	/**
	 * 版块批量修改处理器
	 * @author waco <etoupcom@126.com>
	 */
	public function dorun() {
		if (IS_POST) {
			$datas = $newdatas = array();
			list(
				$posts,
				$newposts
			) = array(
				I('post.data'),
				I('post.newdata')
			);
			if ($posts) {
				foreach ($posts as $post) {
					if (!$post['title'] || !$post['create_time']) {
						continue;
					}

					$map = array(
						'id'          => $post['id'],
						'title'       => $post['title'],
						'sort'        => $post['sort'],
						'post_count'  => intval($post['post_count']),
						'status'      => $post['status']?$post['status']:0,
						'create_time' => strtotime($post['create_time']),
						'update_time' => NOW_TIME

					);
					$resource = M('Forum')->save($map);
					$dms[]    = $dm;
				}
			}
			if ($newposts) {
				foreach ($newposts as $k => $newpost) {
					if (!$newpost['title'] || !$newpost['create_time']) {
						continue;
					}

					list($isroot, $id) = explode('_', $k);
					if ($isroot == 'root') {
						$parentid = 0;
					} elseif ($isroot == 'child') {
						if (is_numeric($newpost['parentid'])) {
							$parentid = intval($newpost['parentid']);
						} else {
							$parentid = (int) $resource;
						}
					}

					$map = array(
						'title'       => $newpost['title'],
						'parentid'    => $parentid,
						'sort'        => $newpost['sort'],
						'create_time' => NOW_TIME,
						'update_time' => NOW_TIME
					);

					$resource      = M('Forum')->add($map);
					$edt['rootid'] = $parentid?$parentid:$resource;
					M('Forum')->where(array('id' => $resource))->save($edt);
				}
			}

			ajaxMsg('操作成功');
		} else {
			$this->error('非法操作');
		}

	}

	/**
	 * 版块编辑
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			$Forum = D('Forum');
			if ($Forum->create()) {
				$back = $Forum->save();
				$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
			} else {
				ajaxMsg($Forum->getError(), false);
			}
		} else {
			$id = I('get.id', 0, 'int');
			$id || $this->error('请选择操作项');
			$info = M('Forum')->find($id);
			if ($info['parentid'] > 0) {
				$this->parentids = M('Forum')->where(array('parentid' => array('eq', 0)))->field('id,title')->select();
			}
			$this->info = $info;
			$this->list = D('MemberGroups')->getAllGroups();
			$this->display();
		}

	}

	/**
	 * 版块删除
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$id = I('get.id', 0, 'int');
		$id || $this->error('请选择操作项');
		$data = array('id' => $id, 'status' => -1);
		$rid  = M('Forum')->save($data);
		$rid?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
	}

	/**
	 * 版块回收站
	 * @author waco <etoupcom@126.com>
	 */
	public function forumtrash() {
		$this->list = D('Forum')->getForumByStatus(-1);
		$this->display();
	}

	/**
	 * 版块还原
	 * @author waco <etoupcom@126.com>
	 */
	public function restore() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$back = M('Forum')->where(array('id' => array('in', $id)))->setField('status', 1);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('Forum')->where(array('id' => $id))->setField('status', 1);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 版块清理
	 * @author waco <etoupcom@126.com>
	 * ！此处预留删除版块帖子功能，建议使用事务处理
	 */
	public function clear() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$back = M('Forum')->where(array('id' => array('in', $id)))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('Forum')->where(array('id' => $id))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 帖子管理
	 * @author waco <etoupcom@126.com>
	 */
	public function post() {
		$dao            = D('ForumPostView');
		$data           = D('Forum')->getForumByStatus(2);
		$this->flist    = $this->_arrayValueSort($data);
		$forum_id       = 0;
		$this->forum_id = $forum_id;
		$this->display();
	}

	/**
	 * 帖子搜索
	 * @author waco <etoupcom@126.com>
	 */
	public function searchPost() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$map         = array();
		//显示正常状态的帖子
		$map['status'] = 1;
		list(
			$p,
			$soso,
			$keyword,
			$nickname,
			$time_start,
			$time_end,
			$forum_id,
			$uid,
			$create_ip,
			$hits_start,
			$hits_end,
			$replies_start,
			$replies_end
		) = array(
			I('request.p', 1, 'int'),
			I('request.soso', 0, 'int'),
			I('request.keyword', ''),
			I('request.nickname', ''),
			I('request.time_start', ''),
			I('request.time_end', ''),
			I('request.forum_id', 0, 'int'),
			I('request.uid', 0, 'int'),
			I('request.create_ip', ''),
			I('request.hits_start', 0, 'int'),
			I('request.hits_end', 0, 'int'),
			I('request.replies_start', 0, 'int'),
			I('request.replies_end', 0, 'int')
		);
		$keyword && $map['title']     = array('like', '%'.$keyword.'%');
		$nickname && $map['nickname'] = array('like', '%'.$nickname.'%');
		$forum_id && $map['forum_id'] = intval($forum_id);
		if ($time_start && $time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($time_start && !$time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		$this->list       = $this->lists('ForumPostView', $map, 'create_time desc', true, 1);
		$this->p          = $p;
		$this->soso       = $soso?true:false;
		$this->keyword    = $keyword;
		$this->nickname   = $nickname;
		$this->forum_id   = $forum_id;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->display('post');
	}

	/**
	 * 帖子高级搜索
	 * @author waco <etoupcom@126.com>
	 */
	public function advanced() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$this->display();
	}

	/**
	 * 帖子审核
	 * @author waco <etoupcom@126.com>
	 */
	public function checkPost() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$map         = array();
		//显示待审状态的帖子
		$map['status'] = 0;
		list(
			$p,
			$soso,
			$nickname,
			$time_start,
			$time_end,
			$forum_id
		) = array(
			I('request.p', 1, 'int'),
			I('request.soso', 0, 'int'),
			I('request.nickname', ''),
			I('request.time_start', ''),
			I('request.time_end', ''),
			I('request.forum_id', 0, 'int')
		);
		$nickname && $map['nickname'] = array('like', '%'.$nickname.'%');
		$forum_id && $map['forum_id'] = intval($forum_id);
		if ($time_start && $time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($time_start && !$time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		$this->list       = $this->lists('ForumPostView', $map, 'create_time desc', true, 1);
		$this->p          = $p;
		$this->soso       = $soso?true:false;
		$this->nickname   = $nickname;
		$this->forum_id   = $forum_id;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->display();
	}

	/**
	 * 帖子通过
	 * @author waco <etoupcom@126.com>
	 */
	public function passPost() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$data = array('id' => array('in', $id), 'status' => 1);
			M('ForumPost')->save($data);
		} else {
			$data = array('id' => $id, 'status' => 1);
			M('ForumPost')->save($data);
		}
		ajaxMsg('操作成功');
	}

	/**
	 * 帖子删除
	 * @author waco <etoupcom@126.com>
	 */
	public function delPost() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$data = array(
				'id'           => array('in', $id),
				'operator'     => USERNAME,
				'operate_time' => NOW_TIME,
				'reason'       => '管理员删除',
				'status'       => -1
			);
			M('ForumPost')->save($data);
		} else {
			$data = array('id' => $id, 'operator' => USERNAME, 'operate_time' => NOW_TIME, 'reason' => '管理员删除', 'status' => -1);
			M('ForumPost')->save($data);
		}
		ajaxMsg('操作成功');
	}

	/**
	 * 帖子回收站
	 * @author waco <etoupcom@126.com>
	 */
	public function recycle() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$map         = array();
		//显示正常状态的帖子
		$map['status'] = -1;
		list(
			$p,
			$soso,
			$keyword,
			$nickname,
			$time_start,
			$time_end,
			$forum_id,
			$operator,
			$operate_time_start,
			$operate_time_end,
		) = array(
			I('request.p', 1, 'int'),
			I('request.soso', 0, 'int'),
			I('request.keyword', ''),
			I('request.nickname', ''),
			I('request.time_start', ''),
			I('request.time_end', ''),
			I('request.forum_id', 0, 'int'),
			I('request.operator', ''),
			I('request.operate_time_start', ''),
			I('request.operate_time_end', ''),
		);
		$keyword && $map['title']     = array('like', '%'.$keyword.'%');
		$nickname && $map['nickname'] = array('like', '%'.$nickname.'%');
		$operator && $map['operator'] = array('like', '%'.$operator.'%');
		$forum_id && $map['forum_id'] = intval($forum_id);
		if ($time_start && $time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($time_start && !$time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($operate_time_start && $operate_time_end) {
			$map['operate_time'] = array('gt', strtotime($operate_time_start));
			$map['operate_time'] = array('lt', strtotime($operate_time_end));
		}
		if ($operate_time_start && !$operate_time_end) {
			$map['operate_time'] = array('gt', strtotime($operate_time_start));
		}
		if (!$operate_time_start && $operate_time_end) {
			$map['operate_time'] = array('lt', strtotime($operate_time_end));
		}
		$this->list               = $this->lists('ForumPostView', $map, 'create_time desc', true, 1);
		$this->p                  = $p;
		$this->soso               = $soso?true:false;
		$this->keyword            = $keyword;
		$this->nickname           = $nickname;
		$this->operator           = $operator;
		$this->forum_id           = $forum_id;
		$this->time_start         = $time_start;
		$this->time_end           = $time_end;
		$this->operate_time_start = $operate_time_start;
		$this->operate_time_end   = $operate_time_end;
		$this->display();
	}

	/**
	 * 帖子还原
	 * @author waco <etoupcom@126.com>
	 */
	public function restorePost() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$back = M('ForumPost')->where(array('id' => array('in', $id)))->setField('status', 1);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('ForumPost')->where(array('id' => $id))->setField('status', 1);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 帖子清理
	 * @author waco <etoupcom@126.com>
	 */
	public function clearPost() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$back = M('ForumPost')->where(array('id' => array('in', $id)))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('ForumPost')->where(array('id' => $id))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 回复管理
	 * @author waco <etoupcom@126.com>
	 */
	public function reply() {
		$dao            = D('ForumPostView');
		$data           = D('Forum')->getForumByStatus(2);
		$this->flist    = $this->_arrayValueSort($data);
		$forum_id       = 0;
		$this->forum_id = $forum_id;
		$this->display();
	}

	/**
	 * 回复搜索
	 * @author waco <etoupcom@126.com>
	 */
	public function searchReply() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$map         = array();
		//显示正常状态的帖子
		$map['status'] = 1;
		list(
			$p,
			$soso,
			$keyword,
			$nickname,
			$time_start,
			$time_end,
			$forum_id,
			$uid,
			$create_ip
		) = array(
			I('request.p', 1, 'int'),
			I('request.soso', 0, 'int'),
			I('request.keyword', ''),
			I('request.nickname', ''),
			I('request.time_start', ''),
			I('request.time_end', ''),
			I('request.forum_id', 0, 'int'),
			I('request.uid', 0, 'int'),
			I('request.create_ip', '')
		);
		$keyword && $map['content']   = array('like', '%'.$keyword.'%');
		$nickname && $map['nickname'] = array('like', '%'.$nickname.'%');
		$forum_id && $map['forum_id'] = intval($forum_id);
		if ($time_start && $time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($time_start && !$time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		$this->list       = $this->lists('ForumPostReplyView', $map, 'create_time desc', true, 1);
		$this->p          = $p;
		$this->soso       = $soso?true:false;
		$this->keyword    = $keyword;
		$this->nickname   = $nickname;
		$this->forum_id   = $forum_id;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->display('reply');
	}

	/**
	 * 回复删除
	 * @author waco <etoupcom@126.com>
	 */
	public function delReply() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$data = array(
				'id'           => array('in', $id),
				'operator'     => USERNAME,
				'operate_time' => NOW_TIME,
				'reason'       => '管理员删除',
				'status'       => -1
			);
			M('ForumPostReply')->save($data);
		} else {
			$data = array('id' => $id, 'operator' => USERNAME, 'operate_time' => NOW_TIME, 'reason' => '管理员删除', 'status' => -1);
			M('ForumPostReply')->save($data);
		}
		ajaxMsg('操作成功');
	}

	/**
	 * 回复高级搜索
	 * @author waco <etoupcom@126.com>
	 */
	public function advancedReply() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$this->display();
	}

	/**
	 * 回复审核
	 * @author waco <etoupcom@126.com>
	 */
	public function checkReply() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$map         = array();
		//显示待审状态的帖子
		$map['status'] = 0;
		list(
			$p,
			$soso,
			$nickname,
			$time_start,
			$time_end,
			$forum_id
		) = array(
			I('request.p', 1, 'int'),
			I('request.soso', 0, 'int'),
			I('request.nickname', ''),
			I('request.time_start', ''),
			I('request.time_end', ''),
			I('request.forum_id', 0, 'int')
		);
		$nickname && $map['nickname'] = array('like', '%'.$nickname.'%');
		$forum_id && $map['forum_id'] = intval($forum_id);
		if ($time_start && $time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($time_start && !$time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		$this->list       = $this->lists('ForumPostReplyView', $map, 'create_time desc', true, 1);
		$this->p          = $p;
		$this->soso       = $soso?true:false;
		$this->nickname   = $nickname;
		$this->forum_id   = $forum_id;
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
		$this->display();
	}

	/**
	 * 回复通过
	 * @author waco <etoupcom@126.com>
	 */
	public function passReply() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$data = array('id' => array('in', $id), 'status' => 1);
			M('ForumPostReply')->save($data);
		} else {
			$data = array('id' => $id, 'status' => 1);
			M('ForumPostReply')->save($data);
		}
		ajaxMsg('操作成功');
	}

	/**
	 * 回复回收站
	 * @author waco <etoupcom@126.com>
	 */
	public function recycleReply() {
		$data        = D('Forum')->getForumByStatus(2);
		$this->flist = $this->_arrayValueSort($data);
		$map         = array();
		//显示正常状态的回复
		$map['status'] = -1;
		list(
			$p,
			$soso,
			$keyword,
			$nickname,
			$time_start,
			$time_end,
			$forum_id,
			$operator,
			$operate_time_start,
			$operate_time_end,
		) = array(
			I('request.p', 1, 'int'),
			I('request.soso', 0, 'int'),
			I('request.keyword', ''),
			I('request.nickname', ''),
			I('request.time_start', ''),
			I('request.time_end', ''),
			I('request.forum_id', 0, 'int'),
			I('request.operator', ''),
			I('request.operate_time_start', ''),
			I('request.operate_time_end', ''),
		);
		$keyword && $map['title']     = array('like', '%'.$keyword.'%');
		$nickname && $map['nickname'] = array('like', '%'.$nickname.'%');
		$operator && $map['operator'] = array('like', '%'.$operator.'%');
		$forum_id && $map['forum_id'] = intval($forum_id);
		if ($time_start && $time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($time_start && !$time_end) {
			$map['create_time'] = array('gt', strtotime($time_start));
		}
		if (!$time_start && $time_end) {
			$map['create_time'] = array('lt', strtotime($time_end));
		}
		if ($operate_time_start && $operate_time_end) {
			$map['operate_time'] = array('gt', strtotime($operate_time_start));
			$map['operate_time'] = array('lt', strtotime($operate_time_end));
		}
		if ($operate_time_start && !$operate_time_end) {
			$map['operate_time'] = array('gt', strtotime($operate_time_start));
		}
		if (!$operate_time_start && $operate_time_end) {
			$map['operate_time'] = array('lt', strtotime($operate_time_end));
		}
		$this->list               = $this->lists('ForumPostReplyView', $map, 'create_time desc', true, 1);
		$this->p                  = $p;
		$this->soso               = $soso?true:false;
		$this->keyword            = $keyword;
		$this->nickname           = $nickname;
		$this->operator           = $operator;
		$this->forum_id           = $forum_id;
		$this->time_start         = $time_start;
		$this->time_end           = $time_end;
		$this->operate_time_start = $operate_time_start;
		$this->operate_time_end   = $operate_time_end;
		$this->display();
	}

	/**
	 * 回复还原
	 * @author waco <etoupcom@126.com>
	 */
	public function restoreReply() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$back = M('ForumPostReply')->where(array('id' => array('in', $id)))->setField('status', 1);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('ForumPostReply')->where(array('id' => $id))->setField('status', 1);
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 回复清理
	 * @author waco <etoupcom@126.com>
	 */
	public function clearReply() {
		$id = I('request.id');
		empty($id) && ajaxMsg('请选择操作项', false);
		if (is_array($id)) {
			$back = M('ForumPostReply')->where(array('id' => array('in', $id)))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		} else {
			$back = M('ForumPostReply')->where(array('id' => $id))->delete();
			$back?ajaxMsg('操作成功'):ajaxMsg('操作失败', false);
		}
	}

	/**
	 * 对版块信息进行分组和排序
	 * @author waco <etoupcom@126.com>
	 */
	private function _arrayValueSort($array) {
		if (!is_array($array)) {
			return array();
		}

		$_array = array();
		$_key   = 0;
		foreach ($array as $key => $value) {
			if ($value['parentid'] == '0') {
				$_key                  = $key;
				$_array[$_key]         = $value['sort'];
				$array[$_key]['child'] = array();
			} else {
				$array[$_key]['child'][] = $array[$key];
			}
		}
		asort($_array, SORT_NUMERIC);
		foreach ($_array as $_key => $_value) {
			$_array[$_key] = $array[$_key];
		}
		return $_array;
	}
}
