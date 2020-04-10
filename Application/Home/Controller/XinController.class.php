<?php
namespace Home\Controller;
use Think\Controller;

class XinController extends PublicController {
	
	public function _initialize(){
            	
        $user=$_SESSION['user'];
		  if(empty($user)){
		     $this->weixindeng();
		  }
		  $hezuo=$this->hezuo();
		  $this->hz=$hezuo;
    }

	
    public function index(){
    	  
          
    	$uid=$_SESSION['user']['id'];
    	$user=M("user")->where("id={$uid}")->find();
    	$this->huiyuan=$user['type'];
//  	dump($user);die;

    	//首页轮zz
    	$temp=M("picture")->where("id=38||id=39||id=40||id=41||id=42")->select();
    	$this->bann1=$temp[0];
    	$this->bann2=$temp[1];
    	$this->bann3=$temp[2];
    	$this->bann4=$temp[3];
    	$this->bann5=$temp[4];
    	//首页中间单图
    	$dan=M("picture")->where("id=43")->find();
    	$this->dan=$dan;
    	//首页中间轮播
    	$guangao=M("picture")->where("id=44||id=45||id=46||id=47||id=48")->select();
    	$this->zhong1=$guangao[0];
    	$this->zhong2=$guangao[1];
    	$this->zhong3=$guangao[2];
    	$this->zhong4=$guangao[3];
    	$this->zhong5=$guangao[4];
//  	 dump($guangao[0]);die;
    	//首页游天下
    	$you=M("cate")->where("cate_p_id=22")->select();
    	$list1=$you[0];
    	$list2=$you[1];
    	$list3=$you[2];
    	$list4=$you[3];
    	//dump($list4);die;
//     	$store1 = M("article")->where("art_cate_id='".$list1['cate_id']."' and type=1")->order("pai asc")->limit('1')->find();
//     	$store2 = M("article")->where("art_cate_id='".$list2['cate_id']."' and type=1 ")->order("pai asc")->limit('1')->find();
//     	$store3=M("article")->where("art_cate_id='".$list3['cate_id']."' and type=1 ")->order("pai asc")->limit('1')->find();
//     	$store4=M("article")->where("art_cate_id='".$list4['cate_id']."' and type=1 ")->order("pai asc")->limit('1')->find();
			$store1 = M("tuijian")->where("id=8")->find();
			$store2 = M("tuijian")->where("id=9")->find();
			$store3= M("tuijian")->where("id=10")->find();
			$store4= M("tuijian")->where("id=11")->find();
    	$this->store1=$store1;
    	$this->store2=$store2;
    	$this->store3=$store3;
    	$this->store4=$store4;
    	
    	
    	//推荐
    	$tu=M("tuijian")->where("id=1|| id=2 ||id=3 ||id=4")->select();
    	$this->tui=$tu;

    	//热卖
    	$mai=M("tuijian")->where("id=5|| id=6||id=7")->select();
    	$this->mai1=$mai[0];
    	$this->mai2=$mai[1];
    	$this->mai3=$mai[2];
    	// 限时购
    	$xianshitype=M("display")->where("id=1")->find();
    	$this->xianshi_type=$xianshitype['type'];
    	$ktime=date("Y-m-d 00:00:00",time());
    	$endtime=date("Y-m-d 23:59:00",time());
    	$where['ktime']=array("gt",$ktime);
    	$where['endtime']=array("lt",$endtime);
    	$where['type']=1;
    	$chang=M("yingxiao")->where($where)->select();
    	foreach($chang as $k=>$v){
    		$store = M("yingxiao_store")->where("chang_id='".$v['id']."'")->select();
    		foreach($store as $h=>$t){
    			 $teop=M("article")->where("art_id='" . $t['store_id']."'")->find();
    			 $store[$h]['store_name']=$teop['product_title']; //产品名称
    			 $store[$h]['store_img']=$teop['art_img_url'];//产品图片
    			 $store[$h]['store_huiyuan']=$teop['huiyuan'];//产品会员价
    			 $store[$h]['store_jiage']=$teop['jiage'];//产品商城价
                 $store[$h]['store_shuoming']=$teop['guige'];//产品说明
                 unset($gmap);
                 $gmap['cid'] = $teop['art_id'];
    			 $store[$h]['jiage'] = M('guige')->where($gmap)->order('money_ying asc')->getField('money_ying'); // 促销价
    		}
    		
    		$chang[$k]['store']=$store;
    	}
    	// 首页显示的限时购
    	$time=date("Y-m-d H:i:s",time());
    	foreach($chang as $k=>$v){
    		 if($time>$v['ktime'] && $time<$v['endtime']){
    		 	$xian=$chang[$k];
    		 }
    		
    	}
    	 $this->xian=$xian;
    	 if($xianshitype['type']=='1'){
    	 	$this->endtime=strtotime($xian['endtime']);
    	 }
    	 
    	$this->xianshigou=$chang;
    	
    	//整点秒杀
    	$miaotype=M("display")->where("id=2")->find();
    	$this->miao_type=$miaotype['type'];
    	
    	$whe['ktime']=array("gt",$ktime);
    	$whe['type']=2;
    	$miao=M("yingxiao")->where($whe)->select();
    	foreach($miao as $k=>$v){
    		$store=M("yingxiao_store")->where("chang_id='".$v['id']."'")->select();
    		foreach($store as $h=>$t){
    			 $teop=M("article")->where("art_id='".$t['store_id']."'")->find();
    			 $store[$h]['store_name']=$teop['product_title']; //产品名称
    			 $store[$h]['store_img']=$teop['art_img_url'];//产品图片
    			 $store[$h]['store_huiyuan']=$teop['huiyuan'];//产品会员价
    			 $store[$h]['store_jiage']=$teop['jiage'];//产品商城价
    			 $store[$h]['store_shuoming']=$teop['guige'];//产品说明 
                 unset($gmap);
                 $gmap['cid'] = $teop['art_id'];
                 $store[$h]['jiage'] = M('guige')->where($gmap)->order('money_ying asc')->getField('money_ying'); // 促销价
                 $store[$h]['num'] = M('guige')->where($gmap)->order('money_ying asc')->getField('num');
    		}
    		$miao[$k]['store']=$store;
    	}

    	// 首页显示
    	foreach($miao as $k=>$v){
    		$miaoxian=$miao[$k];
    	}
    	$this->miaoxian=$miaoxian;

    	$miaotime=$miaoxian['name'];
    	$miao_shi=substr($miaotime, 0,2); 
    	$miao_fen=substr($miaotime, -2); 
    	$this->miao_shi=$miao_shi;
    	$this->miao_fen=$miao_fen;
        $this->display();
    }

    public function hezuoqiye(){
    	$id=I("id");
    	$temp=M("gongying")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$store=M("article")->where("gong='".$id."' and type=1")->select();
    	$this->store=$store;
    	$this->display();
    }

    // Banner详情页
    public function hong(){
    	$id = I('id/d');
        empty($id) && die('参数错误！');
        $res = M('Picture')->where(['id' => $id])->find();
        if ($res) {
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
            $res['img'] = 'http://' . $_SERVER['HTTP_HOST'] . $res['picture_url'];

            $this->res = $res;
            $this->display();
        } else {
            die('信息不存在！');
        }
    }

    public function guige(){
    	$id=I("id");
    	$arr=M("guige")->where("id={$id}")->find();
    	if(!empty($arr)){
    			$this->ajaxReturn(array(
                    'state'=>'1',
                    'msg'=>$arr['money'],
										'num'=>$arr['num'],
                    'money_huiyuan'=>$arr['money_huiyuan'],
                    'money_ying' =>$arr['money_ying']
                ));
    	}else{
    		$this->ajaxReturn(array('state'=>'0','msg'=>'暂无该规格产品'));
    	}
    }

}