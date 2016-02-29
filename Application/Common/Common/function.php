<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
const ONETHINK_VERSION    = '1.1.140825';
const ONETHINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author waco <etoupcom@126.com>
 */
function is_login() {
	$user = session('user_auth');
	if (empty($user)) {
		return 0;
	} else {
		return session('user_auth_sign') == data_auth_sign($user)?$user['uid']:0;
	}
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author waco <etoupcom@126.com>
 */
function is_administrator($uid = null, $config = false) {
	$uid = is_null($uid)?is_login():$uid;
	//组合管理员列表
	if (empty($config)) {
        return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
	} else {
		$admini  = $config?explode(',', $config):array();
		$manager = array_merge((array) C('USER_ADMINISTRATOR'), $admini);
		return $uid && (in_array(intval($uid), $manager));
	}
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author waco <etoupcom@126.com>
 */
function str2arr($str, $glue = ',') {
	return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author waco <etoupcom@126.com>
 */
function arr2str($arr, $glue = ',') {
	return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix?$slice.'...':$slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author waco <etoupcom@126.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key  = md5(empty($key)?C('DATA_AUTH_KEY'):$key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire?$expire+time():0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1))+(ord(substr($char, $i, 1)))%256);
	}
	return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author waco <etoupcom@126.com>
 */
function think_decrypt($data, $key = '') {
	$key  = md5(empty($key)?C('DATA_AUTH_KEY'):$key);
	$data = str_replace(array('-', '_'), array('+', '/'), $data);
	$mod4 = strlen($data)%4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data   = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data   = substr($data, 10);

	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1))+256)-ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1))-ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author waco <etoupcom@126.com>
 */
function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data);//排序
	$code = http_build_query($data);//url编码并生成query字符串
	$sign = sha1($code);//生成签名
	return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array($list)) {
		$refer = $resultSet = array();
		foreach ($list as $i => $data)
		$refer[$i] = &$data[$field];
		switch ($sortby) {
			case 'asc':// 正向排序
				asort($refer);
				break;
			case 'desc':// 逆向排序
				arsort($refer);
				break;
			case 'nat':// 自然排序
				natcasesort($refer);
				break;
		}
		foreach ($refer as $key => $val)
		$resultSet[] = &$list[$key];
		return $resultSet;
	}
	return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author waco <etoupcom@126.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = &$list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] = &$list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent           = &$refer[$parentId];
					$parent[$child][] = &$list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array($tree)) {
		$refer = array();
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if (isset($reffer[$child])) {
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby = 'asc');
	}
	return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author waco <etoupcom@126.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) {$size /= 1024;
	}

	return round($size, 2).$delimiter.$units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author waco <etoupcom@126.com>
 */
function set_redirect_url($url) {
	cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author waco <etoupcom@126.com>
 */
function get_redirect_url() {
	$url = cookie('redirect_url');
	return empty($url)?__APP__:$url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array()) {
	\Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name) {
	$class = "Addons\\{$name}\\{$name}Addon";
	return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name) {
	$class = get_addon_class($name);
	if (class_exists($class)) {
		$addon = new $class();
		return $addon->getConfig();
	} else {
		return array();
	}
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author waco <etoupcom@126.com>
 */
function addons_url($url, $param = array()) {
	$url        = parse_url($url);
	$case       = C('URL_CASE_INSENSITIVE');
	$addons     = $case?parse_name($url['scheme']):$url['scheme'];
	$controller = $case?parse_name($url['host']):$url['host'];
	$action     = trim($case?strtolower($url['path']):$url['path'], '/');

	/* 解析URL带的参数 */
	if (isset($url['query'])) {
		parse_str($url['query'], $query);
		$param = array_merge($query, $param);
	}

	/* 基础参数 */
	$params = array(
		'_addons'     => $addons,
		'_controller' => $controller,
		'_action'     => $action,
	);
	$params = array_merge($params, $param);//添加额外参数

	return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author waco <etoupcom@126.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	$time = $time === NULL?NOW_TIME:intval($time);
	return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {//获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if (empty($list)) {
		$list = S('sys_active_user_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset($list[$key])) {//已缓存，直接使用
		$name = $list[$key];
	} else {//调用接口获取用户信息
		$User = new User\Api\UserApi();
		$info = $User->info($uid);
		if ($info && isset($info[1])) {
			$name = $list[$key] = $info[1];
			/* 缓存用户 */
			$count = count($list);
			$max   = C('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			S('sys_active_user_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {//获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if (empty($list)) {
		$list = S('sys_user_nickname_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset($list[$key])) {//已缓存，直接使用
		$name = $list[$key];
	} else {//调用接口获取用户信息
		$info = M('Member')->field('nickname')->find($uid);
		if ($info !== false && $info['nickname']) {
			$nickname = $info['nickname'];
			$name     = $list[$key]     = $nickname;
			/* 缓存用户 */
			$count = count($list);
			$max   = C('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			S('sys_user_nickname_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null) {
	static $list;

	/* 非法分类ID */
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = S('sys_category_list');
	}

	/* 获取分类名称 */
	if (!isset($list[$id])) {
		$cate = M('Category')->find($id);
		if (!$cate || 1 != $cate['status']) {//不存在分类，或分类被禁用
			return '';
		}
		$list[$id] = $cate;
		S('sys_category_list', $list);//更新缓存
	}
	return is_null($field)?$list[$id]:$list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id) {
	return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id) {
	return get_category($id, 'title');
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = null) {
	$map = array('status' => 1, 'extend' => 0);
	if (!is_null($model_id)) {
		$map['id'] = array('neq', $model_id);
	}
	$model = M('Model')->where($map)->field(true)->select();
	foreach ($model as $value) {
		$list[$value['id']] = $value;
	}
	return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null) {
	static $list;

	/* 非法分类ID */
	if (!(is_numeric($id) || is_null($id))) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = S('DOCUMENT_MODEL_LIST');
	}

	/* 获取模型名称 */
	if (empty($list)) {
		$map   = array('status' => 1, 'extend' => 1);
		$model = M('Model')->where($map)->field(true)->select();
		foreach ($model as $value) {
			$list[$value['id']] = $value;
		}
		S('DOCUMENT_MODEL_LIST', $list);//更新缓存
	}

	/* 根据条件返回数据 */
	if (is_null($id)) {
		return $list;
	} elseif (is_null($field)) {
		return $list[$id];
	} else {
		return $list[$id][$field];
	}
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author waco <etoupcom@126.com>
 */
function ubb($data) {
	//TODO: 待完善，目前返回原始数据
	return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author waco <etoupcom@126.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null) {

	//参数检查
	if (empty($action) || empty($model) || empty($record_id)) {
		return '参数不能为空';
	}
	if (empty($user_id)) {
		$user_id = is_login();
	}

	//查询行为,判断是否执行
	$action_info = M('Action')->getByName($action);
	if ($action_info['status'] != 1) {
		return '该行为被禁用或删除';
	}

	//插入行为日志
	$data['action_id']   = $action_info['id'];
	$data['user_id']     = $user_id;
	$data['action_ip']   = ip2long(get_client_ip());
	$data['model']       = $model;
	$data['record_id']   = $record_id;
	$data['create_time'] = NOW_TIME;

	//解析日志规则,生成日志备注
	if (!empty($action_info['log'])) {
		if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
			$log['user']   = $user_id;
			$log['record'] = $record_id;
			$log['model']  = $model;
			$log['time']   = NOW_TIME;
			$log['data']   = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
			foreach ($match[1] as $value) {
				$param = explode('|', $value);
				if (isset($param[1])) {
					$replace[] = call_user_func($param[1], $log[$param[0]]);
				} else {
					$replace[] = $log[$param[0]];
				}
			}
			$data['remark'] = str_replace($match[0], $replace, $action_info['log']);
		} else {
			$data['remark'] = $action_info['log'];
		}
	} else {
		//未定义日志规则，记录操作url
		$data['remark'] = '操作url：'.$_SERVER['REQUEST_URI'];
	}

	M('ActionLog')->add($data);

	if (!empty($action_info['rule'])) {
		//解析行为
		$rules = parse_action($action, $user_id);

		//执行行为
		$res = execute_action($rules, $action_info['id'], $user_id);
	}
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author waco <etoupcom@126.com>
 */
function parse_action($action = null, $self) {
	if (empty($action)) {
		return false;
	}

	//参数支持id或者name
	if (is_numeric($action)) {
		$map = array('id' => $action);
	} else {
		$map = array('name' => $action);
	}

	//查询行为信息
	$info = M('Action')->where($map)->find();
	if (!$info || $info['status'] != 1) {
		return false;
	}

	//解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	$rules = $info['rule'];
	$rules = str_replace('{$self}', $self, $rules);
	$rules = explode(';', $rules);

	$return = array();
	foreach ($rules as $key => &$rule) {
		$rule = explode('|', $rule);
		foreach ($rule as $k => $fields) {
			$field = empty($fields)?array():explode(':', $fields);
			if (!empty($field)) {
				$return[$key][$field[0]] = $field[1];
			}
		}
		//cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
		if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
			unset($return[$key]['cycle'], $return[$key]['max']);
		}
	}

	return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author waco <etoupcom@126.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null) {
	if (!$rules || empty($action_id) || empty($user_id)) {
		return false;
	}

	$return = true;
	foreach ($rules as $rule) {

		//检查执行周期
		$map                = array('action_id' => $action_id, 'user_id' => $user_id);
		$map['create_time'] = array('gt', NOW_TIME-intval($rule['cycle'])*3600);
		$exec_count         = M('ActionLog')->where($map)->count();
		if ($exec_count > $rule['max']) {
			continue;
		}

		//执行数据库操作
		$Model = M(ucfirst($rule['table']));
		$field = $rule['field'];
		$res   = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

		if (!$res) {
			$return = false;
		}
	}
	return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files) {
	foreach ($files as $key => $value) {
		if (substr($value, -1) == '/') {
			mkdir($value);
		} else {
			@file_put_contents($value, '');
		}
	}
}

if (!function_exists('array_column')) {
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array();
		if (null === $indexKey) {
			if (null === $columnKey) {
				$result = array_values($input);
			} else {
				foreach ($input as $row) {
					$result[] = $row[$columnKey];
				}
			}
		} else {
			if (null === $columnKey) {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row;
				}
			} else {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row[$columnKey];
				}
			}
		}
		return $result;
	}
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author waco <etoupcom@126.com>
 */
function get_table_name($model_id = null) {
	if (empty($model_id)) {
		return false;
	}
	$Model = M('Model');
	$name  = '';
	$info  = $Model->getById($model_id);
	if ($info['extend'] != 0) {
		$name = $Model->getFieldById($info['extend'], 'name').'_';
	}
	$name .= $info['name'];
	return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true, $fields = true) {
	static $list;

	/* 非法ID */
	if (empty($model_id) || !is_numeric($model_id)) {
		return '';
	}

	/* 获取属性 */
	if (!isset($list[$model_id])) {
		$map    = array('model_id' => $model_id);
		$extend = M('Model')->getFieldById($model_id, 'extend');

		if ($extend) {
			$map = array('model_id' => array("in", array($model_id, $extend)));
		}
		$info            = M('Attribute')->where($map)->field($fields)->select();
		$list[$model_id] = $info;
	}

	$attr = array();
	if ($group) {
		foreach ($list[$model_id] as $value) {
			$attr[$value['id']] = $value;
		}
		$sort = M('Model')->getFieldById($model_id, 'field_sort');

		if (empty($sort)) {//未排序
			$group = array(1=> array_merge($attr));
		} else {
			$group = json_decode($sort, true);

			$keys = array_keys($group);
			foreach ($group as &$value) {
				foreach ($value as $key => $val) {
					$value[$key] = $attr[$val];
					unset($attr[$val]);
				}
			}

			if (!empty($attr)) {
				$group[$keys[0]] = array_merge($group[$keys[0]], $attr);
			}
		}
		$attr = $group;
	} else {
		foreach ($list[$model_id] as $value) {
			$attr[$value['name']] = $value;
		}
	}
	return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name, $vars = array()) {
	$array     = explode('/', $name);
	$method    = array_pop($array);
	$classname = array_pop($array);
	$module    = $array?array_pop($array):'Common';
	$callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
	if (is_string($vars)) {
		parse_str($vars, $vars);
	}
	return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author waco <etoupcom@126.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null) {
	if (empty($value) || empty($table)) {
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info            = M(ucfirst($table))->where($map);
	if (empty($field)) {
		$info = $info->field(true)->find();
	} else {
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author waco <etoupcom@126.com>
 */
function get_link($link_id = null, $field = 'url') {
	$link = '';
	if (empty($link_id)) {
		return $link;
	}
	$link = M('Url')->getById($link_id);
	if (empty($field)) {
		return $link;
	} else {
		return $link[$field];
	}
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author waco <etoupcom@126.com>
 */
function get_cover($cover_id, $field = null) {
	if (empty($cover_id)) {
		return false;
	}
	$picture = M('Picture')->where(array('status' => 1))->getById($cover_id);
	if ($field == 'path') {
		if (!empty($picture['url'])) {
			$picture['path'] = $picture['url'];
		} else {
			$picture['path'] = __ROOT__.$picture['path'];
		}
	}
	return empty($field)?$picture:$picture[$field];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author waco <etoupcom@126.com>
 */
function check_document_position($pos = 0, $contain = 0) {
	if (empty($pos) || empty($contain)) {
		return false;
	}

	//将两个参数进行按位与运算，不为0则表示$contain属于$pos
	$res = $pos&$contain;
	if ($res !== 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取数据的所有子孙数据的id值
 * @author waco <etoupcom@126.com>
 */

function get_stemma($pids, Model&$model, $field = 'id') {
	$collection = array();

	//非空判断
	if (empty($pids)) {
		return $collection;
	}

	if (is_array($pids)) {
		$pids = trim(implode(',', $pids), ',');
	}
	$result    = $model->field($field)->where(array('pid' => array('IN', (string) $pids)))->select();
	$child_ids = array_column((array) $result, 'id');

	while (!empty($child_ids)) {
		$collection = array_merge($collection, $result);
		$result     = $model->field($field)->where(array('pid' => array('IN', $child_ids)))->select();
		$child_ids  = array_column((array) $result, 'id');
	}
	return $collection;
}

/**
 * 验证分类是否允许发布内容
 * @param  integer $id 分类ID
 * @return boolean     true-允许发布内容，false-不允许发布内容
 */
function check_category($id) {
	if (is_array($id)) {
		$type = get_category($id['category_id'], 'type');
		$type = explode(",", $type);
		return in_array($id['type'], $type);
	} else {
		$publish = get_category($id, 'allow_publish');
		return $publish?true:false;
	}
}

/**
 * 检测分类是否绑定了指定模型
 * @param  array $info 模型ID和分类ID数组
 * @return boolean     true-绑定了模型，false-未绑定模型
 */
function check_category_model($info) {
	$cate  = get_category($info['category_id']);
	$array = explode(',', $info['pid']?$cate['model_sub']:$cate['model']);
	return in_array($info['model_id'], $array);
}

/**
 * 发送模板短信
 * @param to 手机号码集合,用英文逗号分开
 * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
 * @param $tempId 模板Id
 * @param demo调用 sendSms("18674049588", array('8590', '5'), "1");//手机号码，替换内容数组，模板ID
 * @return array
 */
function sendSms($to, $datas, $tempId) {
	// 初始化REST SDK
	list(
		$accountSid,
		$accountToken,
		$appId, $serverIP,
		$serverPort,
		$softVersion
	)     = getSms();
	$rest = new Common\Api\RestApi($serverIP, $serverPort, $softVersion);
	$rest->setAccount($accountSid, $accountToken);
	$rest->setAppId($appId);

	// 发送模板短信
	$result = $rest->sendTemplateSMS($to, $datas, $tempId);
	if ($result == NULL) {
		return array('status' => false, 'msg' => 'result error!');
	}
	if ($result->statusCode != 0) {
		return array('status' => false, 'msg' => $result->statusMsg, 'code' => $result->statusCode);
	} else {
		return array('status' => true, 'msg' => '发送成功，请注意查收');
	}
}

/**
 * 语音验证码
 * @param verifyCode 验证码内容，为数字和英文字母，不区分大小写，长度4-8位
 * @param playTimes 播放次数，1－3次
 * @param to 接收号码
 * @param displayNum 显示的主叫号码
 * @param respUrl 语音验证码状态通知回调地址，云通讯平台将向该Url地址发送呼叫结果通知
 * @param demo调用 voiceVerify("1586", "3", "18674049588", "4008738666", "http://127.0.0.1");
 * @return array
 */
function voiceVerify($verifyCode, $playTimes, $to, $displayNum, $respUrl) {
	// 初始化REST SDK
	list(
		$accountSid,
		$accountToken,
		$appId, $serverIP,
		$serverPort,
		$softVersion
	)     = getSms();
	$rest = new Common\Api\RestApi($serverIP, $serverPort, $softVersion);
	$rest->setAccount($accountSid, $accountToken);
	$rest->setAppId($appId);

	//调用语音验证码接口
	$result = $rest->voiceVerify($verifyCode, $playTimes, $to, $displayNum, $respUrl);
	if ($result == NULL) {
		return array('status' => false, 'msg' => 'result error!');
	}

	if ($result->statusCode != 0) {
		return array('status' => false, 'msg' => $result->statusMsg, 'code' => $result->statusCode);
	} else {
		return array('status' => true, 'msg' => '发送成功，请注意查收');
	}
}

/**
 * 获取短信配置
 * @return array
 */
function getSms() {
	//$config = M('Config')->where(array('group' => 10, 'type' => 1))->getField('value', true);
	$config = array(
		C('SMS_SID'),
		C('SMS_TOKEN'),
		C('SMS_APPID'),
		C('SMS_IP'),
		C('SMS_PORT'),
		C('SMS_VERSION')
	);
	return $config;
}

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function op_t($text) {
	$text = nl2br($text);
	$text = real_strip_tags($text);
	$text = addslashes($text);
	$text = trim($text);
	return $text;
}

/**
 * Ajax方式返回数据到客户端
 * @access protected
 * @param mixed $data 要返回的数据
 * @param String $type AJAX返回数据格式
 * @return void
 */
function ajaxMsg($data, $state = true) {
	if ($state) {
		// 返回JSON数据格式到客户端 包含状态信息
		header('Content-Type:application/json; charset=utf-8');
		if (is_array($data)) {
			$map = array(
				'referer' => $data['referer']?$data['referer']:'',
				'refresh' => $data['refresh']?$data['refresh']:false,
				'state'   => $data['state']?$data['state']:'success',
				'data'    => $data['data']?$data['data']:'',
				'html'    => $data['html']?$data['html']:'',
				'message' => $data['message']?$data['message']:array('操作成功'),
				'__error' => $data['__error']?$data['__error']:''
			);
		} else {
			$map = array(
				'refresh' => false,
				'state'   => 'success',
				'message' => array($data),
			);
		}
		exit(json_encode($map));
	} else {
		// 返回JSON数据格式到客户端 包含状态信息
		header('Content-Type:application/json; charset=utf-8');
		if (is_array($data)) {
			$map = array(
				'referer' => $data['referer']?$data['referer']:'',
				'refresh' => $data['refresh']?$data['refresh']:false,
				'state'   => $data['state']?$data['state']:'fail',
				'data'    => $data['data']?$data['data']:'',
				'html'    => $data['html']?$data['html']:'',
				'message' => $data['message']?$data['message']:array('操作失败'),
				'__error' => $data['__error']?$data['__error']:''
			);
		} else {
			$map = array(
				'refresh' => false,
				'state'   => 'fail',
				'message' => array($data),
			);
		}
		exit(json_encode($map));
	}
}

/**
 * 此函数对称加密解密
 * @param string string 明文 或 密文
 * @return string  operation DECODE表示解密,其它表示加密
 * @return string  key 密匙
 * @return int  expiry 密文有效期
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
	$ckey_length = 4;

	// 密匙
	$key = md5($key?$key:C('DATA_AUTH_KEY'));

	// 密匙a会参与加解密
	$keya = md5(substr($key, 0, 16));
	// 密匙b会用来做数据完整性验证
	$keyb = md5(substr($key, 16, 16));
	// 密匙c用于变化生成的密文
	$keyc = $ckey_length?($operation == 'DECODE'?substr($string, 0, $ckey_length):substr(md5(microtime()), -$ckey_length)):'';
	// 参与运算的密匙
	$cryptkey   = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
	// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
	$string        = $operation == 'DECODE'?base64_decode(substr($string, $ckey_length)):sprintf('%010d', $expiry?$expiry+time():0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result        = '';
	$box           = range(0, 255);
	$rndkey        = array();
	// 产生密匙簿
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i%$key_length]);
	}
	// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
	for ($j = $i = 0; $i < 256; $i++) {
		$j       = ($j+$box[$i]+$rndkey[$i])%256;
		$tmp     = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	// 核心加解密部分
	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a       = ($a+1)%256;
		$j       = ($j+$box[$a])%256;
		$tmp     = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		// 从密匙簿得出密匙进行异或，再转成字符
		$result .= chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
	}
	if ($operation == 'DECODE') {
		// substr($result, 0, 10) == 0 验证数据有效性
		// substr($result, 0, 10) - time() > 0 验证数据有效性
		// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
		// 验证数据有效性，请看未加密明文的格式
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10)-time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/**
 * 判断获得密码强度
 *
 * @param string $pwd 密码强度
 * @return int 返回强度级别：(1：弱,2: 一般, 3： 强, 4：非常强)
 */
function checkPwdStrong($pwd) {
	$array = array();
	$len   = strlen($pwd);
	$i     = 0;
	$mode  = array('a' => 0, 'A' => 0, 'd' => 0, 'f' => 0);
	while ($i < $len) {
		$ascii = ord($pwd[$i]);
		if ($ascii >= 48 && $ascii <= 57)//数字
		{ $mode['d']++;
		} elseif ($ascii >= 65 && $ascii <= 90)//大写字母
		{ $mode['A']++;
		} elseif ($ascii >= 97 && $ascii <= 122)//小写
		{ $mode['a']++;
		} else {

			$mode['f']++;
		}

		$i++;
	}
	/*全是小写字母或是大写字母或是字符*/
	if ($mode['a'] == $len || $mode['A'] == $len || $mode['f'] == $len) {
		return 2;
	}
	/*全是数字*/
	if ($mode['d'] == $len) {
		return 1;
	}

	$score = 0;
	/*大小写混合得分20分*/
	if ($mode['a'] > 0 && $mode['A'] > 0) {
		$score += 20;
	}
	/*如果含有3个以内（不包含0和3）数字得分10分,如果包括3个（含3个）以上得分20*/
	if ($mode['d'] > 0 && $mode['d'] < 3) {
		$score += 10;
	} elseif ($mode['d'] >= 3) {
		$score += 20;
	}
	/*如果含有一个字符得分10分，含有1个以上字符得分25*/
	if ($mode['f'] == 1) {
		$score += 10;
	} elseif ($mode['f'] > 1) {
		$score += 25;
	}
	/*同时含有：字母和数字 得25分；含有：字母、数字和符号 得30分；含有：大小写字母、数字和符号 得35分*/
	if ($mode['a'] > 0 && $mode['A'] > 0 && $mode['d'] > 0 && $mode['f'] > 0) {
		$score += 35;
	} elseif (($mode['a'] > 0 || $mode['A'] > 0) && $mode['d'] > 0 && $mode['f'] > 0) {
		$score += 30;
	} elseif (($mode['a'] > 0 || $mode['A'] > 0) && $mode['d'] > 0) {
		$score += 25;
	}
	if ($len < 3) {$score -= 10;
	}

	if ($score >= 60) {
		return 4;
	} elseif ($score >= 40) {
		return 3;
	} elseif ($score >= 20) {
		return 2;
	}
	return 1;
}

/**
 * 获得验证码
 *
 * @param int $len 位数
 * @return string
 */
function getCode($len = 4) {
	$str  = '123456789';
	$_tmp = strlen($str)-1;
	$code = '';
	$_num = 0;
	for ($i = 0; $i < $len; $i++) {
		$_num = mt_rand(0, $_tmp);
		$code .= $str[$_num];
	}
	return $code;
}
/**
 * 星号替换
 *
 * @return string
 */
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if($code == 'UTF-8')
    {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);
        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen));
        return join('', array_slice($t_string[0], $start, $sublen));
    }
    else
    {
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';
        for($i=0; $i< $strlen; $i++)
        {
            if($i>=$start && $i< ($start+$sublen))
            {
                if(ord(substr($string, $i, 1))>129)
                {
                    $tmpstr.= substr($string, $i, 2);
                }
                else
                {
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        //if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
        return $tmpstr;
    }
}

/**
 * 格式化金额
 *
 * @param string $money 金额
 * @param string $myunit 自定义单位 %s万元
 * @param int $num 基数
 * @return string
 */
function money($money = '0.00',$myunit='', $num=0) {
    if($num){
        $money = $money * $num;
        $unit = C('MONEYUNIT')?C('MONEYUNIT'):'￥%s';
        if($myunit){
            $unit = $myunit;
        }
        $money_format = sprintf($unit,number_format($money,2));
    }else{
        $unit = C('MONEYUNIT')?C('MONEYUNIT'):'￥%s';
        if($myunit){
            $unit = $myunit;
        }
        $money_format = sprintf($unit,number_format($money,2));
    }

    return $money_format;
}

/**
 * 检测手机号码是否合法
 *
 * @return string
 */
function check_mobile($mobilephone){
    if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobilephone)){
        return true;
    }else{
        return false;
    }
}
/**
 * 隐藏手机中间数字
 *
 * @return string
 */
function hide_mobile($mobile){
    return substr_replace($mobile,'*****',3,5);
}

function real_strip_tags($str, $allowable_tags = "") {
	$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	return strip_tags($str, $allowable_tags);
}

function is_selected($val, $opt) {
	return ($val == $opt)?'selected':'';
}
function in_checked($val, $opt) {
	return (in_array($val, $opt))?'checked':'';
}
function is_checked($val, $opt) {
	return ($val == $opt)?'checked':'';
}
//充值方式
function methodsFoRecord($val) {
    switch($val){
        case 0:
            $back = '线上支付';
            break;
        case 1:
            $back = '银行转账';
            break;
    }
    return $back;
}
//充值状态
function status($val) {
    switch($val){
        case 0:
            $back = '审核中...';
            break;
        case 1:
            $back = '成功';
            break;
        case -1:
            $back = '失败';
            break;
    }
    return $back;
}
//充值状态
function bidsStatus($val) {
    switch($val){
        case 0:
            $back = '投标中...';
            break;
        case 1:
            $back = '投标完成';
            break;
        case -1:
            $back = '投标失败';
            break;
    }
    return $back;
}
//到款方式
function pay_type($val) {
    switch($val){
        case 0:
            $back = '支付本息';
            break;
        case 1:
            $back = '支付意向金';
            break;
    }
    return $back;
}

function dateDiff($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->format('%a');
}

function peiziStuArr($key = null){
    $data = [
        0=>'待审',
        1=>'待付本息',
        2=>'配资中',
        3=>'待绑定账户',
        8=>'配资成功',
        -1=>'配资失败',
        -2=>'结束'
    ];
    if(isset($key))
        return $data[$key];
    return $data;
}

function fundsTypesArr($key = null){
    $data = [
        1=>'充值',
        2=>'提现',
        11=>'出金',
        12=>'投资失败返还',
        13=>'投资月收益',
        14=>'投资本金回款',
        15=>'提现失败返还',
        16=>'返还利息差额',
        17=>'推广收益',
        18=>'提前结束罚金收益',
        21=>'首期保证金',
        22=>'补充保证金',
        23=>'支付利息',
        24=>'扣除投标金额',
        25=>'支付意向金',
        26=>'充值手续费',
        27=>'提现手续费',
        28=>'提前结束罚金',
        29=>'逾期罚金'
    ];
    if(isset($key))
        return $data[$key];
    return $data;
}
function page2limit($page, $perpage = 10) {
    $limit = intval($perpage);
    $start = max(($page - 1) * $limit, 0);
    return array($start, $limit);
}


/**
 * 人民币小写转大写
 *
 * @param string $number   待处理数值
 * @param bool   $is_round 小数是否四舍五入,默认"四舍五入"
 * @param string $int_unit 币种单位,默认"元"
 * @return string
 */
function rmb_format($money = 0, $is_round = true, $int_unit = '元') {
    $chs     = array (0, '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
    $uni     = array ('', '拾', '佰', '仟' );
    $dec_uni = array ('角', '分' );
    $exp     = array ('','万','亿');
    $res     = '';
    // 以 元为单位分割
    $parts   = explode ( '.', $money, 2 );
    $int     = isset ( $parts [0] ) ? strval ( $parts [0] ) : 0;
    $dec     = isset ( $parts [1] ) ? strval ( $parts [1] ) : '';
    // 处理小数点
    $dec_len = strlen ( $dec );
    if (isset ( $parts [1] ) && $dec_len > 2) {
        $dec = $is_round ? substr ( strrchr ( strval ( round ( floatval ( "0." . $dec ), 2 ) ), '.' ), 1 ) : substr ( $parts [1], 0, 2 );
    }
    // number= 0.00时，直接返回 0
    if (empty ( $int ) && empty ( $dec )) {
        return '零';
    }

    // 整数部分 从右向左
    for($i = strlen ( $int ) - 1, $t = 0; $i >= 0; $t++) {
        $str = '';
        // 每4字为一段进行转化
        for($j = 0; $j < 4 && $i >= 0; $j ++, $i --) {
            $u   = $int{$i} > 0 ? $uni [$j] : '';
            $str = $chs [$int {$i}] . $u . $str;
        }
        $str = rtrim ( $str, '0' );
        $str = preg_replace ( "/0+/", "零", $str );
        $u2  = $str != '' ? $exp [$t] : '';
        $res = $str . $u2 . $res;
    }
    $dec = rtrim ( $dec, '0' );
    // 小数部分 从左向右
    if (!empty ( $dec )) {
        $res .= $int_unit;
        $cnt =  strlen ( $dec );
        for($i = 0; $i < $cnt; $i ++) {
            $u = $dec {$i} > 0 ? $dec_uni [$i] : ''; // 非0的数字后面添加单位
            $res .= $chs [$dec {$i}] . $u;
        }
        if ($cnt == 1) $res .= '整';
        $res = rtrim ( $res, '0' ); // 去掉末尾的0
        $res = preg_replace ( "/0+/", "零", $res ); // 替换多个连续的0
    } else {
        $res .= $int_unit . '整';
    }
    return $res;
}
function prevDate($time,$format='Y-m-d'){
    return time_format(intval($time-1),$format);
}

function _safe($str){

    $html_string = array("&amp;", "&nbsp;", "'", '"', "<", ">", "\t", "\r");

    $html_clear = array("&", " ", "&#39;", "&quot;", "&lt;", "&gt;", "&nbsp; &nbsp; ", "");

    $js_string = array("/<script(.*)<\/script>/isU");

    $js_clear = array("");



    $frame_string = array("/<frame(.*)>/isU", "/<\/fram(.*)>/isU", "/<iframe(.*)>/isU", "/<\/ifram(.*)>/isU",);

    $frame_clear = array("", "", "", "");



    $style_string = array("/<style(.*)<\/style>/isU", "/<link(.*)>/isU", "/<\/link>/isU");

    $style_clear = array("", "", "");



    $str = trim($str);

    //过滤字符串

    $str = str_replace($html_string, $html_clear, $str);

    //过滤JS

    $str = preg_replace($js_string, $js_clear, $str);

    //过滤ifram

    $str = preg_replace($frame_string, $frame_clear, $str);

    //过滤style

    $str = preg_replace($style_string, $style_clear, $str);

    return $str;

}
function getD(){
    return date('Y-m-d');
}
function getPassTime(){
    return strtotime('+30 minutes');
}
//function getNextMonthLastDay($date) {
//    return date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +2 month -1 day'));
//}
//封装打印函数
function p($data, $die = true) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	$die && die();
}