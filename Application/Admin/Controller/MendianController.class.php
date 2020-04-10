<?php
namespace Admin\Controller;

class MendianController extends CommController {

    /* 文章列表
     *
     */
    public function index(){
        
       $temp=M("dianpu")->select();
        $this->temp=$temp;
        $this->display();
    }
    
    
    public function addcl(){
    	$data=I("post.");
    	$title=I("title");
    	$arr['title']=$title;
    	$arr['time']=date('Y-m-d H:i:s',time());
    	$arr['user']=$data['user'];
    	$arr['phone']=$data['phone'];
    	$arr['kaihuhang']=$data['kaihu'];
    	$arr['huming']=$data['huname'];
    	$arr['kahao']=$data['kahao'];
    	$arr['zhifubao']=$data['zhifubao'];
    	
    	
    	
    	
    	
    	
    	
    	$res=M("dianpu")->add($arr);
    	
    	$url="http://www.xatei666.com/index.php/Home/Huiyuan/myvip.html?id=".$res;
    	
  	    //生成门店二维码
  	    if($res){
  	    	$fileName = createQRcode($url,1);
  	    	$img='/'.$fileName;
  	    	$xiu=M("dianpu")->where("id='".$res."'")->save(array('img'=>$img));
  	    }


    	$this->success('添加成功',U('Mendian/index'));
    }
    
  

    /*删除
     *
     */
    public function delete(){
        $rs=M('dianpu')->where(array('id'=>I('get.id')))->delete();
        if($rs){
            $this->success('删除成功',U('Mendian/index'));
        }else{
            $this->error();
        }
    }

   

  

    /*批量删除
 *
 */
    public function deleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('dianpu')->where(array("id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Mendian/index'));
        }else{
            $this->error('批量删除失败',U('Mendian/index'));
        }
    }
    
    
    public function edit(){
    	$id=I("id");
    	$temp=M("dianpu")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$this->display();
    }
    
    public function cledr(){
    	$data=I("post.");
    	$id=$data['ids'];
        $arr['title']=$data['title'];
        $arr['user']=$data['user'];
    	$arr['phone']=$data['phone'];
    	$arr['kaihuhang']=$data['kaihu'];
    	$arr['huming']=$data['huname'];
    	$arr['kahao']=$data['kahao'];
    	$arr['zhifubao']=$data['zhifubao'];
    	
    	
        $res=M("dianpu")->where("id='".$id."'")->save($arr);
    	if($res){
    		    $this->success('编辑成功',U('Mendian/index'),1);
    	}else{
    		    $this->success('编辑失败',U('Mendian/index'),1);
    	}
    }
    
    
    
   //门店订单
   public function dingdan(){
        $Picture = M('huiyuan_paysn');
         $where['ster']='1';
         $kaitime=I("kaitime");
         $endtime=I("endtime");
         $this->kaitime=$kaitime;
         $this->endtime=$endtime;
         if(!empty($kaitime) && !empty($endtime)){
         	$where['time']=array('between',array($kaitime,$endtime));
         }
         $menid=I("men");
        $this->menid=$menid;
         if(!empty($menid)){
         	$where['menid']=$menid;
         }else{
         	$where['menid']=array('neq','0');
         }
        $count = $Picture -> where($where) -> count();   
        $this->count=$count;
        $zong=$count*1;
        $this->zong=$zong;
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        //第一页
        $pictureList = $Picture -> where($where) -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
       
        foreach($pictureList as $k=>$v){
        	  $user=M("dianpu")->where("id='".$v['menid']."'")->find();
        	  $pictureList[$k]['mname']=$user['title'];
        	
        }
        $temp=$Picture -> where($where) -> order('id desc')-> select();
        
        
        $this ->assign('picture',$pictureList);

        $this->assign('page',$show);
        //门店集合
        $men=M("dianpu")->where()->select();
        $this->men=$men;
        
        
        
        
        $this -> display();
        
        
         
    
  
   	
   }
   
   
   public function daochu(){
   	$ktime=I("kaitime");
   	$endtime=I("endtime");
   	if(!empty($ktime) && !empty($endtime)){
   		$where['time']=['between', [$ktime, $endtime]];
   	}
   
   	$men=I("men");
    if(empty($men)){
    	$where['menid']=array('neq','0');
    }else{
    	$where['menid']=$men;
    }
    $where['ster']='1';
    $temp=M("huiyuan_paysn")->where($where)->select();
	   foreach($temp as $k=>$v){
	   	  $mendian=M("dianpu")->where("id='".$v['menid']."'")->find();
	   	  $temp[$k]['mname']=$mendian['title'];
	   	  $temp[$k]['daotime']=date('Y-m-d H:i:s',time());
	   	   $temp[$k]['pay']=' '.$v['paysn'].' ';
	   	  
	   }	
	   
	   
	  
    $this->goods($temp);
   }
   
   
   
   function goods($goods_list = array()) {
        $data = array();
        foreach ($goods_list as $k => $goods_info) {
        
            $data[$k][paysn] = $goods_info['pay']; //订单号
            $data[$k][name] = $goods_info['name']; 
            $data[$k][ktime] = $goods_info['time']; 
            $data[$k][zhuangtai] = '已支付'; 
            $data[$k][qu] = '微信支付'; //交易渠道
            $data[$k][mname] = $goods_info['mname'];
            $data[$k][daotime] = $goods_info['daotime']; //导出时间
            
        }
        foreach ($data as $field => $v) {
            if ($field == 'paysn') {
                $headArr[] = '订单号';
            }
            if ($field == 'name') {
                $headArr[] = '业务类型';
            }
            if ($field == 'ktime') {
                $headArr[] = '开通时间';
            }
            if ($field == 'zhuangtai') {
                $headArr[] = '状态';
            }
            
            if ($field == 'qu') {
                $headArr[] = '支付渠道';
            }
            if ($field == 'mname') {
                $headArr[] = '所属门店';
            }
            
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '店铺购买会员支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }
    
    
    
    private function getExcel($fileName, $headArr, $data) {
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入   这三个文件放在thinkphp/library/org/phpExcel
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");
        $date = date("Y_m_d_H_i_s", time());
        $fileName.= "_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        //设置表头  超过26列
        $key = 0;
        foreach ($headArr as $v) {
            //注意，不能少了。将列数字转换为字母\
            $colum = \PHPExcel_Cell::stringFromColumnIndex($key);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key+= 1;
        }
        $column = 2; //从第二行写入数据 第一行是表头
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach ($data as $key => $rows) { //行写入
            $span = 0;
            foreach ($rows as $keyName => $value) { // 列写入
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j . $column, $value);
                $span++;
            }
            $column++;
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
}