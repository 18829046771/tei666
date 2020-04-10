<?php
namespace Home\Controller;
use Think\Controller;
class 	DingshiController extends Controller {
	 //24小时未支付 ,自动结束订单
	public function jieshu(){
		 $temp=M("dingdan")->where("ster=0 and tuikuan=0")->select();
		 $time=strtotime(date('Y-m-d H:i:s',time()));
		 foreach($temp as $k=>$v){
		 	 $endtime=strtotime($v['time'])+24*60*60;
		 	 if($endtime<$time){
		 	 	$res=M("dingdan")->where("id='".$v['id']."'")->delete();
		 	 }
		 }
	}
	
	// 用户如果没有确认收货，订单在发货15个工作日后自动完成
	public function shouhuo(){
		$temp=M("dingdan")->where("ster=2 and tuikuan=0")->select();
		$time=strtotime(date('Y-m-d H:i:s',time()));
		foreach($temp as $k=>$v){
		 	 $endtime=strtotime($v['wuliu_time'])+24*60*60*15;
		 	 if($endtime<$time){
		 	 	$res = M("dingdan")->where(['id' => $v['id']])->save([
		 	 		'ster' => 4,
		 	 		'wan_time' => date('Y-m-d H:i:s', time())
		 	 	]);
		 	 }
		 }
	}
	
}

?>