<?php
/**
 * Created by PhpStorm.
 * User: 史浩东
 * Date: 2017/8/13
 * Time: 13:48
 */
namespace Admin\Controller;

class PictureManagementController extends CommController {
    /**
     * 退款
     */
    public function index(){
        $Picture = M('dingdan');
        $search=I("search");
        $this->search = $search;
        if(!empty($search)){
        	$where['paysn']=$search;
        }
        $where['tuikuan']=array('neq',0);
        $count = $Picture -> where($where) -> count();   
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        //第一页
        $pictureList = $Picture -> where($where) -> order('tuikuan_ktime desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($pictureList as $k=>$v){
            if ($v['type'] == 1) {
                $pictureList[$k]['user'] = $v['lianxi'];
                $pictureList[$k]['photo'] = $v['lianxiphoto'];
            } else {
                $address = M('address')->where(['id' => $v['addressid']])->find();
                $pictureList[$k]['user'] = $address['name'];
                $pictureList[$k]['photo'] = $address['dian'];
            }
            $pictureList[$k]['tuikuans'] = M('tuikuan')->where(['did' => $v['id']])->find();
        }
        
        $this ->assign('picture',$pictureList);

        $this->assign('page',$show);
        $this -> display();
    }

    // 退款原因
    public function tuikuans(){
        $id = I('id/d');
        $dan = M('dingdan')->where(['id' => $id])->find();
        $this->dan = $dan;
        $tui = M('tuikuan')->where(['did' => $id])->find();
        $this->tui = $tui;
        $this->display();
    }

    public function daochu(){
    	
        $Picture = M('tuikuan');
        $count = $Picture -> where() -> count();   
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        //第一页
        $pictureList = $Picture -> where() -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($pictureList as $k=>$v){
        	$dian=M("dingdan")->where("paysn='".$v['paysn']."'")->find();
        	$pictureList[$k]['pay']=' '.$dian['paysn'].' ';
        	if($dian['ster']=='1' and $dian['tuikuan']=='1' ){
        		$pictureList[$k]['tuikuan']='未发货/待退款';
        	}
        	if($dian['ster']=='1' and $dian['tuikuan']=='2' ){
        		$pictureList[$k]['tuikuan']='未发货/已退款';
        	}
        	if($dian['ster']=='2' and $dian['tuikuan']=='1' ){
        		$pictureList[$k]['tuikuan']='已发货/待退款';
        	}
        	if($dian['ster']=='2' and $dian['tuikuan']=='2' ){
        		$pictureList[$k]['tuikuan']='已发货/已退款';
        	}
        	$pictureList[$k]['storename']=$dian['name']; //产品名称
        	if(empty($dian['store_id'])){
        		//查找活动
        		$pictureList[$k]['fen']='城会玩';
        	}else{
        		//查找产品
        		$store=M("article")->where("art_id='".$dian['store_id']."'")->find();
        		$lei=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
        		$pictureList[$k]['fen']=$lei['cate_name'];
        	}
        	$tt=M("user")->where("id= '".$v['uid']."'")->find();
        	$pictureList[$k]['name']=$tt['username']; 
        }
        // $this ->assign('picture',$pictureList);

        //dump($pictureList);die;
        $this->goods_export($pictureList);
        
         
    
    }
    
    
      function goods_export($goods_list=array())
    {
      //dump($goods_list);
//      exit;  //导出数据
        $goods_list = $goods_list;
        $data = array();
        foreach ($goods_list as $k=>$goods_info){
            $data[$k][id] = $goods_info['id'];   //序号
            $data[$k][pay] = $goods_info['pay'];//订单号
            $data[$k][storename] = $goods_info['storename'];  //产品名称
            $data[$k][money] = $goods_info['money'];//退款金额
            $data[$k][fen] = $goods_info['fen'];//所属分类
            $data[$k][tuikuan] = $goods_info['tuikuan'];//状态
            $data[$k][time] = $goods_info['time'];//申请时间
            $data[$k][name] = $goods_info['name'];  //申请人手机号

			
        }

       

        foreach ($data as $field=>$v){
            if($field == 'id'){
                $headArr[]='序号';
            }
            if($field == 'pay'){
                $headArr[]='订单号';
            }
            if($field == 'storename'){
                $headArr[]='产品名称';
            }
            if($field == 'money'){
                $headArr[]='退款金额';
            }
            if($field == 'fen'){
                $headArr[]='所属分类';
            }
            if($field == 'tuikuan'){
                $headArr[]='状态';
            }
            if($field == 'time'){
                $headArr[]='申请时间';
            }
            if($field == 'name'){
                $headArr[]='申请人';
            }


        }

        $filename='退款信息';//导出文件名称

        $this->getExcel($filename,$headArr,$data);
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

    /**
     * 
     */
    public function add(){
        $this -> display();
    }


   /***
    * 微信退款
    */
 public function tuikuan(){
 	 $id=I("id");
 	
 	 //根据$id 查询订单
 	 $tuikuan = M('tuikuan')->where(array('id'=>$id))->find(); 
 	
 	 
 	 $order=M("dingdan")->where("paysn='".$tuikuan['paysn']."' AND ster=1")->find();
 	 if(!$order){
 	 	return false;
 	 } 
 	
 }




    /**
     * 图片编辑页面显示
     */
    public function tupian_bian(){
      
            $id = I('get.id');

            $Picture = M('picture');

            $picture = $Picture -> where('id = '.$id) -> find();

            $this -> assign('picture',$picture);

            $this -> display();
        
    }

    /**
     * 添加图片逻辑处理
     */
    public function addHandle(){
        if(IS_POST){
            if (!empty($_FILES['picture'])){
                $config = array(
                    'rootPath'  => './Uploads/picture/',
                    'savePath'  => '',
                    'maxSize'   => 19145728000,
                    'exts'      => array('jpg', 'gif', 'png', 'jpeg')
                );

                $upload = new \Think\Upload($config);

                $picture = $upload -> uploadOne($_FILES['picture']);

                $pictureUrl =  '/Uploads/picture/'.$picture['savepath'].$picture['savename'];

            }
            
            

            $editData = array(
                'picture_title' => I('post.picture_title'),
                'picture_url'   => $pictureUrl,
                'add_time'      => time()
            );

            $Picture = M('picture');

            $res = $Picture->data($editData)->add();

            if ($res){
                $this -> success('添加成功',U('PictureManagement/index'));
            }else{
                $this -> error('添加失败',U('PictureManagement/index'));
            }
        }
    }

    /**
     * 图片编辑逻辑处理
     */
    public function editHandle(){
       
            $id = I('post.id');

            if (!empty($_FILES['picture'])){
                $config = array(
                    'rootPath'  => './Uploads/picture/',
                    'savePath'  => '',
                    'maxSize'   => 19145728000,
                    'exts'      => array('jpg', 'gif', 'png', 'jpeg')
                );

                $upload = new \Think\Upload($config);

                $picture = $upload -> uploadOne($_FILES['picture']);

                $pictureUrl =  '/Uploads/picture/'.$picture['savepath'].$picture['savename'];
                if(strlen($pictureUrl)<30){
            	  $pictureUrl=null;
                }
            }
            $editData = [
                'picture_url'   => $pictureUrl,
                'add_time'      => time(),
                'url'           => I("url"),
                'content'       => I("content"),
                'title'         => I('title')
            ];

            $res = M('picture')->where(['id' => $id])->save($editData);
            if ($res){
                $this -> success('编辑成功',U('PictureManagement/tupian'));
            }else{
                $this -> error('编辑失败',U('PictureManagement/tupian'));
            }
        
    }
    
     //旅游  车管家
    public function tupian(){
    	 $temp=M("picture")->where()->order("id asc")->select();
    	 $this->temp=$temp;
    	 
    	$this->display();
    }
}