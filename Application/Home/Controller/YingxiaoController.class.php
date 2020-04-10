<?php
namespace Home\Controller;
use Think\Controller;
class YingxiaoController extends PublicController {
	
	public function _initialize(){
        $user=$_SESSION['user'];
		  if(empty($user)){
		     $this->weixindeng();
		  }
		  $hezuo=$this->hezuo();
		  $this->hz=$hezuo;
    }

    // 限时购
    public function xs_list(){
    	$ktime=date("Y-m-d 00:00:00",time());
    	$endtime=date("Y-m-d 23:59:00",time());
    	$where['ktime'] = array("gt", $ktime);
    	$where['endtime'] = array("lt", $endtime);
    	$where['type'] = 1;
    	$chang=M("yingxiao")->where($where)->select();
    	foreach($chang as $k=>$v){
    		if(strtotime($v['ktime'])>time()){
    			 $chang[$k]['zhuangtai'] = 0;
    		}else if(strtotime($v['ktime']) < time() && time() < strtotime($v['endtime'])){
    			 $chang[$k]['zhuangtai'] = 1;
    		}else if(time()>strtotime($v['endtime'])){
    			$chang[$k]['zhuangtai'] = 2;
    		}
    		$store = M("yingxiao_store")->where("chang_id='".$v['id']."'")->select();
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
    		$chang[$k]['store']=$store;
    	}
    	$this->xianshigou=$chang;
    	$this->display();
    }

    public function xs_shangpin(){
    	$this->display();
    }

    //整点秒杀
    public function zd_list(){
    	$ktime=date("Y-m-d 00:00:00",time());
    	$whe['ktime']=array("gt",$ktime);
    	$whe['type']=2;
    	$miao=M("yingxiao")->where($whe)->select();
    	foreach($miao as $k=>$v){
    		 if(strtotime($v['ktime']) <time()){
    		 	$miao[$k]['zhuangtai']='已开抢';
    		 }else{
    		 	$miao[$k]['zhuangtai']='未开始';
    		 }
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
    	$this->miaosha=$miao;
    	$this->display();
    }
    
    public function zd_shangpin(){
    	$this->display();
    }
    
    
}