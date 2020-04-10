<?php
namespace Admin\Controller;
use Think\Controller;
class ProductManagementController extends CommController {

    /* 路游文章列表
     *
     */
    public function index(){
    	$Article = M('article');
        $where['cate_p_id']=22;
		$catep=M("cate")->where($where)->getField('cate_id',true);
		$cate=M("cate")->where($where)->select();
		$this->cate=$cate;
		$select1=I("select1");
    $this->select1=$select1;
		
		$select2=I("select2");
    $this->select2 = $select2;
        $ming=I("ming");
        $this->ming = $ming;
		$tt = array();
		if(empty($select1) && empty($ming)){
			  $tt['art_cate_id'] = array('in',$catep);
		}else if(!empty($select1)){
			$tt['art_cate_id'] = $select1;
		}elseif(!empty($ming)){
			$tt['product_title']=array('like','%'.$ming.'%');
		}
		if(!empty($select2)){
			  if($select2=='2'){
			  	 $tt['type'] = 0;
			  }else{
			  	 $tt['type'] = 1;
			  }
			
		}
          
		 
		$count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        
        $products = $Article -> where($tt) -> order('art_date desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
        	$tt=M("cate")->where("cate_id= '".$v['art_cate_id']."'")->find();
        	$products[$k]['fen']=$tt['cate_name']; 
        	 $gong=M("gongying")->where("id='".$v['gong']."'")->find();
        	$products[$k]['gong']=$gong['name'];
        }
        
        $this->assign('list',$products);

        $this->assign('page',$show);

        $this->display();
    }
    
    
    //排序
    function paixu(){
    	$data=I("get.");
    	
    	$res=M("article")->where("art_id='".$data['id']."'")->save(array('pai'=>$data['val']));
        $select1=$data['select1'];
    	if($res){
    		  $this->success('修改成功',U('ProductManagement/index',array('select1'=>$select1)),1);
    	}else{
    		 $this->success('修改成功',U('ProductManagement/index',array('select1'=>$select1)),1);
    	}
    }
    
   
    function paixuche(){
    	$data=I("get.");
    	
    	$res=M("article")->where("art_id='".$data['id']."'")->save(array('pai'=>$data['val']));
        $select1=$data['select1'];
    	if($res){
    		  $this->success('修改成功',U('ProductManagement/index_che',array('select1'=>$select1)),1);
    	}else{
    		 $this->success('修改成功',U('ProductManagement/index_che',array('select1'=>$select1)),1);
    	}
    }
    
   
    
    /* 车文章列表
     *
     */
    
    public function index_che(){
    	
        $Article = M('article');
        $where['cate_p_id']=18;
		$catep=M("cate")->where($where)->getField('cate_id',true);
		$cate=M("cate")->where($where)->select();
		$this->cate=$cate;
		$select1=I("select1");
    $this->select1 = $select1;
		$select2=I("select2");
    $this->select2 = $select2;
		$ming=I("ming");
    $this->ming = $ming;
		$tt = array();
		if(empty($select1)&& empty($ming)){
			  $tt['art_cate_id'] = array('in',$catep);
		}else if(!empty($select1)){
			$tt['art_cate_id'] = $select1;
			$this->select1=$select1;
		}else if(!empty($ming)){
			$tt['product_title']=array('like','%'.$ming.'%');
		}
		if(!empty($select2)){
			  if($select2=='2'){
			  	 $tt['type'] = 0;
			  }else{
			  	 $tt['type'] = 1;
			  }
			
		}
          
       
		 
		$count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        $products = $Article -> where($tt) -> order('art_date desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
        	$tt=M("cate")->where("cate_id= '".$v['art_cate_id']."'")->find();
        	$products[$k]['fen']=$tt['cate_name']; 
        	$gong=M("gongying")->where("id='".$v['gong']."'")->find();
        	$products[$k]['gong']=$gong['name'];
        }
        
        $this->assign('list',$products);

        $this->assign('page',$show);

        $this->display();
    
    }

    // 忒娃产品列表
    public function index_teiwa(){
        $Article = M('article');
        $where['cate_p_id']=102;
        $catep=M("cate")->where($where)->getField('cate_id',true);
        $cate=M("cate")->where($where)->select();
        $this->cate=$cate;
        $select1=I("select1");
    $this->select1 = $select1;
        $select2=I("select2");
    $this->select2 = $select2;
        $ming=I("ming");
    $this->ming = $ming;
        $tt = array();
        if(empty($select1)&& empty($ming)){
              $tt['art_cate_id'] = array('in',$catep);
        }else if(!empty($select1)){
            $tt['art_cate_id'] = $select1;
            $this->select1=$select1;
        }else if(!empty($ming)){
            $tt['product_title']=array('like','%'.$ming.'%');
        }
        if(!empty($select2)){
              if($select2=='2'){
                 $tt['type'] = 0;
              }else{
                 $tt['type'] = 1;
              }
            
        }
          
       
         
        $count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        $products = $Article -> where($tt) -> order('art_date desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
            $tt=M("cate")->where("cate_id= '".$v['art_cate_id']."'")->find();
            $products[$k]['fen']=$tt['cate_name']; 
            $gong=M("gongying")->where("id='".$v['gong']."'")->find();
            $products[$k]['gong']=$gong['name'];
        }
        
        $this->assign('list',$products);

        $this->assign('page',$show);

        $this->display();
    }
    
    
    /*添加旅游
     *
     */
    public function add(){
        //分类
        $where['cate_p_id']=22;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        //旅游供应商
        $gong=M("gongying")->where("cate_id=22")->select();
        $this->gong=$gong;
        
        $this->display();
    }
   


    /*添加车
     *
     */
    public function add_che(){
    	
        //分类
        $where['cate_p_id']=18;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        //车产品fuwu商
        $fuwushang=M("fuwu")->where("cate_id=18")->select();
        $this->fuwushang=$fuwushang;
         //车产品供应商
        $gong=M("gongying")->where("cate_id=18")->select();
        $this->gong=$gong;
        $this->display();
    }


    // 添加忒娃
    public function add_teiwa(){
        //分类
        $where['cate_p_id']=102;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        //车产品fuwu商
        $fuwushang=M("fuwu")->where("cate_id=102")->select();
        $this->fuwushang=$fuwushang;
         //车产品供应商
        $gong=M("gongying")->where("cate_id=102")->select();
        $this->gong=$gong;
        $this->display();
    }

    public function combineDika() {
		$data = func_get_args();
		$data = current($data);
		$cnt = count($data);
		$result = array();
	    $arr1 = array_shift($data);
		foreach($arr1 as $key=>$item) 
		{
			$result[] = array($item);
		}		
	
		foreach($data as $key=>$item) 
		{                                
			$result = $this->combineArray($result,$item);
		}
		return $result;
	}
     
   
	public function combineArray($arr1,$arr2) {		 
		$result = array();
		foreach ($arr1 as $item1) 
		{
			foreach ($arr2 as $item2) 
			{
				$temp = $item1;
				$temp[] = $item2;
				$result[] = $temp;
			}
		}
		return $result;
	}
     
     
     
     
    public function ajaxGetSpecInput(){
    	
    	$spec_arr=I("spec_arr");
    	// 排序
        foreach ($spec_arr as $k => $v)
        {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);  
           
        foreach ($spec_arr_sort as $key =>$val)
        {
            $spec_arr2[$key] = $spec_arr[$key];
        }
        
        $clo_name = array_keys($spec_arr2);   //把key 值拿出来组成新的数组       
        $spec_arr2 = $this->combineDika($spec_arr2); //  获取 规格的 笛卡尔积       
                
    	$spec = M('shuxing')->getField('id,name'); // 规格表  
    	
    	$specItem = M('shuxingzhi')->getField('id,zhi,sid');//规格项                    
       $str = "<table class='table table-bordered' id='spec_input_tab' >";
       $str .="<tr>";       
       // 显示第一行的数据
       
       foreach ($clo_name as $k => $v) 
       {
           $str .=" <td style='width:100px;'><b>{$spec[$v]}</b></td>";
       }    
      $str .="<td><b>价格</b></td>
                <td><b>库存</b></td>
                </tr>";  
     	// 显示第二行开始 
       foreach ($spec_arr2 as $k => $v) 
       {
            $str .="<tr>";
            $item_key_name = array();
            foreach($v as $k2 => $v2)
            {
                $str .="<td>{$specItem[$v2][zhi]}</td>";
                $item_key_name[$v2] = $spec[$specItem[$v2]['sid']].':'.$specItem[$v2]['zhi'];
            }   
            ksort($item_key_name);            
            $item_key = implode('_', array_keys($item_key_name));
            $item_name = implode(' ', $item_key_name);
            
			$keySpecGoodsPrice[$item_key][price] ? false : $keySpecGoodsPrice[$item_key][price] = 0; // 价格默认为0
           
			$keySpecGoodsPrice[$item_key][store_count] ? false : $keySpecGoodsPrice[$item_key][store_count] = 0; //库存默认为0
            $str .="<td><input name='item[$item_key][price]' value='{$keySpecGoodsPrice[$item_key][price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td><input name='item[$item_key][store_count]' value='{$keySpecGoodsPrice[$item_key][store_count]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'/></td>";            
            $str .="</tr>";           
       }
        $str .= "</table>";   
        $this->ajaxReturn($str);
    
    }

       public function ajaxGet(){
    	
    	$spec_arr=I("spec_arr");
    	$ids=I("ids");
    	
    	// 排序
        foreach ($spec_arr as $k => $v)
        {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);  
           
        foreach ($spec_arr_sort as $key =>$val)
        {
            $spec_arr2[$key] = $spec_arr[$key];
        }
        
        $clo_name = array_keys($spec_arr2);   //把key 值拿出来组成新的数组       
        $spec_arr2 = $this->combineDika($spec_arr2); //  获取 规格的 笛卡尔积       
                
    	$spec = M('shuxing')->getField('id,name'); // 规格表  
    	
    	$specItem = M('shuxingzhi')->getField('id,zhi,sid');//规格项
    	$keySpecGoodsPrice=M("guige")->where("cid='".$ids."'")->getField('guige,num,money,cid');//规格项 
    	   
    	 foreach($keySpecGoodsPrice as $k=>$v){
    	 	$keySpecGoodsPrice[$k]['price']=$keySpecGoodsPrice[$k]['money'];
    	 	$keySpecGoodsPrice[$k]['store_count']=$keySpecGoodsPrice[$k]['num'];
    	 }         
//  	 dump($keySpecGoodsPrice);die;
       $str = "<table class='table table-bordered' id='spec_input_tab' >";
       $str .="<tr>";       
       // 显示第一行的数据
       
       foreach ($clo_name as $k => $v) 
       {
           $str .=" <td style='width:100px;'><b>{$spec[$v]}</b></td>";
       }    
      $str .="<td><b>价格</b></td>
                <td><b>库存</b></td>
                </tr>";  
     	// 显示第二行开始 
       foreach ($spec_arr2 as $k => $v) 
       {
            $str .="<tr>";
            $item_key_name = array();
            foreach($v as $k2 => $v2)
            {
                $str .="<td>{$specItem[$v2][zhi]}</td>";
                $item_key_name[$v2] = $spec[$specItem[$v2]['sid']].':'.$specItem[$v2]['zhi'];
            }   
            ksort($item_key_name);            
            $item_key = implode('_', array_keys($item_key_name));
            $item_name = implode(' ', $item_key_name);
            
			$keySpecGoodsPrice[$item_key][price] ? false : $keySpecGoodsPrice[$item_key][price] = 0; // 价格默认为0
           
			$keySpecGoodsPrice[$item_key][store_count] ? false : $keySpecGoodsPrice[$item_key][store_count] = 0; //库存默认为0
            $str .="<td><input name='item[$item_key][price]' value='{$keySpecGoodsPrice[$item_key][price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td><input name='item[$item_key][store_count]' value='{$keySpecGoodsPrice[$item_key][store_count]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'/></td>";            
            $str .="</tr>";           
       }
        $str .= "</table>";   
        $this->ajaxReturn($str);
    
    }

   public function jin(){
   	$this->display();
   }
    public function jing(){
   	$this->display();
   }


public function jinter(){
   	$this->display();
   }


public function jinst(){
   	$this->display();
   }


public function jintu(){
   	$this->display();
   }



    /*编辑
   *
   */
    public function edit(){

        $obj=M('article')->where(array('art_id'=>I('get.art_id')))->find();
        
        $this->assign('obj',$obj);

        //分类显示
        $where['cate_p_id']=22;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
         //旅游供应商
        $gong=M("gongying")->where("cate_id=22")->select();
        $this->gong=$gong;
        
         //规格
        $mygui=M("guige")->where(array('cid'=>I('get.art_id')))->select();
        $conter=count($mygui);
        $this->chang=$conter;
        $this->assign('guige',$mygui);
        $this->display();
    }
    /*添加处理旅游
     *
     */
    public function addcl(){
         $datat=I("post.");
        $model = M('article');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $data['art_img_url'] = $products;
        $data['art_img_url_s']=$banns1;
        $data['art_title']=$banns2;
        $data['art_keywords']=$banns3;
        $data['art_description']=$banns4;
        $data['product_title'] = I('post.art_title');//产品名称
        $data['jiage']=I("art_jiage");  //产品价格
        $data['huiyuan']=I("huiyuan");  //产品价格
        $data['jinjia']=I("jinjia");
        $data['num']=I("art_kucun");  //产品库存
        $data['guige']=I("art_shuo"); //产品说明
        $data['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $data['art_cate_id'] = I('post.art_cate_id'); //所属
        $data['art_content'] = I('post.art_content');//纵览
        $data['content'] = I('post.content'); //费用说明
        $data['cont'] = I('post.cont'); //行程
        $data['gong'] = I('post.gong_id'); //供应商
        
        $data['art_date']      =date('Y-m-d H:i:s',time());
        $res=M("article")->add($data);
        if($res){
          	/****处理产品规格值*****/
            if(!empty($datat['gui1'])|| !empty($datat['jia1']) || !empty($datat['num1'])){
              $arr=array(
                'guige'=>$datat['gui1'],
                'num'=>$datat['num1'],
                'money_huiyuan'=>$datat['huiyuan1'],
                'money'=>$datat['jia1'],
                'money_jin'=>$datat['jin1'],
                'money_you'=>$datat['you1'],
                'cid'=>$res
              );
              M("guige")->add($arr);  
            }

            if(!empty($datat['gui2'])|| !empty($datat['jia2']) || !empty($datat['num2'])){
              $arr=array(
                'guige'=>$datat['gui2'],
                'num'=>$datat['num2'],
                'money_huiyuan'=>$datat['huiyuan2'],
                'money'=>$datat['jia2'],
                'money_jin'=>$datat['jin2'],
                'money_you'=>$datat['you2'],
                'cid'=>$res
              );
              M("guige")->add($arr);  
            }


            if(!empty($datat['gui3'])|| !empty($datat['jia3']) || !empty($datat['num3'])){
              $arr=array(
                'guige'=>$datat['gui3'],
                'num'=>$datat['num3'],
                'money_huiyuan'=>$datat['huiyuan3'],
                'money'=>$datat['jia3'],
                'money_jin'=>$datat['jin3'],
                'money_you'=>$datat['you3'],
                'cid'=>$res
              );
              M("guige")->add($arr);  
            }

            if(!empty($datat['gui4'])|| !empty($datat['jia4']) || !empty($datat['num4'])){
              $arr=array(
                'guige'=>$datat['gui4'],
                'num'=>$datat['num4'],
                'money_huiyuan'=>$datat['huiyuan4'],
                'money'=>$datat['jia4'],
                'money_jin'=>$datat['jin4'],
                'money_you'=>$datat['you4'],
                'cid'=>$res
              );
              M("guige")->add($arr);  
            }

            if(!empty($datat['gui5'])|| !empty($datat['jia5']) || !empty($datat['num5'])){
              $arr = array(
                'guige'=>$datat['gui5'],
                'num'=>$datat['num5'],
                'money_huiyuan'=>$datat['huiyuan5'],
                'money'=>$datat['jia5'],
                'money_jin'=>$datat['jin5'],
                'money_you'=>$datat['you5'],
                'cid'=>$res
              );
              M("guige")->add($arr);  
            }

            if(!empty($datat['gui6'])|| !empty($datat['jia6']) || !empty($datat['num6'])){
              $arr = [
                'guige'=>$datat['gui6'],
                'num'=>$datat['num6'],
                'money_huiyuan'=>$datat['huiyuan6'],
                'money'=>$datat['jia6'],
                'money_jin'=>$datat['jin6'],
                'money_you'=>$datat['you6'],
                'cid'=>$res
              ];
              M("guige")->add($arr);  
            }

            if(!empty($datat['gui7'])|| !empty($datat['jia7']) || !empty($datat['num7'])){
              $arr = [
                'guige'=>$datat['gui7'],
                'num'=>$datat['num7'],
                'money_huiyuan'=>$datat['huiyuan7'],
                'money'=>$datat['jia7'],
                'money_jin'=>$datat['jin7'],
                'money_you'=>$datat['you7'],
                'cid'=>$res
              ];
              M("guige")->add($arr);  
            }

            if(!empty($datat['gui8'])|| !empty($datat['jia8']) || !empty($datat['num8'])){
              $arr = [
                'guige'=>$datat['gui8'],
                'num'=>$datat['num8'],
                'money_huiyuan'=>$datat['huiyuan8'],
                'money'=>$datat['jia8'],
                'money_jin'=>$datat['jin8'],
                'money_you'=>$datat['you8'],
                'cid'=>$res
              ];
              M("guige")->add($arr);  
            }

            $this->success('添加成功',U('ProductManagement/index'),1);
        }else{
        	 $this->error('添加失败',U('ProductManagement/index'),1);
        }
        
        

    }

   //添加车
   public function addcl_che(){
   	    $data=I("post.");
// 	    dump($data);die;
   	    $model = M('article');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $data['art_img_url'] = $products;
        $data['art_img_url_s']=$banns1;
        $data['art_title']=$banns2;
        $data['art_keywords']=$banns3;
        $data['art_description']=$banns4;
        $data['product_title'] = I('post.art_title');//产品名称
        $data['jiage']=I("art_jiage");  //产品商城价格
        $data['huiyuan']=I("huiyuan");  //产品会员价格  111
        $data['num']=I("art_kucun");  //产品库存
        $data['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $data['guige']=I("art_shuo"); //产品说明
        $data['youdi']=I("art_you"); //邮费
        $data['art_cate_id'] = I('post.art_cate_id'); //所属
        $data['art_content'] = I('post.art_content');//纵览
        $data['content'] = I('post.content'); //费用说明
        $data['cont'] = I('post.cont'); //行程
        $data['shipin'] = I('post.shipin'); //Banner视频
        $data['xiang_shipin'] = I('post.xiang_shipin'); //详情视频111
        $data['gong'] = I('post.gong_id'); //供应商
        $data['jinjia'] = I('post.jinjia'); //进价 111
        $data['fuwu'] = I('post.fuwu'); //产品是否有服务商  1是  0 不是 111
        $data['fu_id'] = I('post.fu_id'); //服务商 111
        $data['f_money'] = I('post.f_money'); //服务商费用111
        $data['art_date'] =date('Y-m-d H:i:s',time());
        $res=$model->add($data);
         
        
        if($res){
        	 
        	/****处理产品规格值*****/
          if(!empty($data['gui1'])|| !empty($data['jia1']) || !empty($data['num1'])){
            $arr=array(
              'guige'=>$data['gui1'],
              'num'=>$data['num1'],
              'money'=>$data['jia1'],
              'money_huiyuan'=>$data['huiyuan1'],
              'money_jin'=>$data['jin1'],
              'money_you'=>$data['you1'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui2'])|| !empty($data['jia2']) || !empty($data['num2'])){
            $arr=array(
              'guige'=>$data['gui2'],
              'num'=>$data['num2'],
              'money'=>$data['jia2'],
              'money_huiyuan'=>$data['huiyuan2'],
              'money_jin'=>$data['jin2'],
              'money_you'=>$data['you2'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui3'])|| !empty($data['jia3']) || !empty($data['num3'])){
            $arr=array(
              'guige'=>$data['gui3'],
              'num'=>$data['num3'],
              'money'=>$data['jia3'],
              'money_huiyuan'=>$data['huiyuan3'],
              'money_jin'=>$data['jin3'],
              'money_you'=>$data['you3'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui4'])|| !empty($data['jia4']) || !empty($data['num4'])){
            $arr=array(
              'guige'=>$data['gui4'],
              'num'=>$data['num4'],
              'money'=>$data['jia4'],
              'money_huiyuan'=>$data['huiyuan4'],
              'money_jin'=>$data['jin4'],
              'money_you'=>$data['you4'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui5'])|| !empty($data['jia5']) || !empty($data['num5'])){
            $arr=array(
              'guige'=>$data['gui5'],
              'num'=>$data['num5'],
              'money'=>$data['jia5'],
              'money_huiyuan'=>$data['huiyuan5'],
              'money_jin'=>$data['jin5'],
              'money_you'=>$data['you5'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui6'])|| !empty($data['jia6']) || !empty($data['num6'])){
            $arr=array(
              'guige'=>$data['gui6'],
              'num'=>$data['num6'],
              'money'=>$data['jia6'],
              'money_huiyuan'=>$data['huiyuan6'],
              'money_jin'=>$data['jin6'],
              'money_you'=>$data['you6'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui7'])|| !empty($data['jia7']) || !empty($data['num7'])){
            $arr=array(
              'guige'=>$data['gui7'],
              'num'=>$data['num7'],
              'money'=>$data['jia7'],
              'money_huiyuan'=>$data['huiyuan7'],
              'money_jin'=>$data['jin7'],
              'money_you'=>$data['you7'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }

          if(!empty($data['gui8'])|| !empty($data['jia8']) || !empty($data['num8'])){
            $arr=array(
              'guige'=>$data['gui8'],
              'num'=>$data['num8'],
              'money'=>$data['jia8'],
              'money_huiyuan'=>$data['huiyuan8'],
              'money_jin'=>$data['jin8'],
              'money_you'=>$data['you8'],
              'cid'=>$res
            );
            M("guige")->add($arr);  
          }
					
					  if(!empty($data['teiwa'])){
					  		$this->success('添加成功',U('productManagement/index_teiwa'),1); //忒娃
					  }else{
					  	$this->success('添加成功',U('ProductManagement/index_che'),1); //忒车
					  } 
           
        }else{
           if(!empty($data['teiwa'])){
           	$this->success('添加失败',U('productManagement/index_teiwa'),1); //忒娃
           }else{
           	$this->error('添加失败',U('ProductManagement/index_che'),1);
           }
        }

    
   }
   
   
   public function edit_che(){
   	

        $obj=M('article')->where(array('art_id'=>I('get.art_id')))->find();
        
        $this->assign('obj',$obj);

        //分类显示
        $where['cate_p_id']=18;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
         
        //规格
        $mygui=M("guige")->where(array('cid'=>I('get.art_id')))->select();
        $conter=count($mygui);
        $this->chang=$conter;
        $this->assign('guige',$mygui);
	       
            //车供应商
        $gong=M("gongying")->where("cate_id=18")->select();
        $this->gong=$gong;
        //服务商
        $fuwu=M("fuwu")->where("cate_id=18")->select();
        $this->fuwu=$fuwu;
        $this->display();
    
   }

   public function edit_teiwa(){
    

        $obj=M('article')->where(array('art_id'=>I('get.art_id')))->find();
        
        $this->assign('obj',$obj);

        //分类显示
        $where['cate_p_id']=102;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
         
        //规格
        $mygui=M("guige")->where(array('cid'=>I('get.art_id')))->select();
        $conter=count($mygui);
        $this->chang=$conter;
        $this->assign('guige',$mygui);
           
            //车供应商
        $gong=M("gongying")->where("cate_id=102")->select();
        $this->gong=$gong;
        //服务商
        $fuwu=M("fuwu")->where("cate_id=102")->select();
        $this->fuwu=$fuwu;
        $this->display();
    
   }

    /*删除
     *
     */
    public function delete(){
    	$ids=I('art_id');
    	
        $rs=M('article')->where(array('art_id'=>$ids))->delete();
        if($rs){
            $this->success('删除成功',U('ProductManagement/index'));
        }else{
            $this->error();
        }
    }

    /*编辑处理旅游
         *
         */
    public function update(){
          $doto=I("post.");
        $model = M('article');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $data['art_img_url'] = $products;
        $data['art_img_url_s']=$banns1;
        $data['art_title']=$banns2;
        $data['art_keywords']=$banns3;
        $data['art_description']=$banns4;
        $data['product_title'] = I('post.art_title');//产品名称
        $data['jiage']=I("art_jiage");  //产品价格
         $data['huiyuan']=I("huiyuan");  //产品价格
         $data['jinjia']=I("jinjia");
        $data['num']=I("art_kucun");  //产品库存
        $data['guige']=I("art_shuo"); //产品说明
        $data['youdi']=I("art_you"); //邮费
        $data['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $data['art_cate_id'] = I('post.art_cate_id'); //所属
        $data['art_content'] = I('post.art_content');//纵览
        $data['content'] = I('post.content'); //费用说明
        $data['cont'] = I('post.cont'); //行程
        $data['gong']=I('post.gong_id');
        $data['art_date']      =date('Y-m-d H:i:s',time());
        
        $where['art_id']=I('post.art_id');
         
        $rs=M('article')->where($where)->save($data);

        if($rs){
        	 /****处理产品规格值*****/
            $cid=I('post.art_id');
            $guige = M("guige")->where("cid='".$cid."'")->delete();
            if(!empty($doto['gui1'])|| !empty($doto['jia1']) || !empty($doto['num1'])){
              $arr=array(
                'guige'=>$doto['gui1'],
                'num'=>$doto['num1'],
                'money'=>$doto['jia1'],
                'money_huiyuan'=>$doto['huiyuan1'],
                'money_jin'=>$doto['jin1'],
                'money_you'=>$doto['you1'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui2'])|| !empty($doto['jia2']) || !empty($doto['num2'])){
              $arr=array(
                'guige'=>$doto['gui2'],
                'num'=>$doto['num2'],
                'money'=>$doto['jia2'],
                'money_huiyuan'=>$doto['huiyuan2'],
                'money_jin'=>$doto['jin2'],
                'money_you'=>$doto['you2'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui3'])|| !empty($doto['jia3']) || !empty($doto['num3'])){
              $arr=array(
                'guige'=>$doto['gui3'],
                'num'=>$doto['num3'],
                'money'=>$doto['jia3'],
                'money_huiyuan'=>$doto['huiyuan3'],
                'money_jin'=>$doto['jin3'],
                'money_you'=>$doto['you3'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui4'])|| !empty($doto['jia4']) || !empty($doto['num4'])){
              $arr=array(
                'guige'=>$doto['gui4'],
                'num'=>$doto['num4'],
                'money'=>$doto['jia4'],
                'money_huiyuan'=>$doto['huiyuan4'],
                'money_jin'=>$doto['jin4'],
                'money_you'=>$doto['you4'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui5'])|| !empty($doto['jia5']) || !empty($doto['num5'])){
              $arr=array(
                'guige'=>$doto['gui5'],
                'num'=>$doto['num5'],
                'money'=>$doto['jia5'],
                'money_huiyuan'=>$doto['huiyuan5'],
                'money_jin'=>$doto['jin5'],
                'money_you'=>$doto['you5'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui6'])|| !empty($doto['jia6']) || !empty($doto['num6'])){
              $arr=array(
                'guige'=>$doto['gui6'],
                'num'=>$doto['num6'],
                'money'=>$doto['jia6'],
                'money_huiyuan'=>$doto['huiyuan6'],
                'money_jin'=>$doto['jin6'],
                'money_you'=>$doto['you6'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui7'])|| !empty($doto['jia7']) || !empty($doto['num7'])){
              $arr=array(
                'guige'=>$doto['gui7'],
                'num'=>$doto['num7'],
                'money'=>$doto['jia7'],
                'money_huiyuan'=>$doto['huiyuan7'],
                'money_jin'=>$doto['jin7'],
                'money_you'=>$doto['you7'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }

            if(!empty($doto['gui8'])|| !empty($doto['jia8']) || !empty($doto['num8'])){
              $arr=array(
                'guige'=>$doto['gui8'],
                'num'=>$doto['num8'],
                'money'=>$doto['jia8'],
                'money_huiyuan'=>$doto['huiyuan8'],
                'money_jin'=>$doto['jin8'],
                'money_you'=>$doto['you8'],
                'cid'=>$cid
              );
              M("guige")->add($arr);  
            }
            $this->success('编辑成功',U('ProductManagement/index'),1);
        }else{
            $this->error('编辑失败',U('ProductManagement/index'),1);
        }

    }
    
    //车编辑
    public function update_che(){
    	 $doto=I("post.");
    	 
    	 
    	$model = M('article');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $data['art_img_url'] = $products;
        $data['art_img_url_s']=$banns1;
        $data['art_title']=$banns2;
        $data['art_keywords']=$banns3;
        $data['art_description']=$banns4;
        $data['product_title'] = I('post.art_title');//产品名称
        $data['jiage']=I("art_jiage");  //产品价格
        $data['num']=I("art_kucun");  //产品库存
        $data['guige']=I("art_shuo"); //产品说明
        $data['youdi']=I("art_you"); //邮费
        $data['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $data['art_cate_id'] = I('post.art_cate_id'); //所属
        $data['art_content'] = I('post.art_content');//纵览
        $data['content'] = I('post.content'); //费用说明
        $data['cont'] = I('post.cont'); //行程
        $data['shipin'] = I('post.shipin'); //视频
        $data['gong']=I('post.gong_id'); //供应商
        $data['art_date']      =date('Y-m-d H:i:s',time());
        $data['jinjia'] = I('post.jinjia'); //进价 111
        $data['huiyuan']=I("huiyuan");  //产品会员价格  111
        $data['fuwu'] = I('post.fuwu'); //产品是否有服务商  1是  0 不是 111
        $data['fu_id'] = I('post.fu_id'); //服务商 111
        $data['f_money'] = I('post.f_money'); //服务商费用111
        $data['xiang_shipin'] = I('post.xiang_shipin'); //详情视频111
        $where['art_id']=I('post.art_id');
        
        $cid=I('post.art_id');
        $rs=M('article')->where($where)->save($data);

        if($rs){
        	/****处理产品规格值*****/
           
          $guige=M("guige")->where("cid='".$cid."'")->delete();
          if(!empty($doto['gui1'])|| !empty($doto['jia1']) || !empty($doto['num1'])){
            $arr=array(
              'guige'=>$doto['gui1'],
              'num'=>$doto['num1'],
              'money'=>$doto['jia1'],
              'money_huiyuan'=>$doto['huiyuan1'],
              'money_jin'=>$doto['jin1'],
              'money_you'=>$doto['you1'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui2'])|| !empty($doto['jia2']) || !empty($doto['num2'])){
            $arr=array(
              'guige'=>$doto['gui2'],
              'num'=>$doto['num2'],
              'money'=>$doto['jia2'],
              'money_huiyuan'=>$doto['huiyuan2'],
              'money_jin'=>$doto['jin2'],
              'money_you'=>$doto['you2'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui3'])|| !empty($doto['jia3']) || !empty($doto['num3'])){
            $arr=array(
              'guige'=>$doto['gui3'],
              'num'=>$doto['num3'],
              'money'=>$doto['jia3'],
              'money_huiyuan'=>$doto['huiyuan3'],
              'money_jin'=>$doto['jin3'],
              'money_you'=>$doto['you3'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui4'])|| !empty($doto['jia4']) || !empty($doto['num4'])){
            $arr=array(
              'guige'=>$doto['gui4'],
              'num'=>$doto['num4'],
              'money'=>$doto['jia4'],
              'money_huiyuan'=>$doto['huiyuan4'],
              'money_jin'=>$doto['jin4'],
              'money_you'=>$doto['you4'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui5'])|| !empty($doto['jia5']) || !empty($doto['num5'])){
            $arr=array(
              'guige'=>$doto['gui5'],
              'num'=>$doto['num5'],
              'money'=>$doto['jia5'],
              'money_huiyuan'=>$doto['huiyuan5'],
              'money_jin'=>$doto['jin5'],
              'money_you'=>$doto['you5'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui6'])|| !empty($doto['jia6']) || !empty($doto['num6'])){
            $arr=array(
              'guige'=>$doto['gui6'],
              'num'=>$doto['num6'],
              'money'=>$doto['jia6'],
              'money_huiyuan'=>$doto['huiyuan6'],
              'money_jin'=>$doto['jin6'],
              'money_you'=>$doto['you6'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui7'])|| !empty($doto['jia7']) || !empty($doto['num7'])){
            $arr=array(
              'guige'=>$doto['gui7'],
              'num'=>$doto['num7'],
              'money'=>$doto['jia7'],
              'money_huiyuan'=>$doto['huiyuan7'],
              'money_jin'=>$doto['jin7'],
              'money_you'=>$doto['you7'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

          if(!empty($doto['gui8'])|| !empty($doto['jia8']) || !empty($doto['num8'])){
            $arr=array(
              'guige'=>$doto['gui8'],
              'num'=>$doto['num8'],
              'money'=>$doto['jia8'],
              'money_huiyuan'=>$doto['huiyuan8'],
              'money_jin'=>$doto['jin8'],
              'money_you'=>$doto['you8'],
              'cid'=>$cid
            );
            M("guige")->add($arr);  
          }

        	$this->success('编辑成功',U('ProductManagement/index_che'),1);
        	
        }else{
            $this->error('编辑失败',U('ProductManagement/index_che'),1);
        }

    
    }
    
    /*批量删除
     *
     */
    public function deleteAll(){
    	  $data=I("post.");
    	      
        foreach($data as $rows)
        {
            $rs=M('article')->where(array("art_id"=>$rows))->delete();

        }
      
        if($rs){
            $this->success('批量删除成功',U('ProductManagement/index'));
        }else{
            $this->error('批量删除失败',U('ProductManagement/index'));
        }
    }


    public function imgs(){
        $this->assign('pid',I('get.art_id'));
        $this->display('Article/imgs');
    }

    //通过webuploader上传的图片
    public function webuploader() {

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =  3*1024*1024 ;// 设置附件上传大小
        $upload->exts      =  array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Upload/'; // 设置附件上传根目录
        $upload->savePath = './Upload/'; // 设置附件上传（子）目录
        //创建文件夹
        if(!is_dir($upload->savePath)){
            mkdir($upload->savePath,0777,true);
        }
        // 上传文件,返回上次结果
        // dump(1);
        $info   =  $upload->upload();
        //dump($photos);
        // 			exit();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            echo json_encode($info);
        }

    }
    public function imgadd(){

        $model = M('img_url');
        foreach($_POST['img_url'] as $row) {
            $data['img_p_id'] = I('post.pid');
            $data['img_url'] = $row;
            $arr=$model->add($data);
        }
        if ($arr) {
            $this->success('添加成功', U('Article/imgall',array('art_id'=>I('post.pid'))), 1);
        } else {
            $this->error('添加失败', U('Article/imgall',array('art_id'=>I('post.pid'))), 1);
        }


    }
    public function imgall(){
        $model = M('img_url');
        $pid=I('get.art_id');
        $this->assign('pid',$pid);
        $list=$model->table(array('ht_article'=>'a','ht_img_url'=>'b'))->where('b.img_p_id=a.art_id and b.img_p_id = '.$pid)->order('img_id desc')->select();
        //dump($list);
        $this->assign('list',$list);

        $this->display('Article/imgall');
    }

    /*删除
        *
        */
    public function imgdelete(){
        $rs=M('img_url')->where(array('img_id'=>I('get.img_id')))->delete();
        if($rs){
            $this->success('删除成功',U('Article/imgall',array('art_id'=>I('get.pid'))));
        }else{
            $this->error();
        }
    }

    /*批量删除
 *
 */
    public function imgdeleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('img_url')->where(array("img_id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Article/imgall',array('art_id'=>I('get.pid'))));
        }else{
            $this->error('批量删除失败',U('Article/imgall',array('art_id'=>I('get.pid'))));
        }
    }
    
    
    
    public function index_baoxian(){

    	 $Article = M('article');
    	 $select2=I("select2");
    	 $ming=I("ming");
    	 
	   	 $tt = array();
		 if(!empty($select2)){
			  if($select2=='2'){
			  	 $tt['type'] = 0;
			  }else{
			  	 $tt['type'] = 1;
			  }
			
		}
        $tt['art_cate_id']=81;
		if(!empty($ming)){
    	 	$tt['product_title']=array('like','%'.$ming.'%');
    	 } 
    	 //dump($tt);
		$count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        $products = $Article -> where($tt) -> order('art_date desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
        	$tt=M("cate")->where("cate_id= '".$v['art_cate_id']."'")->find();
        	$products[$k]['fen']=$tt['cate_name']; 
        }
        
        $this->assign('list',$products);

        $this->assign('page',$show);

        $this->display();
    	 
    	
    }
    
    // 添加保险
    public function add_baoxian(){
    	 //分类
        $where['cate_id']=81;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
      
        $this->assign('list2',$list2);
    	    //保险供应商
        $gong=M("gongying")->where("cate_id=81")->select();
        $this->gong=$gong;
    	 $this->display();
    }
    
    public  function addcl_baoxian(){
    	 $data=I("post.");
    	// dump($data);die;
    	 $arr=array(
    	   'art_cate_id'=>$data['art_cate_id'],  //所属id
    	   'jiage'=>$data['art_jiage'],   //价格
    	   'art_img_url'=>$data['images'],  // 保险图片
    	   'product_title'=>$data['art_title'], //保险名称
    	   'art_content'=>$data['art_content'],
    	   'type'=>$data['type'],  //商品上下架
    	   'num'=>$data['art_kucun'],//库存
    	    'gong'=>$data['gong_id'],//供应商
    	   'art_date'=>date('Y-m-d H:i:s',time())
    	 );
    	 $res=M("article")->add($arr);
    	 if($res){
    	 	  $this->success('添加成功',U('ProductManagement/index_baoxian'));
    	 }else{
    	 	 $this->success('添加失败',U('ProductManagement/index_baoxian'));
    	 }
    	//$this->display();
    }
    
    
   public function baoxian_edit(){
   
   	    $obj=M('article')->where(array('art_id'=>I('get.art_id')))->find();
        
        $this->assign('obj',$obj);

        //分类显示
        $where['cate_id']=81;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
            //保险供应商
        $gong=M("gongying")->where("cate_id=81")->select();
        $this->gong=$gong;
        $this->display();
   }
   
   public function baoxian_update(){
   	    $data=I("post.");
        
   	    $arr=array(
   	       'art_cate_id'=>$data['art_cate_id'],  //所属id
   	       'jiage'=>$data['art_jiage'],   //价格
    	   'art_img_url'=>$data['images'],  // 保险图片
    	   'product_title'=>$data['art_title'], //保险名称
    	    'type'=>$data['type'],  //商品上下架
    	      'num'=>$data['art_kucun'],//库存
    	        'gong'=>$data['gong_id'],//供应商
    	   'art_content'=>$data['art_content']
   	    );
   	  
   	      $ids=$data['art_id'];
   	     $res=M("article")->where("art_id='".$ids."'")->save($arr);
   	     if($res){
   	     	  $this->success('修改成功',U('ProductManagement/index_baoxian'));
   	     }else{
   	     	  $this->success('修改成功',U('ProductManagement/index_baoxian'));
   	     }
   	    
   	  
   	
   }

   // 阿里云OSS上传视频
   public function cheBannerVideo(){
      $this->display();
   }

   public function cheDetVideo(){
      $this->display();
   }
   
   // 阿里云上传视频
    // public function ossVideo(){ 
    //   set_time_limit(0);
    //   ini_set('memory_limit', '512M');
    //   ini_set('magic_quotes_runtime', 0);

    //   import("Org.Util.aliyunOss.samples.Common");
    //   $bucket = \Common::getBucketName(); // 获取存储空间名称
    //   $ossClient = \Common::getOssClient();
    //   if (is_null($ossClient)) {
    //     $this->ajaxReturn([
    //         'error' => 1, 
    //         'message' => '阿里云OSS连接失败！'
    //     ]);
    //   }

    //   $file = $_FILES['file'];
    //   if (!isset($file) || empty($file)) {
    //     $this->ajaxReturn([
    //         'error' => 1, 
    //         'message' => '非法上传操作！'
    //     ]);
    //   }

    //   $ext = substr($file['name'], stripos($file['name'], '.'));

    //   if (!in_array($ext, ['.mp4', '.swf', '.flv', '.wav', '.ram', '.wma'])) {
    //     $this->ajaxReturn([
    //         'error' => 1, 
    //         'message' => '视频文件格式不正确！'
    //     ]);
    //   }

    //   $object = uniqid() . $ext; // 想要保存文件的名称
    //   $filePath = $file['tmp_name']; // 文件路径，必须是本地的。

    //   $res = $ossClient->uploadFile($bucket, $object, $filePath, []); // 上传文件
    //   if ($res) {
    //     $ossClient->putObjectAcl($bucket, $object, 'public-read'); // 文件的权限设置成public-read
    //     $this->ajaxReturn([
    //         'error' => 0,
    //         'url' => $res['info']['url'],
    //         'message' => '上传成功！'
    //     ]);
    //   }else{
    //     $this->ajaxReturn([
    //         'error' => 1, 
    //         'message' => '抱歉，上传失败，请重新上传！'
    //     ]);
    //   }
    // }

    // 删除阿里云视频
    // public function delVideo(){
    //   import("Org.Util.aliyunOss.samples.Common");
    //   $bucket = \Common::getBucketName(); // 获取存储空间名称
    //   $ossClient = \Common::getOssClient();
    //   if (is_null($ossClient)) exit(1);
    //   $object = '5bc99f20e85c0.jpg';
    //   $res = $ossClient->deleteObject($bucket, $object);
    //   dump($res);
    // }
    
}











