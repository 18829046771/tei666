<?php
namespace Admin\Controller;
use Think\Controller;
class LinksController extends CommController {

    /* 链接列表
     *
     */
    public function index(){
        //分类名称获取
        $User= M('Links');
        $count= $User->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list=$User->table(array('ht_links'=>'a','ht_link_c'=>'b'))->where('b.l_cate_id=a.link_cate_id')->order('link_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }
    public function linklist(){
        //分类名称获取
        $User= M('Links');
        $count= $User->where(array('link_cate_id'=>I('get.l_cate_id')))->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $pid=I('get.l_cate_id');
        $list=$User->table(array('ht_links'=>'a','ht_link_c'=>'b'))->where('b.l_cate_id=a.link_cate_id and a.link_cate_id = '.$pid)->order('link_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display('Links/index'); // 输出模板
    }
    
    
     //排序
    function paixu(){
    	$data=I("get.");
    	
    	$res=M("article")->where("art_id='".$data['id']."'")->save(array('pai'=>$data['val']));
        $select1=$data['select1'];
    	if($res){
    		  $this->success('修改成功',U('Links/qita_index',array('select1'=>$select1)),1);
    	}else{
    		 $this->success('修改成功',U('Links/qita_index',array('select1'=>$select1)),1);
    	}
    }
    /* 增加链接
    *
    */
    public function add(){
        //分类
        $data=I("post.");
        
        $arr=array(
         'name'=>$data['art_title']
        );
        $res=M("shuxing")->add($arr);
        $data_tt=array_values($data);
//       dump($data_tt);
        unset($data_tt[0]);
        $temp=array_values($data_tt);
//      dump($temp);
       if($res){
        	   foreach($temp as $k=>$v){
        	   	M("shuxingzhi")->add(array('zhi'=>$v,'sid'=>$res));
        	   }
        	$this->success('添加成功', U('Links/liebiao'), 1);
        }else{
        	$this->error('添加失败', U('Links/liebiao'), 1);
        }
    }
    
    
    public function liebiao(){
    	$temp=M("shuxing")->select();
    	 foreach($temp as $k=>$v){
    	 	foreach($v as $t=>$h){
    	 		if($t=="id"){
    	 			$arr=M("shuxingzhi")->where("sid='".$h."'")->select();
    	 		}
    	 		$temp[$k]['zhi']=$arr;
    	 	}
    	 }
    	 // dump($temp);
    	$this->temp=$temp;
    	$this->display();
    }
    
    
    
    
    
    /* 增加链接
   *
   */
    public function edit(){
        $obj=M('shuxing')->where(array('id'=>I('get.id')))->find();
       
        $temp=M("shuxingzhi")->where(array('sid'=>I('get.id')))->select();
        $obj['zhi']=$temp;
        $this->assign('list2',$obj);
         // dump($obj);
        $this->display();
    }
    public function delete_zhi(){
    	$id=I("id");// 属性值ID
    	$sid=I("sid");  //属性id
    	
    	if(M("shuxingzhi")->where("id='".$id."'")->delete()){
    		$this->success('操作成功', U('Links/edit',array('id'=>$sid)), 1);
    	}
    }
    
    public function add_zhi(){
    	$date=I("post.");
    	// dump($date);die;
    	 //处理属性名称
    	$shuxing=M("shuxing")->where(array('id'=>I("shuxing")))->find();
    	  
    	if($shuxing['name'] !==$date['art_title']){
    		$arr=array(
    		  'name'=>$date['art_title']
    		);
    		M("shuxing")->where(array('id'=>I("shuxing")))->save($arr);
    	}
    	//处理原有的属性值
    	 
    	foreach($date as $k=>$v){
    		if(is_numeric($k)){
    		  $zhi=M("shuxingzhi")->where("id='".$k."'")->find();
    		   if($zhi['zhi'] !== $v){
    		   	$arr1=array('zhi'=>$v);
    		   	M("shuxingzhi")->where("id='".$k."'")->save($arr1);
    		   }
    		}
    	}
    	  
    	//处理新增的属性值
    	 $sid=$date['shuxing'];
    	foreach( $date as $t=>$h){
    		if(is_numeric($t)){
    			unset($date[$t]);
    		}
    		unset($date['shuxing']);
    		unset($date['art_title']);
    	}
    	 
    	 $temp=array_values($date);
    	 foreach($temp as $k=>$v){
    	 	M("shuxingzhi")->add(array('zhi'=>$v,'sid'=>$sid));
    	 }
    	$this->success('操作成功', U('Links/liebiao'), 1);
    }
    
    
    /* 增加链接处理
    *
    */
    public function addcl()
    {


        if (!empty($_FILES['link_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Link/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName = time() . rand(1000, 9999);
            // 上传文件
            $info = $upload->uploadOne($_FILES['link_img_url']);
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }  /*else {// 上传成功
                $this->success('上传成功！');
            }*/

        }

        $model = M('Links');
        $data['link_img_url'] = $info['savename'];
        // 保存当前数据对象
        $data['link_name'] = I('post.link_name');
        $data['link_cate_id'] = I('post.link_cate_id');
        $data['link_href'] = I('post.link_href');
        $data['link_order'] = I('post.link_order');
        $data['link_target'] = I('post.link_target');
        $data['link_status'] = I('post.link_status');
        if ($model->add($data)) {
            //redirect(U('Cate/cateList'));
            $this->success('添加成功', U('Links/index'), 1);
        } else {
            $this->error('编辑失败', U('Links/index'), 1);
        }
    }
    /*删除
        *
        */
    public function delete(){
        $rs=M('shuxing')->where(array('id'=>I('get.id')))->delete();
        if($rs){
            $this->success('删除成功', U('Links/liebiao'), 1);
        }else{
            $this->error();
        }
    }
    /*编辑处理
        *
        */
    public function update(){




        if (!empty($_FILES['link_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Link/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName = time() . rand(1000, 9999);
            // 上传文件
            $info = $upload->uploadOne($_FILES['link_img_url']);
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }
            $_update['link_img_url'] = $info['savename'];
        }
        // 保存当前数据对象

        $_update['link_name'] = I('post.link_name');
        $_update['link_cate_id'] = I('post.link_cate_id');
        $_update['link_href'] = I('post.link_href');
        $_update['link_order'] = I('post.link_order');
        $_update['link_target'] = I('post.link_target');
        $_update['link_status'] = I('post.link_status');

        $where['link_id']=I('get.link_id');
        $rs=M('Links')->where($where)->save($_update);

        if($rs){
            $this->success('编辑成功',U('Links/index'),1);
        }else{
            $this->error('编辑失败',U('Links/index'),1);
        }

    }
    /*批量删除
     *
     */
    public function deleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('Links')->where(array("link_id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Links/index'));
        }else{
            $this->error('批量删除失败',U('Links/index'));
        }
    }
    
    
    //节目列表
     public function jiemu_index(){
     	$Article = M('article');
        $where['cate_p_id']=82;
		$catep=M("cate")->where($where)->getField('cate_id',true);
		
		$tt = array();
		$search=I("search");
    $this->search = $search;
		if(!empty($search)){
			$tt['product_title']= ['like', '%' . $search . '%'];
		}
        $tt['art_cate_id'] = array('in',$catep);
		 
		$count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        $products = $Article -> where($tt) -> order('art_id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
        	$tt=M("cate")->where("cate_id= '".$v['art_cate_id']."'")->find();
        	$products[$k]['fen']=$tt['cate_name']; 
        }
        
        $this->assign('list',$products);

        $this->assign('page',$show);

     	$this->display();
     }
     
     //添加节目
     public function jiemu_add(){
     	 //分类
        $where['cate_p_id']=82;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        
     	$this->display();
     }
     
     //节目添加处理
     public function jiemu_addcl(){
     	$data=I("post.");
     	
     	$array=array(
     	  'art_cate_id'=>$data['link_cate_id'],
     	  'product_title'=>$data['link_name'],
     	  'content'=>$data['link_href'],
     	  'art_img_url'=>$data['images'],
     	  'cont'=>$data['art_content'],
     	  'art_date' => date('Y-m-d H:i:s',time())
     	);
     	$res=M("article")->add($array);
     	if($res){
     		 $this->success('添加成功',U('Links/jiemu_index'));
     	}else{
     		$this->success('添加失败',U('Links/jiemu_index'));
     	}
     }
     
     //节目编辑
     public function jiemu_edit(){
     	$id=I("art_id");
     	$temp=M("article")->where("art_id='".$id."'")->find();
     	$this->temp=$temp;
     	 //分类
        $where['cate_p_id']=82;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        $this->assign('list2',$list2);
     	$this->display();
     	
     }
     
     
     //节目编辑处理
     public function jiemu_update(){
     	$data=I("post.");
     	$id=$data['art_id'];
     	$array=array(
     	  'art_cate_id'=>$data['link_cate_id'],
     	  'product_title'=>$data['link_name'],
     	   'art_img_url'=>$data['images'],
     	  'content'=>$data['link_href'],
     	  'cont'=>$data['art_content']
     	  
     	);
     	$rest=M("article")->where("art_id='".$id."'")->save($array);
     	if($rest){
     		$this->success('编辑成功',U('Links/jiemu_index'));
     	}else{
     		$this->success('编辑失败',U('Links/jiemu_index'));
     	}
     	
     }
     
     
     //删除节目
     public function jiemu_delete(){
     	$id=I("art_id");
     	$res=M("article")->where("art_id='".$id."'")->delete();
     	if($res){
     		$this->success('删除成功',U('Links/jiemu_index'));
     	}else{
     		$this->success('删除失败',U('Links/jiemu_index'));
     	}
     }
     
     
      /*批量删除
     *
     */
    public function deletejiemu(){
        foreach($_POST as $rows)
        {
            $rs=M('article')->where(array("art_id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Links/jiemu_index'));
        }else{
            $this->error('批量删除失败',U('Links/jiemu_index'));
        }
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

    
    
    
    
    
    
     //其他商品
    public function qita_index(){
    	
    	
        $Article = M('article');
        $where['cate_p_id']=85;
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
		if(empty($select1) && empty($ming)){
			  $tt['art_cate_id'] = array('in',$catep);
		}else if(!empty($select1)){
			$tt['art_cate_id'] = $select1;
			$this->select1=$select1;
		}else if(!empty($ming)){
			$tt['product_title'] = array('like','%'.$ming.'%');
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
        }
        
        $this->assign('list',$products);

        $this->assign('page',$show);

       
    	$this->display();
    }
    
    
    //添加其他商品
    public function qita_add(){
    	 //分类
        $where['cate_p_id']=85;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        
      
       $this->shu=$shu;
       //其他商品供应商
        $gong=M("gongying")->where("cate_id=85")->select();
        $this->gong=$gong;
        //其他商品服务商
        $fuwu=M("fuwu")->where("cate_id=85")->select();
        $this->fuwu=$fuwu;
        
    	$this->display();
    }
    
    public function addcl_qita(){
    	$data=I("post.");
// 	    dump($data);die;
   	    $model = M('article');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $da['art_img_url'] = $products;
        $da['art_img_url_s']=$banns1;
        $da['art_title']=$banns2;
        $da['art_keywords']=$banns3;
        $da['art_description']=$banns4;
        $da['product_title'] = I('post.art_title');//产品名称
        $da['jiage']=I("art_jiage");  //产品商城价格
        $da['huiyuan']=I("huiyuan");  //产品会员价格  111
        $da['num']=I("art_kucun");  //产品库存
        $da['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $da['guige']=I("art_shuo"); //产品说明
        $da['youdi']=I("art_you"); //邮费
        $da['art_cate_id'] = I('post.art_cate_id'); //所属
        $da['art_content'] = I('post.art_content');//纵览
        $da['content'] = I('post.content'); //费用说明
        $da['cont'] = I('post.cont'); //行程
        $da['shipin'] = I('post.shipin'); //Banner视频
        $da['xiang_shipin'] = I('post.xiang_shipin'); //详情视频111
        $da['gong'] = I('post.gong_id'); //供应商
        $da['jinjia'] = I('post.jinjia'); //进价 111
        $da['fuwu'] = I('post.fuwu'); //产品是否有服务商  1是  0 不是 111
        $da['fu_id'] = I('post.fu_id'); //服务商 111
        $da['f_money'] = I('post.f_money'); //服务商费用111
        $da['art_date'] =date('Y-m-d H:i:s',time());
        $res=$model->add($da);
         
        
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

            $this->success('添加成功',U('Links/qita_index'),1);
        }else{
            $this->error('添加失败',U('Links/qita_index'),1);
        }

    
   

    
   
    }
    
    
    //删除其他商品
    public function qita_delete(){
    	 $rs=M('article')->where(array('art_id'=>I('post.art_id')))->delete();
        if($rs){
            $this->success('删除成功',U('Links/qita_index'));
        }else{
            $this->error();
        }
    }
    
    //批量删除其他商品
    public function qita_dert(){
    	
    	  $data=I("post.");
    	      
        foreach($data as $rows)
        {
            $rs=M('article')->where(array("art_id"=>$rows))->delete();

        }
      
        if($rs){
            $this->success('批量删除成功',U('Links/qita_index'));
        }else{
            $this->error('批量删除失败',U('Links/qita_index'));
        }
    
    }
    
    
    //编辑页面
    public function qita_edit(){
    	
   	

        $obj=M('article')->where(array('art_id'=>I('get.art_id')))->find();
        
        $this->assign('obj',$obj);

        //分类显示
        $where['cate_p_id']=85;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        
        $this->assign('list2',$list2);
         
       
       
         
        //其他商品供应商
        $gong=M("gongying")->where("cate_id=85")->select();
        $this->gong=$gong;
        
        
         //其他商品服务商  qita_updata
        $fuwu=M("fuwu")->where("cate_id=85")->select();
        $this->fuwu=$fuwu;
        
        //规格
        $guige=M("guige")->where(array('cid'=>I('get.art_id')))->select();
        $this->chang = count($guige);
        $this->guige=$guige;
        $this->display();
    
   
    	
    }
    
    //其他商品编辑处理
    public function qita_updata(){
    	
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

        	$this->success('编辑成功',U('Links/qita_index'),1);
        	
        }else{
            $this->error('编辑失败',U('Links/qita_index'),1);
        }

    
    
    }
    
    
    public function siren(){
    	
     	$Article = M('siren');
        $count = $Article -> where() -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        $products = $Article -> where() -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
        	$tt=M("user")->where("id= '".$v['uid']."'")->find();
        	$products[$k]['fen']=$tt['sename']; 
        }
         
        $this->assign('list',$products);

        $this->assign('page',$show);

     	$this->display();
     
    }
    
    public function deletesiren(){
    	
    	
    	  $data=I("post.");
    	      
        foreach($data as $rows)
        {
            $rs=M('siren')->where(array("id"=>$rows))->delete();

        }
      
        if($rs){
            $this->success('批量删除成功',U('Links/siren'));
        }else{
            $this->error('批量删除失败',U('Links/siren'));
        }
    
    
    }
    
}