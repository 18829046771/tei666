<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
    	$this->redirect('Home/Xin/index');
    }

    //其他商品页面
    public function qita(){
    	
     	$user=$_SESSION['user'];
    	$this->userimg=$user['img'];
    	$this->username=$user['username'];
     	$che=M("cate")->where("cate_p_id=85")->select();
     	//dump($che);
        $this->assign('list',$che);
        //轮博图
    	$tu=M("picture")->where("id=33||(id=34)||(id=35)||(id=36)||(id=37)")->select();
    	
    	$this->tu1=$tu[0]['picture_url'];
    	$this->tu2=$tu[1]['picture_url'];
    	$this->tu3=$tu[2]['picture_url'];
    	$this->tu4=$tu[3]['picture_url'];
    	$this->tu5=$tu[4]['picture_url'];
        $this->url1=$tu[0]['url'];
        $this->url2=$tu[1]['url'];
    	$this->url3=$tu[2]['url'];
    	$this->url4=$tu[3]['url'];
    	$this->url5=$tu[4]['url'];
    	//电子汽车
    	$ids=$che[0]['cate_id'];
        $temp=M("article")->where("art_cate_id='".$ids."' and type=1")->select();
	    $this->che=$temp[0];
	    //下面
	     
	     $arr=array();
	    foreach($che as $k=>$v){
	    	 
	    	 $tt=M("article")->where(array('art_cate_id'=>$v['cate_id'],'type'=>1,'jiage'=>array('neq','')))->order("art_id desc")->limit("2")->select();
	    	
    		array_push($arr,$tt);
	    	
	    }
//	     dump($arr);die;
	    foreach($arr as $k=> $v){
	    	 foreach($v as $t=>$j){
	    	 	$cate_name=M("cate")->where("cate_id='".$j['art_cate_id']."'")->find();
	    	 	$arr[$k]["fen"]=$cate_name['cate_name'];
	    	 	$arr[$k]["fen_id"]=$cate_name['cate_id'];
	    	 }
	    	
	    }
	    $this->arr1=$arr[0];
	    $this->arr2=$arr[1];
	    $this->arr3=$arr[2];
	    $this->arr4=$arr[3];
	    $this->arr5=$arr[4];
	    $this->arr6=$arr[5];
//	      dump($arr);die;
         // dump($arr[3]);
	     $this->arr=$arr;
	    
    	$this->display();
       
    
    }
    
    
//  public function gaishuju(){
//  	$Brand = M('dingdan');
//      $catep=M("cate")->where("cate_p_id=102")->getField('cate_id',true);
//      $t = array();
//      $t['art_cate_id'] = array('in',$catep);
//      $cate=M("article")->where($t)->getField('art_id',true); 
//      $tt = array();
//      $tt['store_id'] = array('in',$cate);
//      $temp=$Brand->where($tt)->select();
//      foreach($temp as $k=>$v){
//      	  $res=$Brand->where("id='".$v['id']."'")->save(array('type'=>3));
//      }
//      dump("ok3");die;
//      dump($temp);die;
//  }
    
}