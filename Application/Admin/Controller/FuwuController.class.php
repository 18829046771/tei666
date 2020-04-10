<?php
namespace Admin\Controller;
use Think\Controller;

class FuwuController extends CommController {

	// 派单信息
	public function paidan(){
		$get = I('get.');

        $ming = I('get.ming', '');
        if (!empty($ming)) {
            $this->assign('ming', $ming);
            $map['paysn'] = ['like', '%' . $ming . '%'];
        }

        $state = I('get.state', '');
        if (isset($state) && $state != '') {
            $this->assign('state', $state);
            $map['state'] = ['eq', $state];
        }

        // 商品类别
        $cate_id = I('get.cate_id', '');
        $this->assign('cate_id', $cate_id);
        if (!empty($cate_id)) {
            $art_ids = M('Article')->where(['art_cate_id' => $cate_id])->getField('art_id', true);
            if ($art_ids) {
                $cate_map['store_id'] = ['in', $art_ids];
                $paysns = M('dingdan')->where($cate_map)->getField('paysn', true);
                if ($paysns) {
                    $map['paysn'] = ['in', $paysns];
                }
            }
        }

        // 时间筛选
        $startDate = I('get.kaitime', '');
        $endDate = I('get.endtime', '');
        $this->assign('startDate', $startDate);
        $this->assign('endDate', $endDate);
        if(!empty($startDate) && !empty($endDate)){
            $map['time'] = ['between', [$startDate, $endDate]];
        }

		$m = M('fuwu_paidan');
		$map['fu_id'] = ['eq', $get['id']];
		$count = $m->where($map)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();

		$list = $m->where($map)->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$zfwl = 0; // 总服务量
        $zfwf = 0; // 总服务费
        $dataList = [];
		foreach($list as $k => $v){
			$dataList[$k] = $v;
			// 服务商
			$dataList[$k]['fuwu_name'] = $get['name'];
			// 订单
			$dingdan = M('dingdan')->where(['id' => $v['dingid']])->find();
			$dataList[$k]['chanpin_name'] = $dingdan['name'];
			$dataList[$k]['fwl'] = $dingdan['num'];
			$zfwl += $dingdan['num'];
			// 商品
			unset($chanpin);
			$chanpin = M('Article')->where(['art_id' => $dingdan['store_id']])->find();
			$fwf = $dingdan['num'] * $chanpin['f_money'];
            $dataList[$k]['fwf'] = $fwf;
            $zfwf += $fwf;
			// 商品类别
			$cate = M('cate')->where(['cate_id' => $chanpin['art_cate_id']])->find();
			$dataList[$k]['cate_name'] = $cate['cate_name'];
		}

        $where['cate_p_id'] = 18;
        $cate = M("cate")->where($where)->select();
        $this->assign('cate', $cate);
		$this->assign('id', $get['id']);
        $this->assign('name', $get['name']);
		$this->assign('count', $count);
        $this->assign('zfwl', $zfwl);
        $this->assign('zfwf', $zfwf);
		$this->assign('list', $dataList);
		$this->assign('page', $show);
		$this->display();
	}

	public function export(){
		$id = I('get.id/d');
        $name = I('get.name');

        // 商品类别
        $cate_id = I('get.cate_id', '');
        $this->assign('cate_id', $cate_id);
        if (!empty($cate_id)) {
            $art_ids = M('Article')->where(['art_cate_id' => $cate_id])->getField('art_id', true);
            if ($art_ids) {
                $cate_map['store_id'] = ['in', $art_ids];
                $paysns = M('dingdan')->where($cate_map)->getField('paysn', true);
                if ($paysns) {
                    $map['paysn'] = ['in', $paysns];
                }
            }
        }

        $state = I('get.state', '');
        if (isset($state) && $state != '') {
            $this->assign('state', $state);
            $map['state'] = ['eq', $state];
        }

        // 时间筛选
        $startDate = I('get.kaitime', '');
        $endDate = I('get.endtime', '');
        $this->assign('startDate', $startDate);
        $this->assign('endDate', $endDate);
        if(!empty($startDate) && !empty($endDate)){
            $map['time'] = ['between', [$startDate, $endDate]];
        }

        
        $m = M('fuwu_paidan');
        $map['fu_id'] = ['eq', $id];
		$list = $m->where($map)->order('time desc')->select();
		$data = [];
		foreach($list as $k => $v){
			$data[$k]['id'] = $v['id']; // 序号
			$data[$k]['fuwu_name'] = $name; // 服务商名称
			$dingdan = M('dingdan')->where(['id' => $v['dingid']])->find();
			$chanpin = M('Article')->where(['id' => $dingdan['store_id']])->find();
			$data[$k]['chanpin_name'] = $chanpin['product_title']; // 产品名称
			$cate = M('cate')->where(['cate_id' => $chanpin['art_cate_id']])->find();
			$data[$k]['cate_name'] = $v['cate_name']; // 产品类别
			$data[$k]['time'] = $v['time']; // 派单时间
			$data[$k]['fuwu_time'] = $v['fuwu_time']; // 服务时间
			$data[$k]['wan_time'] = $v['wan_time']; // 服务完成时间
			$data[$k]['fwl'] = $dingdan['num'];
			$data[$k]['state'] = $v['state'] == 1 ? '已核销' : '未核销';
		}

        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'fuwu_name') {
                $headArr[] = '服务商名称';
            }
            if ($field == 'chanpin_name') {
                $headArr[] = '产品名称';
            }
            if ($field == 'cate_name') {
                $headArr[] = '产品类别';
            }
            if ($field == 'time') {
                $headArr[] = '派单时间';
            }
            if ($field == 'fuwu_time') {
                $headArr[] = '服务时间';
            }
            if ($field == 'wan_time') {
                $headArr[] = '服务完成时间';
            }
            if ($field == 'fwl') {
                $headArr[] = '服务量';
            }
            if ($field == 'state') {
                $headArr[] = '服务状态';
            }
        }
        $this->getExcel('服务商派单表-' . $name, $headArr, $data);
	}

	// 旅游服务商列表
	public function fuwu_ly(){
		$Article = M("fuwu");
		$count = $Article->where("cate_id=22")->count();   
		$Page = new \Think\Page($count,30);
		$show = $Page->show();
		//第一页
		$products = $Article->where("cate_id=22")->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($products as $k => $v){
			if($v['cate_id'] == '1'){
				$products[$k]['fen'] = '城会玩';	
			}else{
				$tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
				$products[$k]['fen'] = $tt['cate_name']; 
			}
		}

		$this->assign('list', $products);
		$this->assign('page', $show);
		$this->display();
	}  

	// 车供应商列表
	public function fuwu_che(){
		$Article = M("fuwu");
		$count = $Article -> where("cate_id=18") -> count();   
		$Page = new \Think\Page($count,30);
		$show = $Page -> show();
		// 第一页
		$products = $Article -> where("cate_id=18") -> order('time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		foreach($products as $k=>$v){
			if($v['cate_id']=='1'){
				$products[$k]['fen']='城会玩';	
			}else{
				$tt=M("cate")->where("cate_id= '".$v['cate_id']."'")->find();
				$products[$k]['fen']=$tt['cate_name']; 
			}
		}

		$this->assign('list',$products);
		$this->assign('page',$show);
		$this->display();
	}  

	// 忒娃服务商列表
	public function fuwu_teiwa(){
		$Article = M("fuwu");
		$count = $Article -> where("cate_id=102") -> count();   
		$Page = new \Think\Page($count,30);
		$show = $Page -> show();
		// 第一页
		$products = $Article -> where("cate_id=102") -> order('time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		foreach($products as $k=>$v){
			if($v['cate_id']=='1'){
				$products[$k]['fen']='城会玩';	
			}else{
				$tt=M("cate")->where("cate_id= '".$v['cate_id']."'")->find();
				$products[$k]['fen']=$tt['cate_name']; 
			}
		}

		$this->assign('list',$products);
		$this->assign('page',$show);
		$this->display();
	}  

	// 保险供应商列表
	public function fuwu_bao(){
		$Article = M("fuwu");
		$count = $Article -> where("cate_id=81") -> count();   
		$Page = new \Think\Page($count,30);
		$show = $Page -> show();
		// 第一页
		$products = $Article -> where("cate_id=81") -> order('time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		foreach($products as $k=>$v){
			if($v['cate_id']=='1'){
				$products[$k]['fen']='城会玩';	
			}else{
				$tt=M("cate")->where("cate_id= '".$v['cate_id']."'")->find();
				$products[$k]['fen']=$tt['cate_name']; 
			}
		}

		$this->assign('list',$products);
		$this->assign('page',$show);
		$this->display();
	}

	// 其他供应商列表
	public function fuwu_qi(){
		$Article = M("fuwu");
		$count = $Article -> where("cate_id=85") -> count();   
		$Page = new \Think\Page($count,30);
		$show = $Page -> show();
		// 第一页
		$products = $Article -> where("cate_id=85") -> order('time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		foreach($products as $k=>$v){
			if($v['cate_id']=='1'){
				$products[$k]['fen']='城会玩';	
			}else{
				$tt=M("cate")->where("cate_id= '".$v['cate_id']."'")->find();
				$products[$k]['fen']=$tt['cate_name']; 
			}
		}

		$this->assign('list',$products);
		$this->assign('page',$show);
		$this->display();
	}

	// 城会玩供应商列表
	public function fuwu_cheng(){
		$Article=M("fuwu");
		$count = $Article -> where("cate_id=1") -> count();   
		$Page = new \Think\Page($count,30);
		$show = $Page -> show();
		// 第一页
		$products = $Article -> where("cate_id=1") -> order('time desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		foreach($products as $k=>$v){
			if($v['cate_id']=='1'){
				$products[$k]['fen']='城会玩';	
			}else{
				$tt = M("cate")->where("cate_id= '".$v['cate_id']."'")->find();
				$products[$k]['fen']=$tt['cate_name']; 
			}
		}

		$this->assign('list',$products);
		$this->assign('page',$show);
		$this->display();
	}

	// 添加供应商
	public function fuwu_add(){
		// 分类
		$where['cate_id'] = ['in', [18, 22,85,102]];
		$list2 = M('cate')->where($where)->order('cate_order asc')->select();
		$this->assign('list2',$list2);
		$this->display();
	} 

	public function fuwu_addcl(){
		$data = I("post.");
		$rules = [
			['cate_id', 'require', '请选择所属分类！'],
			['name', 'require', '请输入供应商名称！'],
			['photo', 'require', '请输入供应商电话！'],
			['user', 'require', '请输入联系人！'],
			['address', 'require', '请输入服务商地址！'],
			['username', 'require', '请输入登录帐号！'],
			['username', '', '帐号已经存在！', 0, 'unique', 1]
		];
		$db = M("fuwu");
		if (!$db->validate($rules)->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($db->getError());
		}else{
			// 验证通过 可以进行其他数据操作
			$to['cate_id'] = $data['cate_id'];
			$to['name'] = $data['name'];
			$to['photo'] = $data['photo'];
			$to['user'] = $data['user'];
			$to['username'] = $data['username'];
			if (isset($data['password']) && !empty($data['password'])) {
				$to['password'] = md5($data['password']);
			}
			$to['time'] = date('Y-m-d H:i:s',time());
			$res = $db->add($to);
			if($res){
				$this->success('添加成功',U('Fuwu/fuwu_ly'),1);
			}else{
				$this->success('添加失败',U('Fuwu/fuwu_ly'),1);
			}
		}
	} 



	// 删除供应商
	public function fuwu_delete(){
		$id = I("id");
		$rs = M('fuwu')->where(array('id'=>$id))->delete();
		if($rs){
			$this->success('删除成功',U('Fuwu/fuwu_ly'));
		}else{
			$this->error();
		}
	}

	// 编辑供应商
	public function fuwu_edit(){
		// 分类
		$where['cate_p_id'] = 0;
		$list2 = M('cate')->where($where)->order('cate_order asc')->select();
		$this->assign('list2',$list2);
		$id = I("id/d");
		$temp = M('fuwu')->where(array('id'=>$id))->find();
		$this->temp = $temp;
		$this->display();
	}

	public function fuwu_bian(){
		$data = I("post.");
		$ids = $data['ids'];
		$info = 

		$to['cate_id'] = $data['cate_id'];
		$to['name'] = $data['name'];
		$to['photo'] = $data['photo'];
		$to['user'] = $data['user'];
		$to['address'] = $data['address'];
		$to['username'] = $data['username'];

		$where['id'] = ['neq', $ids];
		$where['username'] = ['eq', $data['username']];
		if (M('fuwu')->where($where)->find()) {
			$this->error('账号已存在，请重新输入！');
			die;
		}
		if (isset($data['password']) && !empty($data['password'])) {
			$to['password'] = md5($data['password']);
		}
		$rs = M('fuwu')->where(array('id' => $ids))->save($to);
		if($rs){
			$this->success('编辑成功',U('Fuwu/fuwu_ly'));
		}else{
			$this->success('编辑失败',U('Fuwu/fuwu_ly'));
		}
	}

	// 批量删除
	public function fuwuDeleteAll(){
		$data = I("post.");

		foreach($data as $rows){
			$rs = M('fuwu')->where(array("id"=>$rows))->delete();
		}

		if($rs){
			$this->success('批量删除成功',U('Fuwu/fuwu_ly'));
		}else{
			$this->error('批量删除失败',U('Fuwu/fuwu_ly'));
		}
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