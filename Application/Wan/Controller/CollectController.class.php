<?php
namespace Wan\Controller;
use Think\Controller;

class CollectController extends PublicController {

    public function index(){
    	$db = M('shoucang');

    	$list = $db->where(['uid' => $_SESSION['user']['id']])->order('time desc')->select();
    	$dataList = [];
    	foreach ($list as $key => $value) {
            unset($where);
            $where['is_sj'] = ['eq', 1];
            $where['id'] = ['eq', $value['hid']];
    		$huodong = M('huodong')->where($where)->find();
            if ($huodong) {
                $dataList[$key] = $huodong;
                $dataList[$key]['sid'] = $value['id'];

                $flag = 1; // 进行中
                if ($huodong['end_time'] < time()) {
                    $flag = 3; // 已结束
                }

                $count = M('baoming')->where(['hid' => $huodong['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
                $count = $count ? $count : 0;
                if ($count && $count == $huodong['num']) {
                    $flag = 2; // 已售罄
                }
                $dataList[$key]['sc'] = 1;
                $dataList[$key]['flag'] = $flag;
            } else {
                continue;
            }

    	}
    	$this->dataList = arr_sort($dataList, 'flag');
        $this->display();
    }

}