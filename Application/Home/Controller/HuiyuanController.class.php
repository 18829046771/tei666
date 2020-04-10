<?php
namespace Home\Controller;
use Think\Controller;

class HuiyuanController extends PublicController {
	
	public function _initialize() {
        $user = $_SESSION['user'];
		if (empty($user)) {
		  	$this->weixindeng();
		}

		$this->hz = $this->hezuo();
    }

	// 了解会员
	public function myvip(){
		$id = I('id/d');
		if(!empty($id)){
			$this->menid = $id;
		}

        // 微信JsApi参数
        $wxapi = new \WxPayApi();
        $timestamp = $this->getMillisecond();
        $nonceStr = $wxapi::getNonceStr();
        $this->jsApiArr = [
            'appId' => \WxPayConfig::APPID,
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'signature' => $this->getSignature($timestamp, $nonceStr)
        ];
        $this->shareImg = 'http://' . $_SERVER['HTTP_HOST'] . '/Public/xin/images/1.png';
		
		$this->display();
	}
	
	// 开通会员
	public function ktvip() {
		$id = I('id/d');

		$data['menid'] = empty($id) ? 0 : $id;
		$user = $_SESSION['user'];
		// 产生开通会员订单
		$paysn = makePaySn($user['id']);
		$data['paysn'] = $paysn;
		$data['uid'] = $user['id'];
		$data['name'] = '开通会员';
		$data['menid'] = $id;
		$data['ster'] = 0;
		$data['time'] = date('Y-m-d H:i:s', time());
		$res = M('huiyuan_paysn')->add($data);
		
		$temp = M('huiyuan_paysn')->where(['id' => $res])->find();
		$this->temp = $temp;

        // 微信JsApi参数
        $wxapi = new \WxPayApi();
        $timestamp = $this->getMillisecond();
        $nonceStr = $wxapi::getNonceStr();
        $this->jsApiArr = [
            'appId' => \WxPayConfig::APPID,
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'signature' => $this->getSignature($timestamp, $nonceStr)
        ];
        $this->shareImg = 'http://' . $_SERVER['HTTP_HOST'] . '/Public/xin/images/1.png';

		$this->display();
	}

	// 会员协议
	public function vipxieyi() {
		$this->display();
	}
	
	// 会员受业
	public function  vipindex(){
		$uid=$_SESSION['user']['id'];
		$user=M("user")->where(['id' => $uid])->find();
		$this->user=$user;
		//bassn
		$temp=M("picture")->where("id=49||id=50||id=51||id=52||id=53")->select();
		
		$this->bann1=$temp[0];
		$this->bann2=$temp[1];
		$this->bann3=$temp[2];
		$this->bann4=$temp[3];
		$this->bann5=$temp[4];
		
		//会员权益
		$quanyi=M("quanyi")->where("id=1||id=2||id=3||id=4||id=5")->select();
		$this->quan1=$quanyi[0];
		$this->quan2=$quanyi[1];
		$this->quan3=$quanyi[2];
		$this->quan4=$quanyi[3];
		$this->quan5=$quanyi[4];
		$this->display();
	}
	
   
    
}