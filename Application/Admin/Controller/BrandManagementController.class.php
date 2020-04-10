<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17 0017$this->dia
 * Time: 下午 1:42
 */

namespace Admin\Controller;

class BrandManagementController extends CommController {
    /**
     * 旅游订单管理页面显示
     */
    public function index(){
        $Brand = M('dingdan');
//      $catep=M("cate")->where("cate_p_id=22")->getField('cate_id',true);
//      $t = array();
//      $t['lianxi'] = array('neq',null);
//      $cate=M("article")->where($t)->getField('art_id',true); //所有的旅游产品id集合

        $tt = array();
        $tt['cuxiao'] = ['eq', 0]; 
        //$tt['lianxi'] = array('neq',' '); 
        $tt['type'] = '1'; 
        
        $select1=I("select1");//状态筛选
        $this->select1 = $select1;
     	if(!empty($select1)){
     		if($select1==1){//待付款
                $tt['ster']=['eq', 0];
                $tt['tuikuan']=['eq', 0];
     		}
     		if($select1==2){ //待发货
                $tt['ster']=['eq', 1];
                $tt['tuikuan']=['eq', 0];
     		}
     		if($select1==3){//待收货
                $tt['ster']=['eq', 2];
                $tt['tuikuan']=['eq', 0];
     		}
     		if($select1==4){//已收货
                $tt['ster']=['eq', 3];
                $tt['tuikuan']=['eq', 0];
     		}
     		if($select1==5){//已完结
                $tt['ster']=['eq', 4];
                $tt['tuikuan']=['eq', 0];
     		}
     		if($select1==6){//退款中
                $tt['tuikuan']=['eq', 1];
     		}
     		if($select1==7){//已退款
                $tt['tuikuan']=['eq', 2];
     		}
     	}
     	$ming=I("ming");//下单人电话筛选
        $this->ming = $ming;
     	if(!empty($ming)){
     		$tt['lianxiphoto']=['like', '%' . $ming . '%'];
     	}
     	$lianxi=I("lianxi");//下单人姓名筛选
        $this->lianxi = $lianxi;
     	if(!empty($lianxi)){
     		$tt['lianxi']=['like', '%' . $lianxi . '%'];
     	}

     	$paysn=I("paysn");//订单筛选
        $this->paysn = $paysn;
     	if(!empty($paysn)){
     		$tt['paysn']=['like', '%' . $paysn . '%'];
     	}

        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt)->order("time desc") -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($brandList as $k=>$v){
        	$mober=M("user")->where("id='".$v['uid']."'")->find();
        	$brandList[$k]['huiyuan']=$mober['type'];
        	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
            $gong=M("gongying")->where("id='".$store['gong']."'")->find();
        	$brandList[$k]['gong']=$gong['name'];
        }
     
        $this -> assign('brand',$brandList);
       
        $this -> assign('page',$show);

        $this -> display();
    }
    
    
     public function cuxiao(){
    	
        $Brand = M('dingdan');
        $tt = array();
        $tt['cuxiao']='1';
        $select1=I("select1");//状态筛选
     	if(!empty($select1)){
     		if($select1==1){//待付款
     			
                $tt['ster']=0;
                $tt['tuikuan']=0;
     		}
     		if($select1==2){ //待发货
     			
                $tt['ster']=1;
                $tt['tuikuan']=0;
     		}
     		if($select1==3){//待收货
     			
                $tt['ster']=2;
                $tt['tuikuan']=0;
     		}
     		if($select1==4){//已收货
     			
                $tt['ster']=3;
                $tt['tuikuan']=0;
     		}
     		if($select1==5){//已完结
     			
                $tt['ster']=4;
                $tt['tuikuan']=0;
     		}
     		if($select1==6){//退款中
     			
               
                $tt['tuikuan']=1;
     		}
     		if($select1==7){//已退款
     			
                $tt['tuikuan']=2;
     		}
     		
     	}
     	
     	$paysn=I("paysn");//订单筛选
        $this->paysn = $paysn;
     	if(!empty($paysn)){
     		$tt['paysn']=$paysn;
     	}
		
		
        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt)->order("time desc") -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($brandList as $k=>$v){
        	$mober=M("user")->where("id='".$v['uid']."'")->find();
        	$brandList[$k]['huiyuan']=$mober['type'];
        	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
        	$cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
        	
            $gong=M("gongying")->where("id='".$store['gong']."'")->find();
        	$brandList[$k]['gong']=$gong['name'];
        	$brandList[$k]['cate']=$cate['cate_p_id'];
        	if(empty($v['lianxi'])){
        		$lianxi=M("address")->where("id='".$v['addressid']."'")->find();
        		$brandList[$k]['lianxi']=$lianxi['name'];
        		$brandList[$k]['lianxiphoto']=$lianxi['dian'];
        	}
        	
        	
        	$brandList[$k]['fuwu']=$store['fu_id'];
        	
        	
            
        	//是否分配服务商
        	$pai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
        	if(!empty($pai)){
        		$brandList[$k]['paidan']=$pai['id'];
        	}else{
        		$brandList[$k]['paidan']=null;
        	}
        	
        }
       
     
        $this -> assign('brand',$brandList);
       
        $this -> assign('page',$show);

        $this -> display();
    
    	
    }


   public function qiche_index(){
        $Brand = M('dingdan');
        // $catep=M("cate")->where("cate_p_id=18")->getField('cate_id',true);
        // $t = array();
        // $t['art_cate_id'] = array('in',$catep);
        // $cate=M("article")->where($t)->getField('art_id',true); //所有的旅游产品id集合

        $tt = array();
        // $tt['store_id'] = array('in',$cate);
        $tt['type'] = 2;
		$tt['cuxiao'] = 0; 
        $select1=I("select1");
        $this->select1 = $select1;
     	if(!empty($select1)){
     		if($select1==1){//代付款
     			
                $tt['ster']=0;
                $tt['tuikuan']=0;
     		}
     		if($select1==2){ //待发货
     			
                $tt['ster']=1;
                $tt['tuikuan']=0;
     		}
     		if($select1==3){//待收货
     			
                $tt['ster']=2;
                $tt['tuikuan']=0;
     		}
     		if($select1==4){//已收货
     			
                $tt['ster']=3;
                $tt['tuikuan']=0;
     		}
     		if($select1==5){//已完结
     			
                $tt['ster']=4;
                $tt['tuikuan']=0;
     		}
     		if($select1==6){//退款中
     			
                $tt['tuikuan']=1;
     		}
     		if($select1==7){//已退款
     			
                $tt['tuikuan']=2;
     		}
     		
     	}
     	$ming=I("ming");//下单人电话筛选
        $this->ming = $ming;
     	if(!empty($ming)){
     		 $ids=M("address")->where("dian='".$ming."'")->getField('id',true);;
     		 $tt['addressid']=array('in',$ids);
     	}

        $lianxi=I("lianxi");//下单人姓名筛选
        $this->lianxi = $lianxi;
     	if(!empty($lianxi)){
     		 $ids=M("address")->where("name='".$lianxi."'")->getField('id',true);;
     		 $tt['addressid']=array('in',$ids);
     	}
     	$paysn=I("paysn");//订单筛选
        $this->paysn = $paysn;
     	if(!empty($paysn)){
     		$tt['paysn']=$paysn;
     	}
        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt) ->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($brandList as $k=>$v){
         	$name=M("address")->where("id='".$v['addressid']."'")->find();
         	$brandList[$k]['dianhua']=$name['dian'];
         	$brandList[$k]['username']=$name['name']; 
         	$mober=M("user")->where("id='".$v['uid']."'")->find();
        	
        	$brandList[$k]['huiyuan']=$mober['type'];
        	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
        	
        	$brandList[$k]['fuwu']=$store['fu_id'];
        	
        	
            $gong=M("gongying")->where("id='".$store['gong']."'")->find();
        	$brandList[$k]['gong']=$gong['name'];
        	//是否分配服务商
        	$pai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
        	if(!empty($pai)){
        		$brandList[$k]['paidan']=$pai['id'];
        	}else{
        		$brandList[$k]['paidan']=null;
        	}
         }
         
         
        $this -> assign('brand',$brandList);
         // dump($brandList);
        $this -> assign('page',$show);

        $this -> display();
    
   }

   public function teiwa_index(){
        $Brand = M('dingdan');
        // $catep=M("cate")->where("cate_p_id=102")->getField('cate_id',true);
        // $t = array();
        // $t['art_cate_id'] = array('in',$catep);
        // $cate=M("article")->where($t)->getField('art_id',true); //所有的旅游产品id集合
        $tt = array();
        // $tt['store_id'] = array('in',$cate);
        $tt['type'] = 3;
        $tt['cuxiao'] = 0; 
        $select1=I("select1");
        $this->select1 = $select1;
        if(!empty($select1)){
            if($select1==1){//代付款
                
                $tt['ster']=0;
                $tt['tuikuan']=0;
            }
            if($select1==2){ //待发货
                
                $tt['ster']=1;
                $tt['tuikuan']=0;
            }
            if($select1==3){//待收货
                
                $tt['ster']=2;
                $tt['tuikuan']=0;
            }
            if($select1==4){//已收货
                
                $tt['ster']=3;
                $tt['tuikuan']=0;
            }
            if($select1==5){//已完结
                
                $tt['ster']=4;
                $tt['tuikuan']=0;
            }
            if($select1==6){//退款中
                
                $tt['tuikuan']=1;
            }
            if($select1==7){//已退款
                
                $tt['tuikuan']=2;
            }
            
        }
        $ming=I("ming");//下单人电话筛选
        $this->ming = $ming;
        if(!empty($ming)){
             $ids=M("address")->where("dian='".$ming."'")->getField('id',true);;
             $tt['addressid']=array('in',$ids);
        }

        $lianxi=I("lianxi");//下单人姓名筛选
        $this->lianxi = $lianxi;
        if(!empty($lianxi)){
             $ids=M("address")->where("name='".$lianxi."'")->getField('id',true);;
             $tt['addressid']=array('in',$ids);
        }
        $paysn=I("paysn");//订单筛选
        $this->paysn = $paysn;
        if(!empty($paysn)){
            $tt['paysn']=$paysn;
        }
        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt) ->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($brandList as $k=>$v){
            $name=M("address")->where("id='".$v['addressid']."'")->find();
            $brandList[$k]['dianhua']=$name['dian'];
            $brandList[$k]['username']=$name['name']; 
            $mober=M("user")->where("id='".$v['uid']."'")->find();
            
            $brandList[$k]['huiyuan']=$mober['type'];
            $store=M("article")->where("art_id='".$v['store_id']."'")->find();
            
            $brandList[$k]['fuwu']=$store['fu_id'];
            
            
            $gong=M("gongying")->where("id='".$store['gong']."'")->find();
            $brandList[$k]['gong']=$gong['name'];
            //是否分配服务商
            $pai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
            if(!empty($pai)){
                $brandList[$k]['paidan']=$pai['id'];
            }else{
                $brandList[$k]['paidan']=null;
            }
         }
         
         
        $this -> assign('brand',$brandList);
         // dump($brandList);
        $this -> assign('page',$show);

        $this -> display();
    
   }  
   
   public function paidan(){
   	$id=I("id");
   	$paidan=M("fuwu_paidan")->where("id='".$id."'")->find();
   	$this->paidan=$paidan;
   	$fuwu=M("fuwu")->where("id='".$paidan['fu_id']."'")->find();
   	$this->fuwu=$fuwu;
   	$this->display();
   }

    public function fuwu(){
    	$id=I("id");
    	$temp=M("dingdan")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$fuwu=M("fuwu")->where("cate_id=18")->select();
    	$this->fuwu=$fuwu;
    	$this->display();
    }
    
    public function teiwafuwu(){
        $id=I("id");
        $temp=M("dingdan")->where("id='".$id."'")->find();
        $this->temp=$temp;
        $fuwu=M("fuwu")->where("cate_id=102")->select();
        $this->fuwu=$fuwu;
        $this->display();
    }

    public function qitafuwu(){
    	$id=I("id");
    	$temp=M("dingdan")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$fuwu=M("fuwu")->where("cate_id=85")->select();
    	$this->fuwu=$fuwu;
    	$this->display();
    }
    
     //分配服务商 ,并给服务商和供应商发短信
    public function fuwuadd(){
    	$data=I("post.");
        if (!isset($data['paysn']) || empty($data['paysn'])) {
            $this->error('请输入订单编号！');die();
        }
        if (!isset($data['fu_id']) || empty($data['fu_id'])) {
            $this->error('请选择服务商！');die();
        }
        if (!isset($data['fuwu_time']) || empty($data['fuwu_time'])) {
            $this->error('请选择服务时间！');die();
        }
    	$arr['dingid']=$data['dingid'];
    	$arr['paysn']=$data['paysn'];
    	$arr['fu_id']=$data['fu_id'];
    	$arr['content']=$data['content'];
    	$arr['beizhu']=$data['beizhu'];
    	$arr['fuwu_time']=$data['fuwu_time'];
    	$arr['time']=date('Y-m-d H:i:s',time());
    	$res=M("fuwu_paidan")->add($arr);
    	// 给服务商发
    	require './ThinkPHP/Library/Org/Util/Ucpaas.class.php';
    	// 2.找到服务商的手机号码
        $fuwu=M("fuwu")->where("id='".$data['fu_id']."'")->find();
        $photo2=$fuwu['photo'];
        $shangdizhi=$fuwu['address'];
        // 3.发送短信
        $order_sn=$data['paysn'];
        $options['accountsid']='a2769b5baced349ab6c2833ef00a7f72';
        $options['token']='6b12b3514e6cb17794d0341c7b169ca2';
        $ucpass =  new \Ucpaas($options);  
        // 短信验证码（模板短信）
        $appId = "0441da62e64f47f6ae93d15ae54927e3";

 		$arr2 = $ucpass->SendSms($appId,"387483",$param=null, $photo2, '');
 		$arr_n2 = json_decode(json_encode($arr2),true);

        // 给用户发
        // 2.找到用户手机号码
        $dingdan=M("dingdan")->where("id='".$data['dingid']."'")->find();
        $gong=M("user")->where("id='".$dingdan['uid']."'")->find();
        // 3.发送短信
        // 短信验证码（模板短信）
        $arr3 = $ucpass->SendSms($appId, "387490", "{$shangdizhi},{$photo2}", $gong['photo'], '');
		$arr_n3 = json_decode(json_encode($arr3),true);

    	$this->success('派单成功',U('BrandManagement/qiche_index'));
    }


    /**
     * 添加品牌页面显示
     */
    public function add(){
        $this -> display();
    }

    /**
     * 品牌编辑页面显示
     */
    public function edit(){
        if (IS_GET){
            $id = I('get.id');

            $Brand = M('brand');

            $brand = $Brand -> where('id = '.$id) -> find();

            $this -> assign('brand',$brand);

            $this -> display();
        }
    }

    /**
     * 添加品牌逻辑处理
     */
    public function addHandle(){
        if(IS_POST){
            if (!empty($_FILES['picture'])){
                $config = array(
                    'rootPath'  => './Uploads/brand/',
                    'savePath'  => '',
                    'maxSize'   => 19145728,
                    'exts'      => array('jpg', 'gif', 'png', 'jpeg')
                );

                $upload = new \Think\Upload($config);

                $picture = $upload -> uploadOne($_FILES['picture']);

                $pictureUrl =  '/Uploads/brand/'.$picture['savepath'].$picture['savename'];

            }

            $editData = array(
                'picture_title' => I('post.picture_title'),
                'picture_url'   => $pictureUrl,
                'add_time'      => time()
            );

            $Brand = M('brand');

            $res = $Brand -> data($editData) -> add();

            if ($res){
                $this -> success('添加成功',U('BrandManagement/index'));
            }else{
                $this -> error('添加失败',U('BrandManagement/index'));
            }
        }
    }

    /**
     * 品牌编辑逻辑处理
     */
    public function editHandle(){
        if (IS_POST){
            $id = I('post.id');

            if (!empty($_FILES['picture'])){
                $config = array(
                    'rootPath'  => './Uploads/brand/',
                    'savePath'  => '',
                    'maxSize'   => 19145728,
                    'exts'      => array('jpg', 'gif', 'png', 'jpeg')
                );

                $upload = new \Think\Upload($config);

                $picture = $upload -> uploadOne($_FILES['picture']);

                $pictureUrl =  '/Uploads/brand/'.$picture['savepath'].$picture['savename'];

            }

            $editData = array(
                'picture_url'   => $pictureUrl,
                'add_time'      => time()
            );

            $Brand = M('brand');

            $res = $Brand -> where('id = '.$id) -> field('picture_url,add_time') -> filter('strip_tags') -> save($editData);

            if ($res){
                $this -> success('编辑成功',U('BrandManagement/index'));
            }else{
                $this -> error('编辑失败',U('BrandManagement/index'));
            }
        }
    }

    /**
     * 删除品牌逻辑处理
     */
    public function delete(){
        if(IS_GET){
            $id = I('get.id');

            $Brand = M('brand');

            $res = $Brand -> where('id = '.$id) -> delete();

            if ($res){
                $this -> success('删除成功',U('BrandManagement/index'));
            }else{
                $this -> error('删除失败',U('BrandManagement/index'));
            }
        }
    }
    
    //保险订单
     public function baoxian_index(){
        $Brand = M('dingdan');
        $tt = array();
        $tt['type'] = 4;
		$tt['cuxiao'] = 0;
        $select1=I("select1");
        $this->select1 = $select1;
     	if(!empty($select1)){
     		if($select1==1){//代付款
     			
                $tt['ster']=0;
                $tt['tuikuan']=0;
     		}
     		if($select1==2){ //待发货
     			
                $tt['ster']=1;
                $tt['tuikuan']=0;
     		}
     		if($select1==3){//待收货
     			
                $tt['ster']=2;
                $tt['tuikuan']=0;
     		}
     		if($select1==4){//已收货
     			
                $tt['ster']=3;
                $tt['tuikuan']=0;
     		}
     		if($select1==5){//已完结
     			
                $tt['ster']=4;
                $tt['tuikuan']=0;
     		}
     		if($select1==6){//退款中
     			
                $tt['tuikuan']=1;
     		}
     		if($select1==7){//已退款
     			
                $tt['tuikuan']=2;
     		}
     		
     	}
     	$ming=I("ming");//下单人电话筛选
        $this->ming = $ming;
     	if(!empty($ming)){
     		 $ids=M("address")->where("dian='".$ming."'")->getField('id',true);;
     		 $tt['addressid']=array('in',$ids);
     	}

        $lianxi=I("lianxi");//下单人姓名筛选
        $this->lianxi = $lianxi;
     	if(!empty($lianxi)){
     		 $ids=M("address")->where("name='".$lianxi."'")->getField('id',true);;
     		 $tt['addressid']=array('in',$ids);
     	}
     	$paysn=I("paysn");//订单筛选
        $this->paysn = $paysn;
     	if(!empty($paysn)){
     		$tt['paysn']=$paysn;
     	}
        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt) ->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($brandList as $k=>$v){
         	$name=M("address")->where("id='".$v['addressid']."'")->find();
         	$brandList[$k]['dianhua']=$name['dian'];
         	$brandList[$k]['username']=$name['name']; 
         	$mober=M("user")->where("id='".$v['uid']."'")->find();
        	
        	$brandList[$k]['huiyuan']=$mober['type'];
        	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
        	
        	$brandList[$k]['fuwu']=$store['fu_id'];
        	
        	
            $gong=M("gongying")->where("id='".$store['gong']."'")->find();
        	$brandList[$k]['gong']=$gong['name'];
        	//是否分配服务商
        	$pai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
        	if(!empty($pai)){
        		$brandList[$k]['paidan']=$pai['id'];
        	}else{
        		$brandList[$k]['paidan']=null;
        	}
         }
         
         
        $this -> assign('brand',$brandList);
         // dump($brandList);
        $this -> assign('page',$show);

        $this -> display();
    
   
     	
     }
     
     //编辑订单  上传图片
     
     public function biaoxianedit(){
     	$id=I("id");
     	$this->ids=$id;
     	
     	$this->display();
     }
     //编辑处理
     public function biaoxian_editHandle(){
     	$data=I("post.");
        $res=M("dingdan")->where("id='".$data['id']."'")->save(array('biaoxian_img'=>$data['content']));
     	if($res){
     	   $this -> success('编辑成功',U('BrandManagement/baoxian_index'));
     	}else{
     		 $this -> success('编辑失败',U('BrandManagement/baoxian_index'));
     	}
     }
    
    
    
    //查看
    public function biaoxian_kan(){
        $id=I("id");
    	$temp=M("dingdan")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$name=M("user")->where("id='".$temp['uid']."'")->find();
    	$this->name=$name;
    	  $this->display();
    }
    
    
    //活动订单
    public function huodong_index(){
    	
     	$catep=M("huodong")->where()->getField('id',true);
     	if(!empty($catep)){
     		$ming=I("ming");
     		$select1=I("select1");
     		if(empty($select1) && empty($ming)){
     			$tt = array();
	            $tt['h_id'] = array('in',$catep);
     		}elseif(!empty($select1)){
     			  if($select1==1){//代付款
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=0;
                $tt['tuikuan']=0;
     		}
     		if($select1==2){ //未发货
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=1;
                $tt['tuikuan']=0;
     		}
     		if($select1==3){//未发货 / 待退款
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=1;
                $tt['tuikuan']=1;
     		}
     		if($select1==4){//未发货 / 已退款
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=1;
                $tt['tuikuan']=2;
     		}
     		if($select1==5){//已发货
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=2;
                $tt['tuikuan']=0;
     		}
     		if($select1==6){//已发货/ 待退款
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=2;
                $tt['tuikuan']=1;
     		}
     		if($select1==7){//已发货/ 已退款 
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=2;
                $tt['tuikuan']=2;
     		}
     		if($select1==8){//已完成
     			$tt = array();
                $tt['h_id'] = array('in',$catep);
                $tt['ster']=3;
              
     		}
     		}elseif(!empty($ming)){
     			 $ids=M("user")->where("username='".$ming."'")->find();
     		     $tt = array();
                 $tt['h_id'] = array('in',$catep);
     		     $tt['uid']=$ids['id'];
     		}
     		
	        
	        $count = M("dingdan") -> where($tt) -> count();   
	        
	        $Page = new \Think\Page($count,20);
	        $show = $Page -> show();
	        //第一页
	        $products = M("dingdan")-> where($tt)->order("time desc") -> limit($Page->firstRow.','.$Page->listRows) -> select();
	       
	        foreach($products as $k=>$v){
	        	$tt=M("user")->where("id= '".$v['uid']."'")->find();
	        	$products[$k]['ren']=$tt['username']; 
	        	$products[$k]['username']=$tt['sename']; 
	        }
	        $this->assign('list',$products);
	
	        $this->assign('page',$show);
     	}
		
     	$this->display();
     	
     
    	
    }
    
    
    
    public function shang_qing(){
    	$id=I("id");
    	$dan=M("dingdan")->where("id={$id}")->find();
    	if($dan['ster']=='0' and $dan['tuikuan']=='0'){
    		$zhuangtai='待支付';
    	}else if($dan['ster']=='1' and $dan['tuikuan']=='0'){
    		$zhuangtai='待发货';
    	}else if($dan['ster']=='2' and $dan['tuikuan']=='0'){
    		$zhuangtai='待收货';
    	}else if($dan['ster']=='3' and $dan['tuikuan']=='0'){
    		$zhuangtai='待评价';
    	}else if($dan['ster']=='4' and $dan['tuikuan']=='0'){
    		$zhuangtai='已完成';
    	}else if($dan['tuikuan']=='1'){
    		$zhuangtai='退款中';
    	}else if( $dan['tuikuan']=='2'){
    		$zhuangtai='已退款';
    	}
    	$this->dan=$dan;
    	$this->zhuangtai=$zhuangtai;
    	
        $user=M("user")->where("id='".$dan['uid']."'")->find();
    	$store=M("article")->where("art_id='".$dan['store_id']."'")->find();
    	$gong=M("gongying")->where("id='".$store['gong']."'")->find();
    	$this->user=$user;
    	$this->store=$store;
    	$this->gong=$gong;
    	//游客信息
    	$youke=M("youke")->where("dingid={$id}")->select();
    	foreach($youke as $k=>$v){
    		 if($v['zheng']==1){
    		 	$youke[$k]['zheng']='身份证';
    		 }else if($v['zheng']==2){
    		 	$youke[$k]['zheng']='护照';
    		 }else if($v['zheng']==3){
    		 	$youke[$k]['zheng']='军官证';
    		 }else if($v['zheng']==4){
    		 	$youke[$k]['zheng']='回乡证';
    		 }else if($v['zheng']==5){
    		 	$youke[$k]['zheng']='台胞证';
    		 }else if($v['zheng']==6){
    		 	$youke[$k]['zheng']='港澳通行证';
    		 }else if($v['zheng']==7){
    		 	$youke[$k]['zheng']='国际海员证';
    		 }else if($v['zheng']==8){
    		 	$youke[$k]['zheng']='大陆居民往来台湾通行证';
    		 }else if($v['zheng']==9){
    		 	$youke[$k]['zheng']='外国人永久居留身份证';
    		 }else if($v['zheng']==10){
    		 	$youke[$k]['zheng']='其他';
    		 }
    		 
    		 
    		
    	}
    	$this->youke=$youke;
    	$this->display();
    }
    
    
    public function che_xiang(){
    	
    	$id=I("id");
    	$dan=M("dingdan")->where("id={$id}")->find();
    	if($dan['ster']=='0' and $dan['tuikuan']=='0'){
    		$zhuangtai='待支付';
    	}else if($dan['ster']=='1' and $dan['tuikuan']=='0'){
    		$zhuangtai='待发货';
    	}else if($dan['ster']=='2' and $dan['tuikuan']=='0'){
    		$zhuangtai='待收货';
    	}else if($dan['ster']=='3' and $dan['tuikuan']=='0'){
    		$zhuangtai='待评价';
    	}else if($dan['ster']=='4' and $dan['tuikuan']=='0'){
    		$zhuangtai='已完成';
    	}else if($dan['tuikuan']=='1'){
    		$zhuangtai='退款中';
    	}else if( $dan['tuikuan']=='2'){
    		$zhuangtai='已退款';
    	}
    	$this->dan=$dan;
    	$this->zhuangtai=$zhuangtai;
    	$address=M("address")->where("id='".$dan['addressid']."'")->find();
    	$this->address=$address;
        $user=M("user")->where("id='".$dan['uid']."'")->find();
    	$store=M("article")->where("art_id='".$dan['store_id']."'")->find();
    	$gong=M("gongying")->where("id='".$store['gong']."'")->find();
    	$this->user=$user;
    	$this->store=$store;
    	$this->gong=$gong;
    	$this->display();
    
    }

    public function teiwa_xiang(){
        
        $id=I("id");
        $dan=M("dingdan")->where("id={$id}")->find();
        if($dan['ster']=='0' and $dan['tuikuan']=='0'){
            $zhuangtai='待支付';
        }else if($dan['ster']=='1' and $dan['tuikuan']=='0'){
            $zhuangtai='待发货';
        }else if($dan['ster']=='2' and $dan['tuikuan']=='0'){
            $zhuangtai='待收货';
        }else if($dan['ster']=='3' and $dan['tuikuan']=='0'){
            $zhuangtai='待评价';
        }else if($dan['ster']=='4' and $dan['tuikuan']=='0'){
            $zhuangtai='已完成';
        }else if($dan['tuikuan']=='1'){
            $zhuangtai='退款中';
        }else if( $dan['tuikuan']=='2'){
            $zhuangtai='已退款';
        }
        $this->dan=$dan;
        $this->zhuangtai=$zhuangtai;
        $address=M("address")->where("id='".$dan['addressid']."'")->find();
        $this->address=$address;
        $user=M("user")->where("id='".$dan['uid']."'")->find();
        $store=M("article")->where("art_id='".$dan['store_id']."'")->find();
        $gong=M("gongying")->where("id='".$store['gong']."'")->find();
        $this->user=$user;
        $this->store=$store;
        $this->gong=$gong;
        $this->display();
    
    }
    
    
}