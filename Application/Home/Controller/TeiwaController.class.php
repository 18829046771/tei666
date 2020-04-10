<?php
namespace Home\Controller;
use Think\Controller;

class TeiwaController extends PublicController {
	public function _initialize(){

        $user=$_SESSION['user'];
		  if(empty($user)){
		  	$this->weixindeng();
		}
		
		$hezuo=$this->hezuo();
		$this->hz=$hezuo;
    }
    
     //车管家
    public function cgj(){
    	 //车轮博图
    	$tu=M("picture")->where("id=55||(id=56)||(id=57)||(id=58)||(id=59)")->select();
    	$this->tu1=$tu[0];
    	$this->tu2=$tu[1];
    	$this->tu3=$tu[2];
    	$this->tu4=$tu[3];
    	$this->tu5=$tu[4];
    	
    	$che=M("cate")->where("cate_p_id=102")->select();
      
        //下面 
        foreach($che as $k=>$v){
        	 $tt=M("article")->where(array('art_cate_id'=>$v['cate_id'],'type'=>1,'jiage'=>array('neq','')))->order("pai asc")->limit("2")->select();
             $che[$k]['store']=$tt;
        } 
        $this->che=$che;
    	$this->display();
       
    }

    //产品分类页   
    public function czdq(){
    	$id=I("id");
    	$cate=M("cate")->where("cate_id='".$id."'")->find();
    	$this->cate=$cate;
    	$temp=M("article")->where("art_cate_id='".$id."' and type=1")->order("pai asc")->select();
    	$this->temp=$temp;
    	$this->display();
    }
    
    // 产品详情页
    public function cpxq(){
    	$id=I("id");
    	$temp=M("article")->where("art_id='".$id."'")->find();
    	// 判断商品是否为 限时购或者整点秒杀
    	$chang_store = I("chang_store");
        $gmap['cid'] = $id;
    	if(isset($chang_store) && !empty($chang_store)){
    		$yingxiao_store = M("yingxiao_store")->where("id='".$chang_store."'")->find();
            $yingxiao = M('yingxiao')->where(['id' => $yingxiao_store['chang_id']])->find();
    		$endtime = $yingxiao['type'] == 1 ? strtotime($yingxiao['endtime']) : null; // 营销结束时间
    		$this->endtime = $endtime;
    		$this->chang_store = $chang_store;
            $yingxiao_store['jiage'] = M('guige')->where($gmap)->order('money_ying asc')->getField('money_ying'); // 促销价
            $yingxiao_store['num'] = M('guige')->where($gmap)->order('money_ying asc')->getField('num');
    	}
        $this->yingxiao_store = $yingxiao_store;

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
        $temp['img'] = 'http://' . $_SERVER['HTTP_HOST'] . $temp['art_img_url'];
        
    	$this->temp=$temp;

        // 规格
        $guige=M("guige")->where($gmap)->order('money asc')->select();
        $this->guige=$guige;
        if(empty($guige)){
        	$this->guige_num='0';
        }else{
        	$this->guige_num='1';
        }
        $uid=$_SESSION['user']['id'];
        
        $user=M("user")->where("id='".$uid."'")->find();
        $this->type=$user['type'];
      
    	$this->display();
    }

    //搜索结果页
    public function sou(){
    	$sou=I("sou");
    	$cate=M("cate")->where("cate_p_id=102")->getField('cate_id',true);

    	$where['product_title']=array('like','%'.$sou.'%');
    	$where['art_cate_id']=array('in',$cate);
    	$temp=M("article")->where($where)->select();
    
    	$this->temp=$temp;
    	$this->display();
    }
    //使用说明
    public function cpxq_sysm(){
    	$id=I("id");
    	$temp=M("article")->where("art_id='".$id."'")->find();
    	$this->temp=$temp;
    	
    	$this->display();
    }
    
    //资质
    public function cpxq_zz(){
    	
    	$id=I("id");
    	$temp=M("article")->where("art_id='".$id."'")->find();
    	$this->temp=$temp;
    	$this->display();
    }
    //评论
    public function cpxq_pl(){
    	
//     	$id=I("id");//产品id
//     	$temp=M("ping")->where("art_id='".$id."'")->select();
// 		$this->ids=$id;
// 		foreach($temp as $k=>$v){
// 			$user=M("user")->where("id='".$v['uid']."'")->find();
// 			$temp[$k]['img']=$user['img'];
// 			$temp[$k]['sename']=$user['sename'];
// 			$store=M("article")->where("art_id='".$v['art_id']."'")->find();
// 			$temp[$k]['art_name']=$store['product_title'];
// 		}
// 		
// 		$this->temp=$temp;
// 		$this->display('Teiwa/cpxq_ply');
		$id=I("id");//产品id
		$temp=M("ping")->where("art_id='".$id."'")->select();
		if(empty($temp)){
			$this->ids=$id;
			$this->display('Teiwa/cpxq_pl');
		}else{
			$this->ids=$id;
			foreach($temp as $k=>$v){
				$user=M("user")->where("id='".$v['uid']."'")->find();
				$temp[$k]['img']=$user['img'];
				$temp[$k]['sename']=$user['sename'];
				$store=M("article")->where("art_id='".$v['art_id']."'")->find();
				$temp[$k]['art_name']=$store['product_title'];
			}
			
			$this->temp=$temp;
			$this->display('Teiwa/cpxq_ply');
		}
		
		
    }
    
     //发布评论 
    public function fbpl(){
    	$id=I("id");
    	$dingid=I("dingid");
    	$this->dingid=$dingid;
    	$this->ids=$id;
    	$this->display();
    }
    
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
    	$dingid=I("dingid");
    	
    	if(M("ping")->add($arr)){
    		
    		 //结束订单
    		$jieshu=M("dingdan")->where("id='".$dingid."'")->save(array('ster'=>4));
    		//判断商品分类
    		$store=M("article")->where("art_id='".$data['art_id']."'")->find();
    		$cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
    		if($cate['cate_p_id']=='18'){ 
    			//车管家
    			$this->redirect('Teiwa/cpxq_pl', array('id' => $data['art_id']));
    		}else if($cate['cate_p_id']=='22'){
    			//旅游
    			$this->redirect('Tourism/cpxq_pl', array('id' => $data['art_id']));
    		}else if($cate['cate_p_id']=='85'){
    			//其他
    			$this->redirect('Qita/qi_cpxq_pl', array('id' => $data['art_id']));
    		}
    		
    	 	
    	}
   }
   
    //生成订单
    public function odersheng(){
    	
    	if (isset($_SERVER['HTTP_REFERER'])) {
    		$yuan=$_SERVER['HTTP_REFERER'];
            cookie('yuan',$yuan,3600); 
         } 
    	
    	
       	$id=I("id");//产品 id
       	$store=M("article")->where("art_id='".$id."'")->find();
        $gid = I("str/d");//规格id
       	$guige = M("guige")->where(['id' => $gid])->find();
       
       	$num=I("num");//数量
   	
        //判断用户是否为会员
       	$uid=$_SESSION['user']['id'];
       	$user=M("user")->where("id='".$uid."'")->find();
        $xiantime=date('Y-m-d H:i:s',time());
        //判断是否促销
        $chang_store=I("chang_store");
        if(!empty($chang_store)){
        	 $xing['id']=$chang_store;
        	 $yingxiao_store=M("yingxiao_store")->where($xing)->find();
        }
    
       	if(!empty($yingxiao_store)){
             $array['jiage'] = $guige['money_ying'];
       	}else{
       	 	  //判断是否选取规格
    	   	if($gid == 1){  
    	 	    //没有规格
    	 	    if($user['type']=='0'){  //不是会员 
                    $array['jiage']=$store['jiage'];
    	        }else{ //会员
                    $array['jiage']=$store['huiyuan'];
    	    	}   
    	   	}else{
    	   	 	//有规格
    	   	 	 $array['guige']=$guige['guige'];
    	 	    if($user['type']=='0'){  //不是会员 
    		     $array['jiage']=$guige['money'];
    	        }else{ //会员
    		     $array['jiage']=$guige['money_huiyuan'];
    	    	}
                $array['gid'] = $guige['id'];
    	   	}
       	}

       	$paysn=makePaySn($id);
        $zong=($num*$array['jiage']) + $store['youdi']; //总价
        $array['store_id']=$id;
        $array['paysn']=$paysn;
        $array['store_id']=$store['art_id'];  //产品ID
        $array['name']=$store['product_title'];  //产品名称
    	$array['img']=$store['art_img_url'];  //产品图片
    	$array['time']=date('Y-m-d H:i:s',time());  //订单生成时间
    	$array['uid'] = $user['id'];    //userid
    	$array['ster'] = '0';   //订单状态
    	$array['zong'] = $zong;
    	$array['type'] = '3';
        $array['num'] = $num;
        $array['jin'] = $gid == 1 ? $store['jinjia'] : $guige['money_jin'];
        $array['cuxiao'] = !empty($yingxiao_store) ? 1 : 0; // 1营销，0非营销
        $res=M("dingdan")->add($array); 
        // 修改库存
        $kuncun = $guige['num'] - $num;
        M("guige")->where(['id' => $gid])->save(['num' => $kuncun]);
    	if(!empty($yingxiao_store)){
    		$ku = $yingxiao_store['num'] - $num;
    		$yingid = $yingxiao_store['id'];
    		$ku_xiu = M("yingxiao_store")->where("id='".$yingid."'")->save(array('num'=>$ku));
    	}else{
    		$ku = $store['num'] - $num;
    		$ku_xiu = M("article")->where("art_id='".$id."'")->save(array('num'=>$ku));
    	}
    	if($res){
    		$this->redirect('Teiwa/oderitem', array('did' => $res));
    	}
   	
   }
   
   
   public function oderitem(){
   
   	$did=I('did');
    $temp=M("dingdan")->where("id='".$did."'")->find();
    $this->temp=$temp;
    $sid=$temp['store_id'];
   
    $store=M("article")->where("art_id='".$sid."'")->find();
    $this->store=$store;

    //收货地址
    $uid=$_SESSION['user']['id'];
    $address=M("address")->where("uid='".$uid."' and mo=1")->find();
    $this->address=$address;
    $addressid=$address['id'];
    //修改订单默认地址
     $save=M("dingdan")->where("id='".$did."'")->save(array('addressid'=>$addressid));

   	$this->display();
   }
    
    
    

	
    
    
   
    
}