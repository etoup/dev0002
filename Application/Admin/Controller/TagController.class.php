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
 * 话题管理控制器
 * @author waco <etoupcom@126.com>
 */
class TagController extends AdminController {

	/**
	 * 标签管理
	 * @author waco <etoupcom@126.com>
	 */
	public function index() {
		$data = array();
		list(
			$keyword,
			$ifhot,
			$categoryId,
			$minAttention,
			$maxAttention,
			$minContent,
			$maxContent,
			$p,
			$soso
		) = array(
			I('keyword', ''),
			I('ifhot', -1, 'int'),
			I('categoryId', 0, 'int'),
			I('minAttention', 0, 'int'),
			I('maxAttention', 0, 'int'),
			I('minContent', 0, 'int'),
			I('maxContent', 0, 'int'),
			I('p', 1, 'int'),
			I('soso', 0, 'int')
		);
		$keyword && $data['tag_name']   = array('like', '%'.$keyword.'%');
		($ifhot > -1) && $data['ifhot'] = $ifhot;
		if ($categoryId) {
			$tagIds         = M('TagCategoryRelation')->where(array('category_id' => $categoryId))->getField('tag_id', true);
			$data['tag_id'] = array('in', $tagIds);
		}
		if ($minAttention && $maxAttention && ($minAttention < $maxAttention)) {
			$data['attention_count'] = array('gt', $minAttention);
			$data['attention_count'] = array('lt', $maxAttention);
		}
		if ($minAttention && !$maxAttention) {
			$data['attention_count'] = array('gt', $minAttention);
		}
		if ($maxAttention && !$minAttention) {
			$data['attention_count'] = array('lt', $maxAttention);
		}
		if ($minContent && $maxContent && ($minContent < $maxContent)) {
			$data['content_count'] = array('gt', $minContent);
			$data['content_count'] = array('lt', $maxContent);
		}
		if ($minContent && !$maxContent) {
			$data['content_count'] = array('gt', $minContent);
		}
		if ($maxContent && !$minContent) {
			$data['content_count'] = array('lt', $maxContent);
		}
		$list = $this->lists('Tag', $data, 'created_time desc', true, 2);
		if (is_array($list) && !empty($list)) {
			foreach ($list as $key => $value) {
				$list[$key]['rl'] = M('Tag')->where(
					array(
						'parent_tag_id' => $value['tag_id'],
					)
				)->getField('tag_name');
				if (empty($value['category'])) {
					continue;
				}
				$categorys = array();
				foreach ($value['category'] as $k => $v) {
					array_push($categorys, $v['category_name']);
				}
				$list[$key]['cl'] = implode('，', $categorys);
			}
		}
		$this->list         = $list;
		$this->p            = $p;
		$this->soso         = $soso?true:false;
		$this->ifhot        = $ifhot > -1?$ifhot:-1;
		$this->keyword      = $keyword?$keyword:'';
		$this->categoryId   = $categoryId?$categoryId:0;
		$this->minAttention = $minAttention?$minAttention:'';
		$this->maxAttention = $maxAttention?$maxAttention:'';
		$this->minContent   = $minContent?$minContent:'';
		$this->maxContent   = $maxContent?$maxContent:'';
		$this->categories   = M('TagCategory')->field('category_id,category_name')->select();
		$this->display();
	}

	/**
	 * 设为热门
	 * @author waco <etoupcom@126.com>
	 */
	public function sethot() {
		$tag_id = I('post.tag_id', array());
		empty($tag_id) && ajaxMsg('请选择操作项', false);
		M('Tag')->where(array('tag_id' => array('in', $tag_id)))->save(array('ifhot' => 1));
		ajaxMsg('操作成功');
	}

	/**
	 * 取消热门
	 * @author waco <etoupcom@126.com>
	 */
	public function delhot() {
		$tag_id = I('post.tag_id', array());
		empty($tag_id) && ajaxMsg('请选择操作项', false);
		M('Tag')->where(array('tag_id' => array('in', $tag_id)))->save(array('ifhot' => 0));
		ajaxMsg('操作成功');
	}

	/**
	 * 删除话题
	 * @author waco <etoupcom@126.com>
	 */
	public function del() {
		$tag_id = I('post.tag_id', array());
		empty($tag_id) && ajaxMsg('请选择操作项', false);
		M('Tag')->where(array('tag_id' => array('in', $tag_id)))->delete();
		ajaxMsg('操作成功');
	}

	/**
	 * 合并关联话题
	 * @author waco <etoupcom@126.com>
	 */
	public function merge() {
		if (IS_POST) {
            p(I('request.'));
			$tagName = I('post.tag_name', '');
			$tagId   = I('post.tag_id', array());
			$tagIds  = explode(',', $tagId);
			if (!$tagIds || !$tagName) {
				ajaxMsg('请输入关联话题名称', false);
			}

			$tag = M('Tag')->where(array('tag_name' => $tagName))->find();
			//检查待合并的TAG有无所属话题
			$checkTags = M('Tag')->where(array('tag_id' => array('in', $tagIds)))->select();
			foreach ($checkTags as $v) {
				$parentTag = M('Tag')->where(array('tag_id' => $v['parent_tag_id']))->find();
				$parentTag && ajaxMsg(sprintf('话题"%s"已经合并到话题"%s",不允许再合并到其他话题', $v['tag_name'], $parentTag['tag_name']), false);
				$tagInfo = M('Tag')->where(array('parent_tag_id' => $v['tag_id']))->find();
				$tagInfo && ajaxMsg(sprintf('话题"%s"已经存在合并话题"%s",不允许被合并到其他话题', $v['tag_name'], $tagInfo['tag_name']), false);
			}
			if (!$tag) {
				$toTagId = M('Tag')->add(array('tag_name' => $tagName));
			} else {
				$toTagId = $tag['tag_id'];
				if ($tag['parent_tag_id']) {
					$parentTag = M('Tag')->where(array('tag_id' => $tag['parent_tag_id']))->find();
					ajaxMsg(sprintf('话题"%s"已经合并到话题"%s",不允许合并其他话题', $tag['tag_name'], $parentTag['tag_name']), false);
				}
			}
			foreach ($tagIds as $tagId) {
				$this->_addRelateTag($tagId, $tagName);
			}
			ajaxMsg('合并关联话题成功');
		} else {

			$this->display();
		}

	}

	/**
	 * 移动标签分类
	 * @author waco <etoupcom@126.com>
	 */
	public function move() {
		if (IS_POST) {
			$tagId       = I('post.tag_id', array());
			$tagIds      = explode(',', $tagId);
			$categoryIds = I('post.category_ids', array());
			if (!$tagIds || !$categoryIds) {
				ajaxMsg('请选择操作项', false);
			}
			if (count($categoryIds) > 1) {
				$key = array_search(0, $categoryIds);
				if ($key !== false) {
					unset($categoryIds[$key]);
				}
			}
			foreach ($tagIds as $tagId) {
				$this->updateCategoryRelations($tagId, $categoryIds);
			}
			ajaxMsg('操作成功');
		} else {
			$this->categories = M('TagCategory')->field('category_id,category_name')->select();
			$this->display();
		}

	}

	private function updateCategoryRelations($tagId, $categoryIds) {
		M('TagCategoryRelation')->where(array('tag_id' => $tagId))->delete();
		if (is_array($categoryIds) && !empty($categoryIds)) {
			foreach ($categoryIds as $key                 => $value) {
				M('TagCategoryRelation')->add(array('tag_id' => $tagId, 'category_id' => $value));
			}
			return true;
		} else {
			return false;
		}

	}

	/**
	 * 添加标签
	 * @author waco <etoupcom@126.com>
	 */
	public function add() {
		if (IS_POST) {
			$tag = I('post.tag', array());
			$tag['name'] || ajaxMsg('请填写话题名称', false);
			$this->_getTagByName($tag['name']) && ajaxMsg("话题{$tag['name']}已存在", false);
			$icon = I('post.icon', 0, 'int');
			$data = array();
			//处理图片
			if ($icon) {
				$info  = M('Picture')->find($icon);
				$image = new \Think\Image();
				$image->open('./'.$info['path']);
				// 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
				$image->thumb(50, 50, \Think\Image::IMAGE_THUMB_CENTER)->save('./Uploads/Tag/'.$icon.'_tag.jpg');
				$data['tag_logo'] = './Uploads/Tag/'.$icon.'_tag.jpg';
				$data['iflogo']   = 1;
			}
			$data['created_userid']                             = UID;
			$data['icon']                                       = $icon;
			$data['tag_name']                                   = $tag['name'];
			$data['ifhot']                                      = 1;
			$data['created_time']                               = NOW_TIME;
			$tag['excerpt'] && $data['excerpt']                 = $tag['excerpt'];
			$tag['seo_title'] && $data['seo_title']             = $tag['seo_title'];
			$tag['seo_description'] && $data['seo_description'] = $tag['seo_description'];
			$tag['seo_keywords'] && $data['seo_keywords']       = $tag['seo_keywords'];
			if ($tid = M('Tag')->add($data)) {
				//处理话题分类
				if (is_array($tag['category']) && !empty($tag['category'])) {
					foreach ($tag['category'] as $v) {
						M('TagCategoryRelation')->add(array('tag_id' => $tid, 'category_id' => $v));
					}
				}
				//处理关联话题
				if ($tag['relate_tags']) {
					$tagNames = explode(',', $tag['relate_tags']);
					foreach ($tagNames as $v) {
						$this->_addRelateTag($tid, $v);
					}
				}
				ajaxMsg('操作成功');
			} else {
				ajaxMsg('操作失败', false);
			}
		} else {
			$this->categories = M('TagCategory')->field('category_id,category_name')->select();
			$this->display();
		}
	}

	/**
	 * 删除标签
	 * @author waco <etoupcom@126.com>
	 */
	public function edit() {
		if (IS_POST) {
			$tagId = I('post.tag_id', 0, 'int');
			$tagId || ajaxMsg('请选择操作项', false);
			$tag = I('post.tag', array());
			$tag['name'] || ajaxMsg('请填写话题名称', false);
			$icon    = I('post.icon', 0, 'int');
			$oldIcon = I('post.oldIcon', 0, 'int');
			$data    = array();
			//处理图片
			if ($icon && $icon != $oldIcon) {
				$info  = M('Picture')->find($icon);
				$image = new \Think\Image();
				$image->open('./'.$info['path']);
				// 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
				$image->thumb(50, 50, \Think\Image::IMAGE_THUMB_CENTER)->save('./Uploads/Tag/'.$icon.'_tag.jpg');
				$data['tag_logo'] = './Uploads/Tag/'.$icon.'_tag.jpg';
				$data['iflogo']   = 1;
			}
			$data['tag_id']                                     = $tagId;
			$data['icon']                                       = $icon;
			$data['tag_name']                                   = $tag['name'];
			$tag['excerpt'] && $data['excerpt']                 = $tag['excerpt'];
			$tag['seo_title'] && $data['seo_title']             = $tag['seo_title'];
			$tag['seo_description'] && $data['seo_description'] = $tag['seo_description'];
			$tag['seo_keywords'] && $data['seo_keywords']       = $tag['seo_keywords'];
			//处理话题分类
			if (is_array($tag['category']) && !empty($tag['category'])) {
				$this->updateCategoryRelations($tagId, $tag['category']);
			}
			//处理关联话题
			if ($tag['relate_tags']) {
				$tagNames = explode(',', $tag['relate_tags']);
				foreach ($tagNames as $v) {
					$this->_addRelateTag($tagId, $v);
				}
			}
			M('Tag')->save($data);
			ajaxMsg('操作成功');
		} else {
			$tagId = I('get.tag_id', 0, 'int');
			$tagId || ajaxMsg('请选择操作项', false);
			$this->tag        = M('Tag')->find($tagId);
			$this->parentTags = M('Tag')->where(array('parent_tag_id' => $tagId))->getField('tag_name');
			$this->categories = M('TagCategory')->field('category_id,category_name')->select();
			$this->hasCates   = M('TagCategoryRelation')->where(array('tag_id' => $tagId))->getField('category_id', true);
			$this->display();
		}

	}

	/**
	 * 添加关联话题
	 *
	 * @param int $tagId 关联到哪个话题Id
	 * @param string $tagName 话题名称
	 */
	private function _addRelateTag($tagId, $tagName) {
		$tagId = intval($tagId);
		if ($tagId < 1 || !$tagName) {
			return false;
		}

		$tag = M('Tag')->find($tagId);
		if (!$tag) {
			return false;
		}

		$relateTag = M('Tag')->where(array('tag_name' => $tagName))->find();
		if (!$relateTag) {
			$data = array(
				'tag_name'       => $tagName,
				'created_userid' => UID,
				'created_time'   => NOW_TIME,
				'parent_tag_id'  => $tagId,
			);
			M('Tag')->add($data);
		} else {
			if ($relateTag['tag_id'] == $tagId) {
				return false;
			}
			//检查被关联的话题是否存在子话题
			if ($relateTag['parent_tag_id']) {
				return false;
			}
			$data = array(
				'tag_id'        => $relateTag['tag_id'],
				'parent_tag_id' => $tagId,
			);
			M('Tag')->save($data);
		}
		return true;
	}

	/**
	 * 话题名称检查
	 * @author waco <etoupcom@126.com>
	 */
	private function _getTagByName($name) {
		$id = M('Tag')->where(array('tag_name' => $name))->getField('tag_id');
		return $id;
	}

	/**
	 * 标签分类管理
	 * @author waco <etoupcom@126.com>
	 */
	public function category() {
		if (IS_POST) {
			list($data, $newdata) = array(I('post.data', array()), I('post.newdata', array()));
			$categorys            = M('TagCategory')->getField('category_id,category_name');
			if (is_array($data) && !empty($data)) {
				foreach ($data as $v) {
					unset($categorys[$v['category_id']]);
					$aliasWord = $this->_checkWork($v['alias']);
					if ($aliasWord !== true) {
						ajaxMsg($aliasWord, false);
					}
					if (in_array($v['category_name'], $categorys)) {continue;
					}

					$categorys[$v['category_id']] = $v['category_name'];
					$data                         = array(
						'category_id'   => $v['category_id'],
						'category_name' => $v['category_name'],
						'alias'         => $v['alias'],
						'vieworder'     => $v['vieworder'],
					);
					M('TagCategory')->save($data);
				}
			}
			if (is_array($newdata) && !empty($newdata)) {
				foreach ($newdata as $v) {
					$aliasWord = $this->_checkWork($v['alias']);
					if ($aliasWord !== true) {
						ajaxMsg($aliasWord, false);
					}
					if (in_array($v['category_name'], $categorys)) {continue;
					}

					$map = array(
						'category_name' => $v['category_name'],
						'alias'         => $v['alias'],
						'vieworder'     => $v['vieworder'],
					);
					M('TagCategory')->add($map);
				}
			}
			ajaxMsg('操作成功');
		} else {
			$this->categorys = M('TagCategory')->field('seo_title,seo_description,seo_keywords', true)->select();
			$this->display();
		}
	}
	/**
	 * 标签别名检查
	 * @author waco <etoupcom@126.com>
	 */
	private function _checkWork($str) {
		if (!$str) {return true;
		}

		if (0 >= preg_match('/^[A-Za-z]+$/', $str)) {
			return '别名只能为使用英文';
		}
		return true;
	}
}
