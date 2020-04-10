<?php
namespace Home\Controller;
use Think\Controller;

class TourismController extends PublicController {
	public function _initialize(){
        $user = $_SESSION['user'];
		if(empty($user)){
		  	$this->weixindeng();
		}
		$hezuo=$this->hezuo();
		$this->hz=$hezuo;
    }

	public function ytx(){
//		$user=$_SESSION['user'];
//		
//  	$this->userimg=$user['img'];
//  	$this->username=$user['username'];

    	//分类
    	$guonei=M("cate")->where("cate_p_id=22")->select();
 	   
     	$this->temp=$guonei;
     	$this->si=$guonei[4];

	    $arr=array();
    	foreach($guonei as $k=>$v){
    		$tst=M("article")->where("art_cate_id='".$v['cate_id']."' and type=1")->order("pai asc")->select();
    		 
    		 $tt=$tst[0];
    		array_push($arr,$tt);
    	}
    	
	 
    	foreach($arr as $k=>$v){
    		$name=M("cate")->where("cate_id='".$v['art_cate_id']."'")->find();
    		$arr[$k]["fen"]=$name['cate_name'];
    	}
    	$this->arr=$arr;
    	
    	
    	//旅游轮博图
    	$tu=M("picture")->where("id=22||(id=23)||(id=24)||(id=25)||(id=26)")->select();
    	
    	$this->tu1=$tu[0];
        $this->tu2=$tu[1];
    	$this->tu3=$tu[2];
    	$this->tu4=$tu[3];
    	$this->tu5=$tu[4];
    	
    	$this->display();
    	
    }
    
     //旅游分类页
    public function ytx_list(){
    	
    	$id=I("id");
    	$art=M("article");
    	$temp=$art->where("art_cate_id='".$id."' and type=1")->order("pai asc")->select();
    	$this->temp=$temp;
    	//所属
    	$cate=M("cate");
    	$name=$cate->where("cate_id='".$id."'")->find();
    	$this->name=$name['cate_name'];
    	$this->display();
    	
    	
    }

    // 产品详情
    public function ytx_cpxq(){
    	$id = I("id");
    	$this->ids=$id;
    	$tep=M("article")->where("art_id='".$id."'")->find();

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

        $tep['img'] = 'http://' . $_SERVER['HTTP_HOST'] . $tep['art_img_url'];

    	$this->te = $tep;
    	
    	//所属
    	$cate=M("cate");
    	$cate_name=$cate->where("cate_id='".$tep['art_cate_id']."'")->find();
    	$this->cate_name=$cate_name['cate_name'];
    	
    	$uid=$_SESSION['user']['id'];
    	$type=M("user")->where("id='".$uid."'")->find();
    	$this->type=$type['type'];
    	//规格
        $guige=M("guige")->where("cid='".$id."'")->select();
        $this->guige = $guige;
        $this->guige_num = empty($guige) ? 0 : 1;
    	$this->display();
    }
		
		
		public function kucun(){
					
					
					$str=I("str");//规格
				
					$where['id']=$str;
					$temp=M("guige")->where($where)->find();
					if($temp['num']<0 || $temp['num']==0){
						//库存不足
						$this->ajaxReturn(array('state'=>0,'mag'=>'库存不足,请重新选择!'));
		
					}else{
						//库存足
						$this->ajaxReturn(array('state'=>1,'mag'=>'库存不足,请重新选择!'));
					}
					
					
		}
		

     //费用说明
    public function ytx_cpxq_sysm(){
    	$id=I("id");
        $tep=M("article")->where("art_id='".$id."'")->find();
    	$this->te=$tep;
    	$this->display();
    }
     //行程
    public function ytx_cpxq_xc(){
    	$id=I("id");
    	$this->ids=$id;
    	$tep=M("article")->where("art_id='".$id."'")->find();
    	$this->te=$tep;
    	$this->display();
    }
    
    
     //评论
    public function ytx_cpxq_pl(){
    	$id=I("id");//产品id
    	$temp=M("ping")->where("art_id='".$id."'")->select();
//  	if(empty($temp)){
//  		$this->ids=$id;
//  		$this->display('Tourism/ytx_cpxq_pl');
//  	}else{
//  		$this->ids=$id;
//  		foreach($temp as $k=>$v){
//  			$user=M("user")->where("id='".$v['uid']."'")->find();
//  			$temp[$k]['img']=$user['img'];
//  			$temp[$k]['sename']=$user['sename'];
//  			$store=M("article")->where("art_id='".$v['art_id']."'")->find();
//  			$temp[$k]['art_name']=$store['product_title'];
//  		}
//  		//dump($temp);die;
//  		$this->temp=$temp;
//  		$this->display('Tourism/cpxq_pl');
//  	}
    	
    	
    	
    		$this->ids=$id;
    		foreach($temp as $k=>$v){
    			$user=M("user")->where("id='".$v['uid']."'")->find();
    			$temp[$k]['img']=$user['img'];
    			$temp[$k]['sename']=$user['sename'];
    			$store=M("article")->where("art_id='".$v['art_id']."'")->find();
    			$temp[$k]['art_name']=$store['product_title'];
    		}
    		//dump($temp);die;
    		$this->temp=$temp;
    		$this->display('Tourism/cpxq_pl');
    	
    	
    	
    	
    }
    
     //发表评论
    public function ytx_fbpl(){
    	$id=I("id");//产品id
    	$this->ids=$id;
    	$this->display();
    }
    
     //发表评论
    public function addcl(){
    	$data=I("post.");
    	$userid=$_SESSION['user']['id'];
    
    	$arr['fen']=$data['xing'];
    	$arr['text']=$data['text'];
    	$arr['art_id']=$data['art_id'];
    	$arr['uid']=$userid;
    	$arr['time']=date('Y-m-d',time());
    	$arr['state']=0;
    	$arr['ip']=$_SERVER['REMOTE_ADDR'];
    	if(M("ping")->add($arr)){
    		
    		$this->redirect('Tourism/ytx_cpxq_pl', array('id' => $data['art_id']));
    	 	
    	}
    	 
    }
    
     //提交信息
    public function ytx_tjxx(){
    	$id=I("id");
    	$this->ids=$id;
    	$str=I("str");
    	$this->str=$str;
			$temp=M("guige")->where("id='".$str."'")->find();
			$number=$temp['num']+1;
			$this->number=$number;
    	if (isset($_SERVER['HTTP_REFERER'])) {
    		$yuan=$_SERVER['HTTP_REFERER'];
            cookie('yuan',$yuan,3600); 
         } 
    	
    	$this->display();
    }
    
    
    
    public function dingzhi(){
    	$data=I("post.");
    	
    	$uid=$_SESSION['user']['id'];
    	$arr['uid']=$uid;
    	$arr['sex']=$data['sex'];
    	$arr['name']=$data['name'];
    	$arr['phone']=$data['phone'];
    	$arr['shengri']=$data['shengri'];
    	$arr['zhu_time']=$data['zhutime'];
    	$arr['num']=$data['shu'];
    	$arr['mudidi']=$data['mudizhi'];
    	$arr['jingdian']=$data['jingdian'];
    	$arr['jiudian']=$data['jidian'];
    	$arr['che']=$data['che'];
    	$arr['fuwu']=$data['fuwu'];
    	$arr['time']=date('Y-m-d H:i:s',time());
    	$res=M("siren")->add($arr);
    	if($res){
    		$this->redirect('Tourism/ytx');
    	}
    }
    
    
    
    
    
    public function sou(){
    	
    	$sou=I("sou");
    	$cate=M("cate")->where("cate_p_id=22")->getField('cate_id',true);

    	$where['product_title']=array('like','%'.$sou.'%');
    	$where['art_cate_id']=array('in',$cate);
    	$temp=M("article")->where($where)->select();
    	//dump($temp);die;
    	$this->temp=$temp;
    	$this->display();
    }

    //用户信息提交
    public function lvke(){
    	$data=I("post.");
//  	dump($data);
    	$uid=$_SESSION['user']['id'];
    	$user=M("user")->where("id='".$uid."'")->find();

    	//产品id
    	$storeid=$data['ids']; // 产品id;
    	//联系人
    	$lxuser=$data['lname']; // 联系人姓名
    	$lxphone=$data['lphone']; // 联系人电话

		$pay_sn=makePaySn($storeid); //订单编号
		$store=M("article")->where("art_id='".$storeid."'")->find();
		//dump($store);
		if($data['str']=='1'){
	 		//无规格
	 		//判断用户是否为会员
		   if($user['type']=='0'){//不是会员
			 	 $array['jiage']=$store['jiage'];
		 	
		    }else{ //会员价
		 	     $array['jiage']=$store['huiyuan'];
		    }
	 		
	 		
		}else{
	 		//有规格
	 		$guige=M("guige")->where("id='".$data['str']."'")->find();
	 		 $array['guige']=$guige['guige'];
	 		//判断用户是否为会员
		   if($user['type']=='0'){//不是会员
			 	 $array['jiage']=$guige['money'];;
		 	
		    }else{ //会员价
		 	     $array['jiage']=$guige['money_huiyuan'];;
		    }
            $array['gid'] = $guige['id'];
		}
		 
		$array['paysn']=$pay_sn;  //订单编号
		$array['store_id']=$store['art_id'];  //产品ID
		$array['name']=$store['product_title'];  //产品名称
		$array['img']=$store['art_img_url'];  //产品图片
		$array['time']=date('Y-m-d H:i:s',time());  //订单生成时间
		$array['uid']=$user['id'];    //userid
		$array['ster']='0';   //订单状态
		$array['lianxi']=$lxuser;   //联系人姓名
		$array['lianxiphoto']=$lxphone;//联系人电话
		$array['jin']=$store['jinjia'];//产品进价
		$array['type']='1';//产品进价
		 //dump($array);exit;
		$res=M("dingdan")->add($array); 
		
		if($res){
			  $i=0;
    	 	  $arr=array(
		         'user'=>$data['user'],
		         'photo'=>$data['photo'],
		         'zheng'=>$data['select'],
		         'zhengh'=>$data['tot'],
		         'dingid'=>$res
		    	);
		    	if(!empty($data['user']) && !empty($data['photo']) && !empty($data['select']) && !empty($data['tot']) ){
		    		M("youke")->add($arr);
		    		$i++;
		    	}
		    	$arr1=array(
		         'user'=>$data['user1'],
		         'photo'=>$data['photo1'],
		         'zheng'=>$data['select1'],
		         'zhengh'=>$data['tot1'],
		         'dingid'=>$res
		    	);
		    	if(!empty($data['user1']) && !empty($data['photo1']) && !empty($data['select1']) && !empty($data['tot1'])){
		    		M("youke")->add($arr1);
		    		$i++;
		    	}
		    	$arr2=array(
		         'user'=>$data['user2'],
		         'photo'=>$data['photo2'],
		         'zheng'=>$data['select2'],
		         'zhengh'=>$data['tot2'],
		         'dingid'=>$res
		    	);
		    	if(!empty($data['user2']) && !empty($data['photo2']) && !empty($data['select2']) && !empty($data['tot2'])){
		    		M("youke")->add($arr2);
		    		$i++;
		    	}
		    	$arr3=array(
		         'user'=>$data['user3'],
		         'photo'=>$data['photo3'],
		         'zheng'=>$data['select3'],
		         'zhengh'=>$data['tot3'],
		         'dingid'=>$res
		    	);
		    	if(!empty($data['user3']) && !empty($data['photo3']) && !empty($data['select3']) && !empty($data['tot3'])){
		    		M("youke")->add($arr3);
		    		$i++;
		    	}
		    	$arr4=array(
		         'user'=>$data['user4'],
		         'photo'=>$data['photo4'],
		         'zheng'=>$data['select4'],
		         'zhengh'=>$data['tot4'],
		         'dingid'=>$res
		    	);
		    	if(!empty($data['user4']) && !empty($data['photo4']) && !empty($data['select4']) && !empty($data['tot4'])){
		    		M("youke")->add($arr4);
		    		$i++;
		    	}
		    	$arr5=array(
		         'user'=>$data['user5'],
		         'photo'=>$data['photo5'],
		         'zheng'=>$data['select5'],
		         'zhengh'=>$data['tot5'],
		         'dingid'=>$res
		    	);
		    	if(!empty($data['user5']) && !empty($data['photo5']) && !empty($data['select5']) && !empty($data['tot5'])){
		    		M("youke")->add($arr5);
		    		$i++;
		    	}
		    	
		    	//修改订单总价
		    	$zong=$i*$array['jiage'];
		      $ko=M("dingdan")->where("id='".$res."'")->save(array('zong'=>$zong,'num'=>$i));
		      //修改产品库存
					if(!empty($data['str'])){
						$guige=M("guige")->where("id='".$data['str']."'")->find();
						$kucun_num=$guige['num']-$i;
						$rest=M("guige")->where("id='".$data['str']."'")->save(array('num'=>$kucun_num));
					}
		        
		        
		}
		
		
    	$this->redirect('Tourism/oderitem',array('id'=>$res));
    	 	  
		
		
    	
    	
    }
    
    
    
    public function oderitem(){
            
           $id=I("id");
		   $temp=M("dingdan")->where("id='".$id."'")->find();
		   $num=M("youke")->where("dingid='".$id."'")->select();
		   $tt=count($num);
		   $this->temp=$temp;
		   $this->tt=$tt;
		   $hh=$tt*$temp["jiage"];
		   $this->zong=$hh;
     	   $this->display();
     }
   
    
    
    
    
    
    
    
	
   
    
}