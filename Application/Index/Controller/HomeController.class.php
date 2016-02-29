<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Index\Controller;
use Think\Controller;
use Common\Api\BaiduApi;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {
    /* 空操作，用于输出404页面 */
    public function _empty() {
        $this->redirect('Index/index');
    }
    protected function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);//添加配置
        if (!C('WEB_SITE_CLOSE')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
        define('UID', is_login());
        if(UID){
            $member=M('UcenterMember')->find(UID);
            define('USERNAME', $member['username']);
            define('RATE', $member['rate']);
            $this->rate = RATE?RATE:C('INTERSTRATE');
        }

        //消息统计
        $this->unread_num = M('MessageNotices')->where(['uid'=>UID,'is_read'=>0])->count();
        define('ROOT',C('HTTPHOST'));
        $tnav = $this->_get_top_nav(true);
        if(!empty($tnav)){
            foreach($tnav as $k => $v){
                if(CONTROLLER_NAME.'/'.ACTION_NAME == $v['sign']){
                    $tnav[$k]['hover'] = true;
                }
                $tnav[$k]['url'] = U($v['link']);
            }
        }
        $this->tnav = $tnav;
        $this->bnav = $this->_get_top_nav(false);


        $ip = get_client_ip();
        if($ip == '127.0.0.1'){
            $ip = '211.137.76.143';
        }
        $BaiduApi = new BaiduApi();
        $info = $BaiduApi->locationByIP($ip);
        session('city',$info['city']);

    }
    /* 用户登录检测 */
    protected function login() {
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('Ucenter/Public/login'));
    }

    private function _get_top_nav($top) {
        $nav = D('Nav')->get_nav($top);
        return $nav;
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @param int      $type   模型类型  0：普通模型；1：视图模型；2：关联模型
     * @author waco <etoupcom@126.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists($model, $where = array(), $order = '', $field = true, $type = 0) {
        $options = array();
        $REQUEST = (array) I('request.');
        if (is_string($model)) {
            switch ($type) {
                case 1:
                    $model = D($model);
                    break;

                case 2:
                    $model = D($model)->relation(true);
                    break;

                default:
                    $model = M($model);
                    break;
            }
        }


        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk = $model->getPk();
        if ($order === null) {
            # code... //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk.' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);

        if (empty($where)) {
            $where = array('status' => array('egt', 0));
        }
        if (!empty($where)) {
            $options['where'] = $where;
        }
        $options = array_merge((array) $OPT->getValue($model), $options);
        $total   = $model->where($options['where'])->count();
        if (cookie('rows')) {
            $listRows = (int) cookie('rows');
        } elseif (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0?C('LIST_ROWS'):10;
        }
        $listRows = 5;//调试用
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p = $page->show();
        $this->assign('_page', $p?$p:'');
        $this->assign('_total', $total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options', $options);
        return $model->field($field)->select();
    }
}