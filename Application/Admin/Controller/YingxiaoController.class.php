<?php
namespace Admin\Controller;

class YingxiaoController extends CommController {
     //限时购
    public function xianshi(){
    	$Article=M("yingxiao");
    	$tt['type']=1;
    	$count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        $temp=$Article->where($tt)->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
	    foreach($temp as $k=>$v){
	       	  
	       	  if( strtotime($v['ktime'])>time()){
	       	  	$temp[$k]['zhuangtai']='未开始';
	       	  }else if(strtotime($v['ktime'])<time() && strtotime($v['endtime'])> time()){
	       	  		$temp[$k]['zhuangtai']='进行中';
	       	  }else if(strtotime($v['endtime'])< time()){
	       	  	$temp[$k]['zhuangtai']='已结束';
	       	  }
	    }
        $this->assign('temp',$temp);

        $this->assign('page',$show);

        $this->display = M('display')->where(['id' => 1])->find();
      
   	    $this->display();
    }
    //添加限时购专场
    public function add(){
    	$this->display();
    }
    //添加限时购专场 处理
    public function addcl(){
    	$data=I("post.");
    	
    	$arr['name']=$data['art_title'];
    	$arr['ktime']=$data['ktime'];
    	$arr['endtime']=$data['endtime'];
    	$arr['type']='1';
    	$arr['time']=date('Y-m-d H:i:s',time());
    	$res=M("yingxiao")->add($arr);
    	if($res){
    		 $this->success('添加成功',U('Yingxiao/xianshi'),1);
    	}else{
    		 $this->success('添加失败',U('Yingxiao/xianshi'),1);
    	}
    }
    
     //删除限时购专场
    public function delete_t(){
    	$id=I("id");
    	$res=M("yingxiao")->where("id='".$id."'")->delete();
    	if($res){
    		 $this->success('删除成功',U('Yingxiao/xianshi'),1);
    	}else{
    		 $this->success('删除失败',U('Yingxiao/xianshi'),1);
    	}
    }
    //删除限时购专场  全选
    public function delete(){
    	$data=I("post.");
    	foreach($data as $k=>$v){
    		 $res=M("yingxiao")->where("id='".$v."'")->delete();
    	}
    	 $this->success('删除成功',U('Yingxiao/xianshi'),1);
    }
    
    //专场产品列表
    public function edit(){
    	$id=I("id");
    	$temp=M("yingxiao")->where("id='".$id."'")->find();
    	$this->temp=$temp;
        $store=M("yingxiao_store")->where("chang_id='".$id."'")->select();
        foreach($store as $k=>$v){
            $tem=M("article")->where("art_id='".$v['store_id']."'")->find();
            $store[$k]['store_name']=$tem['product_title'];
            
            $tt=M("cate")->where("cate_id= '".$tem['art_cate_id']."'")->find();
            $store[$k]['fen']=$tt['cate_name']; 
            
            $gong=M("gongying")->where("id='".$tem['gong']."'")->find();
            $store[$k]['gong']=$gong['name'];        	
        }
 	    $this->store=$store;
    	
    	$this->display();
    }
    
    // 获取产品规格
    public function getGuige(){
        $art_id = I('art_id/d');
        if (empty($art_id)) {
            $this->ajaxReturn(['state' => 0, 'msg' => '参数缺失！']);
        } else {
            $guige = M('guige')->where(['cid' => $art_id])->select();
            if ($guige) {
                $this->ajaxReturn(['state' => 1, 'data' => $guige, 'tiannum' => count($guige)]);
            } else {
                $this->ajaxReturn(['state' => 0, 'msg' => '规格数据不存在！']);
            }
        }
    }

    //添加专场产品
    public function addstpre(){
    	$id=I("id");//专场id
    	$this->ids=$id;
    	//车产品
    	$cate=M("cate")->where("cate_p_id=18 || cate_p_id=85 || cate_p_id=102")->getField('cate_id',true);
    	$where['art_cate_id']=array('in',$cate);
    	$where['type']=1;
    	$store=M("article")->where($where)->select();
    	$this->store=$store;
    	$this->display();
    }
    
     //添加专场产品 处理
    public function xianshi_store(){
    	$data=I("post.");

    	$ids = $data['ids'];
        $cid = $data['art_id'];
        if (!isset($cid) || empty($cid)) {
            $this->error('请选择商品！');die();
        }
    	// 专场
    	$temp = M("yingxiao")->where(['id' => $ids])->find();
    	$res = M("yingxiao_store")->add([
            'store_id' => $cid,
            'chang_id' => $ids,
            'time' => date('Y-m-d H:i:s',time())
        ]);
    	if($res){

            /****处理产品规格值*****/
            $guige = M('guige')->where(['cid' => $cid])->delete();
            if(!empty($data['gui1'])|| !empty($data['jia1']) || !empty($data['num1']) || !empty($data['ying1'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui1'],
                    'num'=>$data['num1'],
                    'money'=>$data['jia1'],
                    'money_huiyuan'=>$data['huiyuan1'],
                    'money_jin'=>$data['jin1'],
                    'money_you'=>$data['you1'],
                    'money_ying'=>$data['ying1']
                ]);  
            }

            if(!empty($data['gui2'])|| !empty($data['jia2']) || !empty($data['num2']) || !empty($data['ying2'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui2'],
                    'num'=>$data['num2'],
                    'money'=>$data['jia2'],
                    'money_huiyuan'=>$data['huiyuan2'],
                    'money_jin'=>$data['jin2'],
                    'money_you'=>$data['you2'],
                    'money_ying'=>$data['ying2']
                ]);  
            }

            if(!empty($data['gui3'])|| !empty($data['jia3']) || !empty($data['num3']) || !empty($data['ying3'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui3'],
                    'num'=>$data['num3'],
                    'money'=>$data['jia3'],
                    'money_huiyuan'=>$data['huiyuan3'],
                    'money_jin'=>$data['jin3'],
                    'money_you'=>$data['you3'],
                    'money_ying'=>$data['ying3']
                ]);  
            }

            if(!empty($data['gui4'])|| !empty($data['jia4']) || !empty($data['num4']) || !empty($data['ying4'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui4'],
                    'num'=>$data['num4'],
                    'money'=>$data['jia4'],
                    'money_huiyuan'=>$data['huiyuan4'],
                    'money_jin'=>$data['jin4'],
                    'money_you'=>$data['you4'],
                    'money_ying'=>$data['ying4']
                ]);  
            }

            if(!empty($data['gui5'])|| !empty($data['jia5']) || !empty($data['num5']) || !empty($data['ying5'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui5'],
                    'num'=>$data['num5'],
                    'money'=>$data['jia5'],
                    'money_huiyuan'=>$data['huiyuan5'],
                    'money_jin'=>$data['jin5'],
                    'money_you'=>$data['you5'],
                    'money_ying'=>$data['ying5']
                ]);  
            }

            if(!empty($data['gui6'])|| !empty($data['jia6']) || !empty($data['num6']) || !empty($data['ying6'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui6'],
                    'num'=>$data['num6'],
                    'money'=>$data['jia6'],
                    'money_huiyuan'=>$data['huiyuan6'],
                    'money_jin'=>$data['jin6'],
                    'money_you'=>$data['you6'],
                    'money_ying'=>$data['ying6']
                ]);  
            }

            if(!empty($data['gui7'])|| !empty($data['jia7']) || !empty($data['num7']) || !empty($data['ying7'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui7'],
                    'num'=>$data['num7'],
                    'money'=>$data['jia7'],
                    'money_huiyuan'=>$data['huiyuan7'],
                    'money_jin'=>$data['jin7'],
                    'money_you'=>$data['you7'],
                    'money_ying'=>$data['ying7']
                ]);  
            }

            if(!empty($data['gui8'])|| !empty($data['jia8']) || !empty($data['num8']) || !empty($data['ying8'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui8'],
                    'num'=>$data['num8'],
                    'money'=>$data['jia8'],
                    'money_huiyuan'=>$data['huiyuan8'],
                    'money_jin'=>$data['jin8'],
                    'money_you'=>$data['you8'],
                    'money_ying'=>$data['ying8']
                ]);  
            }

    		$this->redirect('Yingxiao/edit', ['id' => $ids]);
    	}else{
    		$this->redirect('Yingxiao/edit', ['id' => $ids]);
    	}
    }

    //编辑专场产品
    public function bian(){
    	$id=I("id");
    	$temp=M("yingxiao_store")->where(['id' => $id])->find();
    	$this->temp=$temp;
    	//车产品
    	$cate=M("cate")->where("cate_p_id=18")->getField('cate_id',true);
    	$where['art_cate_id']=array('in',$cate);
    	$where['type']=1;
    	$store=M("article")->where($where)->select();
    	$this->store=$store;
    	$this->display();
    	
    }
     //编辑处理
    public function xianshi_bian(){
    	$data=I("post.");
    	$id=$data['ids'];
        $cid = $data['art_id'];
        if (!isset($cid) || empty($cid)) {
            $this->error('请选择商品！');die;
        }

    	$ten=M("yingxiao_store")->where(['id' => $id])->find();
    	$chang_id=$ten['chang_id'];
    	$res = M("yingxiao_store")->where(['id' => $id])->save([
            'store_id' => $cid,
            'time' => date('Y-m-d H:i:s',time())
        ]);
    	if($res){
            /****处理产品规格值*****/
            $guige = M('guige')->where(['cid' => $cid])->delete();
            if(!empty($data['gui1'])|| !empty($data['jia1']) || !empty($data['num1']) || !empty($data['ying1'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui1'],
                    'num'=>$data['num1'],
                    'money'=>$data['jia1'],
                    'money_huiyuan'=>$data['huiyuan1'],
                    'money_jin'=>$data['jin1'],
                    'money_you'=>$data['you1'],
                    'money_ying'=>$data['ying1']
                ]);  
            }

            if(!empty($data['gui2'])|| !empty($data['jia2']) || !empty($data['num2']) || !empty($data['ying2'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui2'],
                    'num'=>$data['num2'],
                    'money'=>$data['jia2'],
                    'money_huiyuan'=>$data['huiyuan2'],
                    'money_jin'=>$data['jin2'],
                    'money_you'=>$data['you2'],
                    'money_ying'=>$data['ying2']
                ]);  
            }

            if(!empty($data['gui3'])|| !empty($data['jia3']) || !empty($data['num3']) || !empty($data['ying3'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui3'],
                    'num'=>$data['num3'],
                    'money'=>$data['jia3'],
                    'money_huiyuan'=>$data['huiyuan3'],
                    'money_jin'=>$data['jin3'],
                    'money_you'=>$data['you3'],
                    'money_ying'=>$data['ying3']
                ]);  
            }

            if(!empty($data['gui4'])|| !empty($data['jia4']) || !empty($data['num4']) || !empty($data['ying4'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui4'],
                    'num'=>$data['num4'],
                    'money'=>$data['jia4'],
                    'money_huiyuan'=>$data['huiyuan4'],
                    'money_jin'=>$data['jin4'],
                    'money_you'=>$data['you4'],
                    'money_ying'=>$data['ying4']
                ]);  
            }

            if(!empty($data['gui5'])|| !empty($data['jia5']) || !empty($data['num5']) || !empty($data['ying5'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui5'],
                    'num'=>$data['num5'],
                    'money'=>$data['jia5'],
                    'money_huiyuan'=>$data['huiyuan5'],
                    'money_jin'=>$data['jin5'],
                    'money_you'=>$data['you5'],
                    'money_ying'=>$data['ying5']
                ]);  
            }

            if(!empty($data['gui6'])|| !empty($data['jia6']) || !empty($data['num6']) || !empty($data['ying6'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui6'],
                    'num'=>$data['num6'],
                    'money'=>$data['jia6'],
                    'money_huiyuan'=>$data['huiyuan6'],
                    'money_jin'=>$data['jin6'],
                    'money_you'=>$data['you6'],
                    'money_ying'=>$data['ying6']
                ]);  
            }

            if(!empty($data['gui7'])|| !empty($data['jia7']) || !empty($data['num7']) || !empty($data['ying7'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui7'],
                    'num'=>$data['num7'],
                    'money'=>$data['jia7'],
                    'money_huiyuan'=>$data['huiyuan7'],
                    'money_jin'=>$data['jin7'],
                    'money_you'=>$data['you7'],
                    'money_ying'=>$data['ying7']
                ]);  
            }

            if(!empty($data['gui8'])|| !empty($data['jia8']) || !empty($data['num8']) || !empty($data['ying8'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui8'],
                    'num'=>$data['num8'],
                    'money'=>$data['jia8'],
                    'money_huiyuan'=>$data['huiyuan8'],
                    'money_jin'=>$data['jin8'],
                    'money_you'=>$data['you8'],
                    'money_ying'=>$data['ying8']
                ]);  
            }

    		$this->redirect('Yingxiao/edit', array('id' => $chang_id));
    	}else{
    		$this->redirect('Yingxiao/edit', array('id' => $chang_id));
    	}
    }

    // 删除专场产品
    public function bian_shan(){
        $id=I("id");
        $ten=M("yingxiao_store")->where(['id' => $id])->find();
        $ids=$ten['chang_id'];
        $res=M("yingxiao_store")->where(['id' => $id])->delete();
        if($res){
            $this->redirect('Yingxiao/edit', array('id' => $ids));
        }else{
            $this->redirect('Yingxiao/edit', array('id' => $ids));
        }
    }
    //删除专场产品
    public function bian_dele(){
    	$data=I("post.");
    	$chang_id=I("chang_id");
    	unset ($data['chang_id']);
    	
    	foreach($data as $k=>$v){
    		$res=M("yingxiao_store")->where("id='".$v."'")->delete();
    	}
    	$this->redirect('Yingxiao/edit', array('id' => $chang_id));
    }

     //首页显示
    public function disp(){
    	$res=M("display")->where("id=1")->save(array('type'=>1));
    	$this->success('操作成功',U('Yingxiao/xianshi'),1);
    }
    
     //首页隐藏
    public function noner(){
    	$res=M("display")->where("id=1")->save(array('type'=>0));
    	$this->success('操作成功',U('Yingxiao/xianshi'),1);
    }

     //秒杀
    public function miaosha(){
    	
    	
    	$Article=M("yingxiao");
    	$tt['type']=2;
    	$count = $Article -> where($tt) -> count();   
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        $temp=$Article->where($tt)->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
	    foreach($temp as $k=>$v){
	       	  if( strtotime($v['ktime'])<time()){
	       	  	$temp[$k]['zhuangtai']='已开始 ';
	       	  }else{
	       	  	$temp[$k]['zhuangtai']='未开始';
	       	  }
	    }

        $this->assign('temp',$temp);


        $this->assign('page',$show);
      
   	   $this->display = M('display')->where(['id' => 2])->find();
   	 
   	  $this->display();
    }
    
     //添加整点秒杀
    public function miao_add(){
    	$this->display();
    }
    //添加整点秒杀  处理
    public function miao_addcl(){
    	$data=I("post.");
    	$arr['name']=$data['art_title'];
    	$arr['ktime']=$data['ktime'];
    	$arr['type']='2';
			$arr['beizhu']=$data['beizhu'];
    	$arr['time']=date('Y-m-d H:i:s',time());
    	$res=M("yingxiao")->add($arr);
    	if($res){
    		 $this->success('添加成功',U('Yingxiao/miaosha'),1);
    	}else{
    		 $this->success('添加失败',U('Yingxiao/miaosha'),1);
    	}
    	
    }
     //删除整点秒杀
    public function miao_delete(){
    	
    	$id=I("id");
    	$res=M("yingxiao")->where("id='".$id."'")->delete();
    	if($res){
    		 $this->success('删除成功',U('Yingxiao/miaosha'),1);
    	}else{
    		 $this->success('删除失败',U('Yingxiao/miaosha'),1);
    	}
    
    }
     //删除整点秒杀  全选
    public function miao_det(){
    	
    	$data=I("post.");
    	foreach($data as $k=>$v){
    		 $res=M("yingxiao")->where("id='".$v."'")->delete();
    	}
    	$this->success('删除成功',U('Yingxiao/miaosha'),1);
    
    }
    
    //删除整点秒杀 产品列表
    public function miao_edit(){
    	$id=I("id");
    	$temp=M("yingxiao")->where("id='".$id."'")->find();
    	$this->temp=$temp;
        $store=M("yingxiao_store")->where("chang_id='".$id."'")->select();
        foreach($store as $k=>$v){
           $tem=M("article")->where("art_id='".$v['store_id']."'")->find();
           $store[$k]['store_name']=$tem['product_title'];

            $tt=M("cate")->where("cate_id= '".$tem['art_cate_id']."'")->find();
            $store[$k]['fen']=$tt['cate_name']; 
            
            $gong=M("gongying")->where("id='".$tem['gong']."'")->find();
            $store[$k]['gong']=$gong['name'];    
        }
 	    $this->store=$store;
    	
    	$this->display();
    
    }
    
    //添加整点秒杀产品
    public function miao_trest(){
    	
    	$id=I("id");//专场id
    	$this->ids=$id;
    	//车产品
    	$cate=M("cate")->where("cate_p_id=18 || cate_p_id=85 || cate_p_id=102")->getField('cate_id',true);
    	$where['art_cate_id']=array('in',$cate);
    	$where['type']=1;
    	$store=M("article")->where($where)->select();
    	$this->store=$store;
    	$this->display();
    }
     //添加整点秒杀产品
    public function miao_ooper(){
    	$data=I("post.");

    	$ids=$data['ids'];
        $cid = $data['art_id'];
        if (!isset($cid) || empty($cid)) {
            $this->error('请选择商品！');die();
        }
    	//专场
    	$temp=M("yingxiao")->where("id='".$ids."'")->find();

        $arr['store_id']=$data['art_id'];
    	$arr['chang_id']=$ids;
    	$arr['time']=date('Y-m-d H:i:s',time());
    	$res=M("yingxiao_store")->add($arr);
    	if($res){

            /****处理产品规格值*****/
            $guige = M('guige')->where(['cid' => $cid])->delete();
            if(!empty($data['gui1'])|| !empty($data['jia1']) || !empty($data['num1']) || !empty($data['ying1'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui1'],
                    'num'=>$data['num1'],
                    'money'=>$data['jia1'],
                    'money_huiyuan'=>$data['huiyuan1'],
                    'money_jin'=>$data['jin1'],
                    'money_you'=>$data['you1'],
                    'money_ying'=>$data['ying1']
                ]);  
            }

            if(!empty($data['gui2'])|| !empty($data['jia2']) || !empty($data['num2']) || !empty($data['ying2'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui2'],
                    'num'=>$data['num2'],
                    'money'=>$data['jia2'],
                    'money_huiyuan'=>$data['huiyuan2'],
                    'money_jin'=>$data['jin2'],
                    'money_you'=>$data['you2'],
                    'money_ying'=>$data['ying2']
                ]);  
            }

            if(!empty($data['gui3'])|| !empty($data['jia3']) || !empty($data['num3']) || !empty($data['ying3'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui3'],
                    'num'=>$data['num3'],
                    'money'=>$data['jia3'],
                    'money_huiyuan'=>$data['huiyuan3'],
                    'money_jin'=>$data['jin3'],
                    'money_you'=>$data['you3'],
                    'money_ying'=>$data['ying3']
                ]);  
            }

            if(!empty($data['gui4'])|| !empty($data['jia4']) || !empty($data['num4']) || !empty($data['ying4'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui4'],
                    'num'=>$data['num4'],
                    'money'=>$data['jia4'],
                    'money_huiyuan'=>$data['huiyuan4'],
                    'money_jin'=>$data['jin4'],
                    'money_you'=>$data['you4'],
                    'money_ying'=>$data['ying4']
                ]);  
            }

            if(!empty($data['gui5'])|| !empty($data['jia5']) || !empty($data['num5']) || !empty($data['ying5'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui5'],
                    'num'=>$data['num5'],
                    'money'=>$data['jia5'],
                    'money_huiyuan'=>$data['huiyuan5'],
                    'money_jin'=>$data['jin5'],
                    'money_you'=>$data['you5'],
                    'money_ying'=>$data['ying5']
                ]);  
            }

            if(!empty($data['gui6'])|| !empty($data['jia6']) || !empty($data['num6']) || !empty($data['ying6'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui6'],
                    'num'=>$data['num6'],
                    'money'=>$data['jia6'],
                    'money_huiyuan'=>$data['huiyuan6'],
                    'money_jin'=>$data['jin6'],
                    'money_you'=>$data['you6'],
                    'money_ying'=>$data['ying6']
                ]);  
            }

            if(!empty($data['gui7'])|| !empty($data['jia7']) || !empty($data['num7']) || !empty($data['ying7'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui7'],
                    'num'=>$data['num7'],
                    'money'=>$data['jia7'],
                    'money_huiyuan'=>$data['huiyuan7'],
                    'money_jin'=>$data['jin7'],
                    'money_you'=>$data['you7'],
                    'money_ying'=>$data['ying7']
                ]);  
            }

            if(!empty($data['gui8'])|| !empty($data['jia8']) || !empty($data['num8']) || !empty($data['ying8'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui8'],
                    'num'=>$data['num8'],
                    'money'=>$data['jia8'],
                    'money_huiyuan'=>$data['huiyuan8'],
                    'money_jin'=>$data['jin8'],
                    'money_you'=>$data['you8'],
                    'money_ying'=>$data['ying8']
                ]);  
            }
    		$this->redirect('Yingxiao/miao_edit', array('id' => $ids));
    	}else{
    		$this->redirect('Yingxiao/miao_edit', array('id' => $ids));
    	}
    
    }
    
    //编辑秒杀产品
    public function miao_bianji(){
    	
    	$id=I("id");
    	$temp=M("yingxiao_store")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	//车产品
    	$cate=M("cate")->where("cate_p_id=18")->getField('cate_id',true);
    	$where['art_cate_id']=array('in',$cate);
    	$where['type']=1;
    	$store=M("article")->where($where)->select();
    	$this->store=$store;
    	$this->display();
    	
    
    }
    //编辑秒杀产品
    public function miao_bure(){
    	
    	$data=I("post.");
    	$id=$data['ids'];
        $cid = $data['art_id'];
        if (!isset($cid) || empty($cid)) {
            $this->error('请选择商品！');die;
        }
    	$ten=M("yingxiao_store")->where("id='".$id."'")->find();
    	$chang_id=$ten['chang_id'];

        $res = M("yingxiao_store")->where(['id' => $id])->save([
            'store_id' => $cid,
            'time' => date('Y-m-d H:i:s',time())
        ]);
    	if($res){
            /****处理产品规格值*****/
            $guige = M('guige')->where(['cid' => $cid])->delete();
            if(!empty($data['gui1'])|| !empty($data['jia1']) || !empty($data['num1']) || !empty($data['ying1'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui1'],
                    'num'=>$data['num1'],
                    'money'=>$data['jia1'],
                    'money_huiyuan'=>$data['huiyuan1'],
                    'money_jin'=>$data['jin1'],
                    'money_you'=>$data['you1'],
                    'money_ying'=>$data['ying1']
                ]);  
            }

            if(!empty($data['gui2'])|| !empty($data['jia2']) || !empty($data['num2']) || !empty($data['ying2'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui2'],
                    'num'=>$data['num2'],
                    'money'=>$data['jia2'],
                    'money_huiyuan'=>$data['huiyuan2'],
                    'money_jin'=>$data['jin2'],
                    'money_you'=>$data['you2'],
                    'money_ying'=>$data['ying2']
                ]);  
            }

            if(!empty($data['gui3'])|| !empty($data['jia3']) || !empty($data['num3']) || !empty($data['ying3'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui3'],
                    'num'=>$data['num3'],
                    'money'=>$data['jia3'],
                    'money_huiyuan'=>$data['huiyuan3'],
                    'money_jin'=>$data['jin3'],
                    'money_you'=>$data['you3'],
                    'money_ying'=>$data['ying3']
                ]);  
            }

            if(!empty($data['gui4'])|| !empty($data['jia4']) || !empty($data['num4']) || !empty($data['ying4'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui4'],
                    'num'=>$data['num4'],
                    'money'=>$data['jia4'],
                    'money_huiyuan'=>$data['huiyuan4'],
                    'money_jin'=>$data['jin4'],
                    'money_you'=>$data['you4'],
                    'money_ying'=>$data['ying4']
                ]);  
            }

            if(!empty($data['gui5'])|| !empty($data['jia5']) || !empty($data['num5']) || !empty($data['ying5'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui5'],
                    'num'=>$data['num5'],
                    'money'=>$data['jia5'],
                    'money_huiyuan'=>$data['huiyuan5'],
                    'money_jin'=>$data['jin5'],
                    'money_you'=>$data['you5'],
                    'money_ying'=>$data['ying5']
                ]);  
            }

            if(!empty($data['gui6'])|| !empty($data['jia6']) || !empty($data['num6']) || !empty($data['ying6'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui6'],
                    'num'=>$data['num6'],
                    'money'=>$data['jia6'],
                    'money_huiyuan'=>$data['huiyuan6'],
                    'money_jin'=>$data['jin6'],
                    'money_you'=>$data['you6'],
                    'money_ying'=>$data['ying6']
                ]);  
            }

            if(!empty($data['gui7'])|| !empty($data['jia7']) || !empty($data['num7']) || !empty($data['ying7'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui7'],
                    'num'=>$data['num7'],
                    'money'=>$data['jia7'],
                    'money_huiyuan'=>$data['huiyuan7'],
                    'money_jin'=>$data['jin7'],
                    'money_you'=>$data['you7'],
                    'money_ying'=>$data['ying7']
                ]);  
            }

            if(!empty($data['gui8'])|| !empty($data['jia8']) || !empty($data['num8']) || !empty($data['ying8'])){
                M("guige")->add([
                    'cid'=>$cid,
                    'guige'=>$data['gui8'],
                    'num'=>$data['num8'],
                    'money'=>$data['jia8'],
                    'money_huiyuan'=>$data['huiyuan8'],
                    'money_jin'=>$data['jin8'],
                    'money_you'=>$data['you8'],
                    'money_ying'=>$data['ying8']
                ]);  
            }
    		$this->redirect('Yingxiao/miao_edit', array('id' => $chang_id));
    	}else{
    		$this->redirect('Yingxiao/miao_edit', array('id' => $chang_id));
    	}
    
    }
    
    //删除秒杀产品
    public function miao_shanchu(){
    	
    	$id=I("id");
    	$ten=M("yingxiao_store")->where("id='".$id."'")->find();
    	$ids=$ten['chang_id'];
    	$res=M("yingxiao_store")->where("id='".$id."'")->delete();
    	if($res){
    		$this->redirect('Yingxiao/miao_edit', array('id' => $ids));
    	}else{
    		$this->redirect('Yingxiao/miao_edit', array('id' => $ids));
    	}
    
    }
    
    //删除秒杀产品  全选
    public function miao_typrt(){
    	
    	$data=I("post.");
    	$chang_id=I("chang_id");
    	unset ($data['chang_id']);
    	
    	foreach($data as $k=>$v){
    		$res=M("yingxiao_store")->where("id='".$v."'")->delete();
    	}
    	$this->redirect('Yingxiao/miao_edit', array('id' => $chang_id));
    
    }
    
    //首页显示
    public function miao_disp(){
    	$res=M("display")->where("id=2")->save(array('type'=>1));
    	$this->success('操作成功',U('Yingxiao/miaosha'),1);
    }
    
    //首页隐藏
    public function miao_noner(){
    	$res=M("display")->where("id=2")->save(array('type'=>0));
    	$this->success('操作成功',U('Yingxiao/miaosha'),1);
    }

}