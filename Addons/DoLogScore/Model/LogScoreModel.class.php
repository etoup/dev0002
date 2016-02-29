<?php

namespace Addons\DoLogScore\Model;
use Think\Model;

/**
 * log_score模型
 */
class LogScoreModel extends Model{
    protected $_validate = array(
        array('uid', 'require', '用户信息错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('getval', 'require', '操作积分信息错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('types', 'require', '操作类型信息错误', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT)
    );

    //处理积分
    protected function userScore($data,$doSave=true){
        if($doSave){
            if($data['uid']){
                //获取积分
                $score = M('Member')->where(array('uid'=>$data['uid']))->getField('score');
                //修改积分冗余
                $sumScore = intval($score)+intval($data['getval']);
                $map = ['score'=>$sumScore];
                M('Member')->where(array('uid'=>$data['uid']))->save($map);
            }
        }
        return $sumScore;
    }

    //处理备注
    protected function typeToRemark($data){
        if($data['types']){
            $list = unserialize(C('USERBEHAVIOR'));
            foreach($list as $k=>$v){
                if($v['unit']==$data['types']){
                    $remark = $v['name'];
                }
            }
        }
        return $remark;
    }

    //记录积分日志
    public function update($data){
        if($list = $this->create()){
            $list['score'] = $this->userScore($data);
            $list['remark'] = $this->typeToRemark($data);
            $back = $this->add($list);
            if($back)
                return true;
        }
    }
}
