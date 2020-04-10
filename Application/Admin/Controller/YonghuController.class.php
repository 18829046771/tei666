<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17 0017$this->dia
 * Time: 下午 1:42
 */

namespace Admin\Controller;

class YonghuController extends CommController {
    /**
     * 会员管理页面显示
     */
    public function index(){
        $tt=array();
    	$search=I("search");
        $this->search = $search;
        if(!empty($search)){
            $tt['sename|photo'] = ['like', '%' . $search . '%'];
        }
    	$tt['type']='1';

        $count= M("user")->where($tt)->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $temp=M("user")->where($tt)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('temp',$temp);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
    	
    	
    	$this->display();
    }
    
    public function xiangqing(){
    	$id=I("id");
    	$temp=M("user")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$this->display();
    }
    
    
     /**
     * 普通管理页面显示
     */
    public function putong(){
    	$search=I("search");
    	$tt=array();
    	$tt['type']='0';
        $this->search = $search;
        if(!empty($search)){
            $tt['sename|photo'] = ['like', '%' . $search . '%'];
        }
        $count= M("user")->where($tt)->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $temp=M("user")->where($tt)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('temp',$temp);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
    	
    	
    	$this->display();
    }
    
    
    public function shouye(){
    	//订单
    	$where['tuikuan']=array('eq','0');
    	$where['ster']=array('neq','0');
    	$dan=M("dingdan")->where($where)->select();
    	foreach($dan as $k=>$v){
    		$zong +=$v['zong'];
    	}
    	$this->dan=count($dan);
    	
    	//会员
    	$huiyuan=M("user")->where("type=1")->count();
    	$this->huiyuan=$huiyuan;
    	
    	//商品
    	$store=M("article")->where()->count();
    	$this->store=$store;
    	
    	//销售额
    	$this->zong=$zong;
    	//最近订单
    	$jin=M("dingdan")->where($where)->order('time desc')->limit('10')->select();
    	$this->jin=$jin;
    	
    	$this->display();
    }
    
    
    
    public function huiyuan(){
    	
    	
    	$tt=array();
    	$search=I("search");
        $this->search = $search;
        if(!empty($search)){
            $tt['paysn'] = ['like', '%' . $search . '%'];
        }
        
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        $this->kaitime = $kaitime;
        $this->endtime = $endtime;
        if(!empty($kaitime) && !empty($endtime)){
            $tt['time'] = ['between', [$kaitime, $endtime]];
        }
        
    	$tt['ster']='1';

        $count= M("huiyuan_paysn")->where($tt)->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $temp=M("huiyuan_paysn")->where($tt)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($temp as $k=>$v){
        	$user=M("user")->where("id='".$v['uid']."'")->find();
        	$temp[$k]['weiname']=$user['sename'];
        }
      
        $this->assign('temp',$temp);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
    	$this->display();
    }
    
    public function tuikuan_dao(){
    	$tt['ster']='1';
    	$kaitime = I("kaitime");
        $endtime = I("endtime");
        $this->kaitime = $kaitime;
        $this->endtime = $endtime;
        if(!empty($kaitime) && !empty($endtime)){
            $tt['time'] = ['between', [$kaitime, $endtime]];
        }
    	$temp=M("huiyuan_paysn")->where($tt)->order("id desc")->select();
    	foreach($temp as $k=>$v){
        	$user=M("user")->where("id='".$v['uid']."'")->find();
        	$temp[$k]['weiname']=$user['sename'];
        }
        $data = [];
        $i = 1;
        foreach($temp as $k=>$v) {
            $data[$k]['id'] = $i; // 序号
            $data[$k]['paysn'] = ' '.$v['paysn'].' '; // 订单号
            $data[$k]['title'] = $v['name']; // 活动名称
            $data[$k]['money'] = '66.6'; // 金额
            $data[$k]['name'] = $v['weiname']; //用户
            $data[$k]['pay_state'] = '已支付'; // 支付状态
            $data[$k]['time'] = $v['time']; // 支付状态
            $data[$k]['daochu'] = date('Y-m-d H:i:s',time()); // 导出时间
            $i++;
        } 
        
        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'paysn') {
                $headArr[] = '订单号';
            }
            if ($field == 'title') {
                $headArr[] = '业务类型';
            }
            
            if ($field == 'money') {
                $headArr[] = '金额';
            }
            if ($field == 'name') {
                $headArr[] = '姓名';
            }
           
            if ($field == 'pay_state') {
                $headArr[] = '支付状态';
            }
            if ($field == 'time') {
                $headArr[] = '支付时间';
            }
           
            if ($field == 'daochu') {
                $headArr[] = '导出时间';
            }
        }
        $this->getExcel('购买会员信息表', $headArr, $data); 
         
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