<?php
namespace Admin\Controller;
use Think\Controller;
class CasesManagementController extends CommController {

    /* 列表
     *
     */
    public function index(){
    	$slect1=I("select1");
         $this->slect1 = $slect1;
    	 $search=I("search");
         $this->search = $search;
    	if(empty($slect1)){
    		 $where=array();
             $where['ster'] = 1;
    		 $where['tuikuan'] = 0;
    		 if(!empty($search)){
    		 	$where['paysn']=$search;
    		 }
    		$Article = M('dingdan');
	        $count = $Article -> where($where) -> count();
	        $Page = new \Think\Page($count,20);
	        $show = $Page -> show();
	        $news = $Article -> where($where) -> order('zhifu_time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
	         foreach($news as $k=>$v){
	         	$name=M("user")->where("id='".$v['uid']."'")->find();
	         	$news[$k]['user']=$name['sename'];
	         	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
	         	$tt=M("cate")->where("cate_id= '".$store['art_cate_id']."'")->find();
	        	$news[$k]['fen']=$tt['cate_name']; 
	         }
	        $this->assign('list',$news);
	        $this->assign('page',$show);
    	}else{
    		  if($slect1=='hid'){ // 活动订单
		    	$catep=M("huodong")->where()->getField('id',true);
		     	if(!empty($catep)){
		     		$where = array();
			        $where['h_id'] = array('in',$catep);
			        $where['ster'] = 1;
                    $where['tuikuan'] = 0;
			         if(!empty($search)){
		    		 	$where['paysn']=$search;
		    		 }
			        $count = M("dingdan") -> where($where) -> count();   
			        $Page = new \Think\Page($count,20);
			        $show = $Page -> show();
			        $Article = M('dingdan');
			        $news = $Article -> where($where) -> order('zhifu_time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
			         foreach($news as $k=>$v){
			         	$name=M("user")->where("id='".$v['uid']."'")->find();
			         	$news[$k]['user']=$name['sename'];
			         	
			        	$news[$k]['fen']='活动'; 
			         }
			        $this->assign('list',$products);
			        $this->assign('page',$show);
		     	}
    	
    		 }else if($slect1=='81'){
    		 	    $t = array();
                    $t['art_cate_id'] =$slect1 ;
    		 	    $cate=M("article")->where($t)->getField('art_id',true); //保险所有的产品id集合
    		 	    
    		 	    $where = array();
                    $where['store_id'] = array('in',$cate);
		    		$where['ster'] = 1;
                    $where['tuikuan'] = 0;
		    		 if(!empty($search)){
		    		 	$where['paysn']=$search;
		    		 }
		    		$Article = M('dingdan');
			        $count = $Article -> where($where) -> count();
			        $Page = new \Think\Page($count,20);
			        $show = $Page -> show();
			        $news = $Article -> where($where) -> order('zhifu_time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
			       
			         foreach($news as $k=>$v){
			         	$name=M("user")->where("id='".$v['uid']."'")->find();
			         	$news[$k]['user']=$name['username'];
			         	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
			         	$tt=M("cate")->where("cate_id= '".$store['art_cate_id']."'")->find();
			        	$news[$k]['fen']=$tt['cate_name']; 
			         }
			         
			        $this->assign('list',$news);
			
			        $this->assign('page',$show);
    		 }else{
    		  	   
		    		$Article = M('dingdan');
		    		$catep=M("cate")->where("cate_p_id='".$slect1."'")->getField('cate_id',true);
                    $t = array();
                    $t['art_cate_id'] = array('in',$catep);
                    $cate=M("article")->where($t)->getField('art_id',true); //该分类所有的产品id集合
                   
		            $where = array();
                    $where['store_id'] = array('in',$cate);
		    		$where['ster'] = 1;
                    $where['tuikuan'] = 0;
		    		 if(!empty($search)){
		    		 	$where['paysn']=$search;
		    		 }
			        $count = $Article -> where($where) -> count();
			        $Page = new \Think\Page($count,20);
			        $show = $Page -> show();
			        $news = $Article -> where($where) -> order('zhifu_time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
			         foreach($news as $k=>$v){
			         	$name=M("user")->where("id='".$v['uid']."'")->find();
			         	$news[$k]['user']=$name['username'];
			         	$store=M("article")->where("art_id='".$v['store_id']."'")->find();
			         	$tt=M("cate")->where("cate_id= '".$store['art_cate_id']."'")->find();
			        	$news[$k]['fen']=$tt['cate_name']; 
			         }
			         
			        $this->assign('list',$news);
			
			        $this->assign('page',$show);
		    	
		    	}
		    	
		    	
    	}

        $zong=M('dingdan')->where(['ster' => 1, 'tuikuan' => 0])->count();
        $classify = M('cate')->where(['cate_p_id' => 0])->select(['cate_id', 'cate_name']);
        $this->assign('classify', $classify);
        $this->zong=$zong;
        $this->display();
    }
    
    public function zong(){
    	 $num=M('dingdan')->where("ster=1")->count();
    	 $zong=I("zong");
    	 if($num > $zong){
    	 	$this->ajaxReturn(array('state'=>'1'));
    	 }else{
    	 	$this->ajaxReturn(array('state'=>'0'));
    	 }
    }
    
    
    /**
     * 发货
     * 
     */
    public function fahuo(){
    	 $id=I("id");
    	 $temp=M("dingdan")->where("id='".$id."'")->find();
    	 $shuohuo=M("address")->where("uid='".$temp['uid']."' and id='".$temp['addressid']."'")->find();
    	 $this->wu=$shuohuo;
    	  $this->temp=$temp;
    	  
    	$this->display();
    }
    
    
    
    public function ding_gai(){
    	$id=I("did");
    	$wuliu=I("wuliu");
    	 $gong=I("gong");
    	$arr=array(
    	   'ster'=>2,
    	   'wuliudan'=>$wuliu,
    	   'wuliu_gongsi'=>$gong,
    	   'wuliu_time'=>date('Y-m-d H:i:s',time())
    	);
    	$res=M("dingdan")->where("id='".$id."'")->save($arr);
    	if($res){
    		$this->success('发货成功',U('CasesManagement/index'),1);
    	}
    }
    
    /**
     * 一键发货
     * 
     */
    public function fahuojian(){
    	 $id=I("id");
         $arr = [
            'ster' => 2,
            'wuliu_time' => date('Y-m-d H:i:s', time())
         ];
    	 $temp = M('Dingdan')->where(['id' => $id])->save($arr);
    	 if ($temp) {
    	 	$this->success('发货成功', U('CasesManagement/index'), 1);
    	 }
    }
    
    /**
     *  订单查询
     * 
     */
     public function cha(){
     	$Article = M('dingdan');
     	$where=array();
     	$where['ster']='2';
        $search=I("search");
        $this->search = $search;
        if(!empty($search)){
        	$where['paysn']=$search;
        }
        $count = $Article -> where($where) -> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $news = $Article -> where($where) -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($news as $k =>$v){
         	if(empty($v['wuliudan'])){
         		$news[$k]['zhuangtai']='一键发货';
         	}else{
         		$news[$k]['zhuangtai']=$v['wuliudan'];
         	}
         	if(empty($v['store_id'])){
         		$news[$k]['fen']='城会玩';
         	}else{
         		$store=M("article")->where("art_id='".$v['store_id']."'")->find();
        		$lei=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
        		$news[$k]['fen']=$lei['cate_name'];
         	}
         	$tt=M("user")->where("id= '".$v['uid']."'")->find();
        	$news[$k]['user']=$tt['username']; 
         	$news[$k]['username']=$tt['sename']; 
         }
         
        $this->assign('list',$news);

        $this->assign('page',$show);

        
     	$this->display();
     }
     
     
     
     
     public function shouhuo(){
     	
     	
     	$Article = M('dingdan');
        $where=array();
     	$where['ster']='3';
        $search=I("search");
        $this->search = $search;
        if(!empty($search)){
        	$where['paysn']=$search;
        }
        $count = $Article -> where($where) -> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $news = $Article -> where($where) -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($news as $k =>$v){
         	if(empty($v['wuliudan'])){
         		$news[$k]['zhuangtai']='一键发货';
         	}else{
         		$news[$k]['zhuangtai']=$v['wuliudan'];
         	}
         	if(empty($v['store_id'])){
         		$news[$k]['fen']='城会玩';
         	}else{
         		$store=M("article")->where("art_id='".$v['store_id']."'")->find();
        		$lei=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
        		$news[$k]['fen']=$lei['cate_name'];
         	}
         	$tt=M("user")->where("id= '".$v['uid']."'")->find();
        	$news[$k]['user']=$tt['username']; 
         	$news[$k]['username']=$tt['sename']; 
         }
         
        $this->assign('list',$news);

        $this->assign('page',$show);

        
     	
     
           
     	$this->display();
     }
     
     
     
     
     public function jieshu(){
     	$id=I("id");
     	$res=M("dingdan")->where("id='".$id."'")->save(array('ster'=>4));
     	//发放积分
     	$temp=M("dingdan")->where("id='".$id."'")->find();
     	$uid=$temp['uid'];
     	$name=M("user")->where("id='".$uid."'")->find();
     	$fen=$name['jifen'];
     	$jifen=$fen+$temp['zong'];
     	$jifen_xin=(int)$jifen;
     	$ret=M("user")->where("id='".$uid."'")->save(array('jifen'=>$jifen_xin));
     	$this->success('操作成功',U('CasesManagement/shouhuo'),1);
     }
     
     
     
    public function daochu(){
    	$Article = M('dingdan');

        $count = $Article -> where("ster=2") -> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $news = $Article -> where("ster=2") -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($news as $k =>$v){
         	if(empty($v['wuliudan'])){
         		$news[$k]['zhuangtai']='一键发货';
         	}else{
         		$news[$k]['zhuangtai']=$v['wuliudan'];
         	}
         	if(empty($v['store_id'])){
         		$news[$k]['fen']='城会玩';
         	}else{
         		$store=M("article")->where("art_id='".$v['store_id']."'")->find();
        		$lei=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
        		$news[$k]['fen']=$lei['cate_name'];
         	}
         	$tt=M("user")->where("id= '".$v['uid']."'")->find();
        	$news[$k]['user']=$tt['username']; 
         	$news[$k]['sename']=$tt['sename']; 
         	
         }
          
        foreach($news as $k=>$v){
        	 $news[$k]['pay']=' '.$v['paysn'].' ';
        	 $new[$k]['daotime']=date('Y-m-d H:i:s',time());
        	 $news[$k]['shouru']=$v['jiage']*$v['num'];
        	 $store=M("article")->where("art_id='".$v['store_id']."'")->find();
        	 $news[$k]['you']=$store['youdi'];
        } 
        
        $this->goods_export($news);
    } 
     
    function goods_export($goods_list=array()){
//    dump($goods_list);
//    exit;  //导出数据
       
        $data = array();
        foreach ($goods_list as $k=>$goods_info){
           // $data[$k][id] = $goods_info['id'];   //序号
            $data[$k][pay] = $goods_info['pay'];//订单号
            $data[$k][storename] = $goods_info['name'];  //产品名称
            $data[$k][zhifu_time] = $goods_info['zhifu_time'];   //支付时间
            $data[$k][user] = $goods_info['user'];  //下单人手机号
            $data[$k][sename] = $goods_info['sename'];  //下单人姓名
            $data[$k][shouru] = $goods_info['shouru'];  //收入金额
            $data[$k][zhekou]='0'; //折扣金额
            $data[$k][shishou]=$goods_info['zong'];; //实收金额 
            $data[$k][you]=$goods_info['you']; //邮费
            $data[$k][qu]='微信支付';//交易渠道
            $data[$k][fen] = $goods_info['fen'];//所属类型
          //  $data[$k][tuikuan] = $goods_info['tuikuan'];//状态
            $data[$k][daotime] = $goods_info['daotime'];//导出时间
            

			
        }

       

        foreach ($data as $field=>$v){
            
            if($field == 'pay'){
                $headArr[]='订单号';
            }
            if($field == 'storename'){
                $headArr[]='产品名称';
            }
            if($field == 'zhifu_time'){
                $headArr[]='支付时间';
            }
            if($field == 'user'){
                $headArr[]='下单人手机号';
            }
            if($field == 'sename'){
                $headArr[]='下单人姓名';
            }
            if($field == 'shouru'){
                $headArr[]='收入金额';
            }
            if($field == 'zhekou'){
                $headArr[]='折扣金额';
            }
            if($field == 'shishou'){
                $headArr[]='实收金额';
            }
            if($field == 'you'){
                $headArr[]='邮费';
            }
            if($field == 'qu'){
                $headArr[]='支付渠道';
            }
            if($field == 'fen'){
                $headArr[]='业务类型';
            }
            if($field == 'daotime'){
                $headArr[]='导出时间';
            }
           


        }

        $filename='支付信息';//导出文件名称

        $this->getExcel($filename,$headArr,$data);
    }
     
     public function wuliu(){
     	 $id=I("id");
     	 $temp=M("dingdan")->where("id='".$id."'")->find();
     	$this->dan=$temp['wuliudan'];
     	$this->name=$temp['wuliu_gongsi'];
     	$this->display();
     }
     
    /* 文章列表 $this->display();
     *
     */
    public function artlist(){

        //分类名称获取
        $keyword=isset($_POST['search'])?$_POST['search']:'';
        //dump($keyword);
        $User= M('article');
        $map['ht_article.art_title']=array('like','%'.$keyword.'%');
        $map['ht_article.art_cate_id']=I('get.cate_id');
        $count= $User->where($map)->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list=$User->join('ht_cate on ht_article.art_cate_id = ht_cate.cate_id')->
        where($map)->order('ht_article.art_date desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        //dump($count);
        $this->display('Article/index'); // 输出模板
    }
    /*添加
     *
     */
    public function add(){
        //分类
        $where['cate_p_id']=27;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        $this->display();
    }

    /*编辑
   *
   */
    public function edit(){

        $obj=M('article')->where(array('art_id'=>I('get.art_id')))->find();
        $this->assign('obj',$obj);

        //分类显示
        $where['cate_p_id']=27;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);

        $this->display();
    }
    /*添加处理
     *
     */
    public function addcl(){

        if(!empty($_FILES['art_img_url'])) {
            $config = array(
                'rootPath' => './Uploads/casePicture/',
                'savePath' => '',
                'maxSize' => 19145728,
                'exts' => array('jpg', 'gif', 'png', 'jpeg')
            );

            $upload = new \Think\Upload($config);

            $casePicture = $upload->uploadOne($_FILES['art_img_url']);

            $case = '/Uploads/casePicture/' . $casePicture['savepath'] . $casePicture['savename'];
        }

        $model = M('article');

        $data['art_img_url'] = $case;
        $data['art_title'] = I('post.art_title');
        $data['art_cate_id'] = I('post.art_cate_id');
        $data['art_from'] = I('post.art_from');
        $data['art_content'] = str_replace('\\','',html_entity_decode(I('post.art_content')));
        $data['art_date']      =date('Y-m-d H:i:s',time());
        if($model->add($data)){
            //redirect(U('Cate/cateList'));
            $this->success('添加成功',U('CasesManagement/index'),1);
        }else{
            $this->error('添加失败',U('CasesManagement/index'),1);
        }

    }

    /*删除
     *
     */
    public function delete(){
        $rs=M('article')->where(array('art_id'=>I('get.art_id')))->delete();
        if($rs){
            $this->success('删除成功',U('CasesManagement/index'));
        }else{
            $this->error();
        }
    }

    /*编辑处理
         *
         */
    public function update(){

        if(!empty($_FILES['art_img_url'])) {
            $config = array(
                'rootPath' => './Uploads/casePicture/',
                'savePath' => '',
                'maxSize' => 19145728,
                'exts' => array('jpg', 'gif', 'png', 'jpeg')
            );

            $upload = new \Think\Upload($config);

            $casePicture = $upload->uploadOne($_FILES['art_img_url']);

            $case = '/Uploads/casePicture/' . $casePicture['savepath'] . $casePicture['savename'];
        }

        $_update['art_img_url'] = $case;
        $_update['art_title'] = I('post.art_title');
        $_update['art_cate_id'] = I('post.art_cate_id');
        $_update['art_from'] = I('post.art_from');
        $_update['art_content'] = str_replace('\\','',html_entity_decode(I('post.art_content')));
        $_update['art_date']      =date('Y-m-d H:i:s',time());

        $where['art_id']=I('post.art_id');
        $rs=M('article')->where($where)->save($_update);

        if($rs){
            $this->success('编辑成功',U('CasesManagement/index'),1);
        }else{
            $this->error('编辑失败',U('CasesManagement/index'),1);
        }

    }
    /*批量删除
     *
     */
    public function deleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('article')->where(array("art_id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('CasesManagement/index'));
        }else{
            $this->error('批量删除失败',U('CasesManagement/index'));
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
    
    
    
     private  function getExcel($fileName,$headArr,$data){
    	
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入   这三个文件放在thinkphp/library/org/phpExcel
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y_m_d_H_i_s",time());
        $fileName .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        
         //设置表头  超过26列
        $key = 0;
        foreach($headArr as $v){
            //注意，不能少了。将列数字转换为字母\
            $colum = \PHPExcel_Cell::stringFromColumnIndex($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

       
        $column = 2; //从第二行写入数据 第一行是表头
        $objActSheet = $objPHPExcel->getActiveSheet(); 
        
        foreach($data as $key => $rows){ //行写入
            $span = 0;
            foreach($rows as $keyName=>$value){// 列写入
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);

       
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
        
        

	
    }
}