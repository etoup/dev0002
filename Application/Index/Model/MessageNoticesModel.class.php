<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: waco <etoupcom@126.com> <http://www.etoup.com>
// +----------------------------------------------------------------------

namespace Index\Model;
use Think\Model;

/**
 * 消息模型
 */

class MessageNoticesModel extends Model {

    //保存数据
    function saveData($uid=0,$title='',$user_con='',$toadmin=0,$admin_con='',$types=0){
        $data = [
            'uid' => $uid,
            'title'=>$title,
            'types'=>$types,
            'to_admin'=>$toadmin,
            'created_time'  => NOW_TIME,
            'update_time' => NOW_TIME,
            'user_con'=>$user_con,
            'admin_con'=>$admin_con
        ];
        M('MessageNotices')->add($data);
        M('Member')->where(['uid'=>$uid])->setInc('notices');
        return true;
    }
}
