<?php
namespace Admin\Model;
use Think\Model;

/**
 * 充值模型
 */

class MainRecordModel extends Model {
	/**
	 * 模型自动完成
	 * @var array
	 */
	protected $_auto = array(
		array('update_time', NOW_TIME, self::MODEL_UPDATE)
	);

	public function saveData($id, $remark='', $status=1) {
        $map = [
            'id'=>$id,
            'remark' => $remark,
            'status'=>$status
        ];

		$data = $this->create($map);
        $back = $this->save($data);
		return $back;
	}
}
