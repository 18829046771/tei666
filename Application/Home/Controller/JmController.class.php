<?php
namespace Home\Controller;
use Think\Controller;
class JmController extends PublicController {
	  public function _initialize(){

        $user=$_SESSION['user'];
		  if(empty($user)){
		  	$this->weixindeng();
		  }
		  
		  $hezuo=$this->hezuo();
		  $this->hz=$hezuo;
    }


      //节目首页
      public function jm(){
      	 $cate=M("cate")->where("cate_p_id=82")->select();
      	//896汽车加速度
      	 $this->qi=$cate[0];
      	//老马识途
      	$this->ma=$cate[1];
      	
      	//精彩活动
      	$this->huo=$cate[2];
      	$this->display();
      }
      
      	//896汽车加速度
      public function qcjsd(){
      	$sousuo=I("sousuo");
    	if(empty($sousuo)){
    		$temp=M("article")->where("art_cate_id=83")->order("art_id desc")->select();
    	  
    	}else{
    		$where['art_cate_id']=83;
    		$where['product_title']=array('like','%'.$sousuo.'%');
    		$temp=M("article")->where($where)->order("art_id desc")->select();
    	}
    	
    	 $this->temp=$temp;
      	
      	$this->display();
      }
      
      //老马识途
      public function lmst(){
      	
      	$sousuo=I("sou");
    	if(empty($sousuo)){
    		$temp=M("article")->where("art_cate_id=84")->order("art_id desc")->select();
    	  
    	}else{
    		$where['art_cate_id']=84;
    		$where['product_title']=array('like','%'.$sousuo.'%');
    		$temp=M("article")->where($where)->order("art_id desc")->select();
    	}
    	  //dump($temp);
    	 $this->temp=$temp;
      	
      	$this->display();
      }
      
      
      //精彩活动
      public function jchd(){
      	//banns图
      	$banns=M("picture")->where("id=54")->find();
      	 $this->banns=$banns;
      	$sousuo=I("sou");
    	if(empty($sousuo)){
    		$temp=M("article")->where("art_cate_id=92")->order("art_id desc")->select();;
    	  
    	}else{
    		
    		$where['art_cate_id']=92;
    		$where['product_title']=array('like','%'.$sousuo.'%');
    		$temp=M("article")->where($where)->order("art_id desc")->select();
    	}
    	//dump($temp);die;
    	$this->temp=$temp;
      	$this->display();
      }
      
      
      public function hong(){
      	$id=I("id");
      	$temp=M("article")->where("art_id={$id}")->find();
      	$this->temp=$temp;
      	
      	$this->display();
      }
       
   
    
}