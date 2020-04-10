<?php
namespace Admin\Controller;
use Think\Controller;

class WanController extends CommController {

    // 活动列表
    public function huodong(){
        $db = M('huodong');
        
        $where = [];
        
        $type = I("type");
        $this->type = $type;
        if (!empty($type)) {
            $where['type'] = ['eq', $type];
        }

        $is_sj = I("is_sj");
        $this->is_sj = $is_sj;
        if (!empty($is_sj)) {
            $where['is_sj'] = ['eq', $is_sj];
        }

        $ming=I("ming");
        $this->ming = $ming;
        if (!empty($ming)) {
            $tt['title']=array('like','%'.$ming.'%');
        }

        $count = $db->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $this->show = $Page->show();
        $list = $db->where($where)->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->list = $list;
        $this->display();
    }

    // 添加活动
    public function add(){
        if (IS_POST) {
            $data = I('post.');
            $rules = [
                ['type', 'require', '请选择参与类型！'],
                ['title', 'require', '请输入活动名称！'],
                ['num', 'require', '请输入参与人数！'],
                ['phone', 'require', '请输入联系电话！'],
                ['address', 'require', '请输入地址！'],
                ['photo', 'require', '请上传活动图片！'],
                ['banner1', 'require', '请上传banner1！'],
                // ['money', 'require', '请输入商城价！'],
                // ['money_hy', 'require', '请输入会员价！'],
                ['start_time', 'require', '请选择开始时间！'],
                ['end_time', 'require', '请选择结束时间！'],
                ['is_sj', 'require', '请选择是否上架！'],
                ['content', 'require', '请输入活动详情！']
            ];
            $db = M("huodong");
            if (!$db->validate($rules)->create()){
                $this->error($db->getError());
            }else{
                $data['content'] = $this->encode(str_replace('\"','"', $_POST['content']));
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $data['add_time'] = time();
                $res = $db->add($data);
                if($res){
                    $this->success('添加成功', U('Wan/huodong'), 1);
                }else{
                    $this->error('添加失败，请重试！');
                }
            }
            return;
        }

        $this->display();
    }

    // 编辑活动
    public function edit(){
        $id = I("id/d");
        empty($id) && die('参数错误！');

        $db = M('huodong');

        if (IS_POST) {
            $data = I('post.');
            $rules = [
                ['id', 'require', '参数错误！'],
                ['type', 'require', '请选择参与类型！'],
                ['title', 'require', '请输入活动名称！'],
                ['num', 'require', '请输入参与人数！'],
                ['phone', 'require', '请输入联系电话！'],
                ['address', 'require', '请输入地址！'],
                ['photo', 'require', '请上传活动图片！'],
                ['banner1', 'require', '请上传banner1！'],
                ['money', 'require', '请输入商城价！'],
                ['money_hy', 'require', '请输入会员价！'],
                ['start_time', 'require', '请选择开始时间！'],
                ['end_time', 'require', '请选择结束时间！'],
                ['is_sj', 'require', '请选择是否上架！'],
                ['content', 'require', '请输入活动详情！']
            ];
            if (!$db->validate($rules)->create()){
                $this->error($db->getError());
            }else{
                $data['content'] = $this->encode(str_replace('\"','"', $_POST['content']));
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);

                $res = $db->where(['id' => $id])->save($data);
                if($res){
                    $this->success('编辑成功', U('Wan/huodong'), 1);
                }else{
                    $this->error('您没有编辑信息！');
                }
            }
            return;
        }

        $res = $db->where(['id' => $id])->find();
        $res['start_time'] = date('Y-m-d H:i:s', $res['start_time']);
        $res['end_time'] = date('Y-m-d H:i:s', $res['end_time']);
        $this->res = $res;
        $this->display();
    }

    /**
     * encode 字符处理
     * @param  string $str 内容
     * @return string
     */
    public function encode($str = ''){
        return empty($str) ? $str : preg_replace("/\\\'/", "'", $str);
    }

    // 排序
    function paixu(){
        $data = I("get.");
        M("huodong")->where(['id' => $data['id']])->save(['pai' => $data['val']]);
        $this->success('修改成功',U('Wan/huodong'), 1);
    }

    // 删除
    public function delete(){
        $id = I('id/d');
        if(M('huodong')->where(['id' => $id])->delete()){
            $this->success('删除成功', U('Wan/huodong'));
        }else{
            $this->error('删除失败！');
        }
    }

    // 删除报名
    public function deleteBm(){
        $id = I('id/d');
        if(M('baoming')->where(['id' => $id])->delete()){
            $this->success('删除成功', U('Wan/baoming'));
        }else{
            $this->error('删除失败！');
        }
    }

    // 报名列表
    public function baoming(){
        $db = M('baoming');

        $where = [];
        
        $type = I("type");
        $this->type = $type;
        if (!empty($type)) {
            $where['type'] = ['eq', $type];
        }
		
		$kaitime = I("kaitime");
		$endtime = I("endtime");
		$this->kaitime = $kaitime;
		$this->endtime = $endtime;
		if(!empty($kaitime) && !empty($endtime)){
			$where['add_time'] = ['between', [strtotime($kaitime), strtotime($endtime)]];
		}
		

        $pay_state = I('pay_state');
        $this->pay_state = $pay_state;
        if ($pay_state != '') {
            $where['pay_state'] = ['eq', $pay_state];
        }

        $ming=I("ming");
        $this->ming = $ming;
        if (!empty($ming)) {
            $tt['name|phone']=array('like','%'.$ming.'%');
        }

        $where['tuikuan']= ['eq', 0];
        $count = $db->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $this->show = $Page->show();
        $list = $db->where($where)->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        $dataList = [];
        foreach ($list as $key => $value) {
            $dataList[$key] = $value;
            $dataList[$key]['hd'] = M('huodong')->where(['id' => $value['hid']])->find(); // 活动信息
        }
        $this->list = $dataList;
        $this->display();
    }

    // 退款列表
    public function tuikuan(){
        $db = M('baoming');

        $where = [];

        $kaitime = I("kaitime");
        $endtime = I("endtime");
        $this->kaitime = $kaitime;
        $this->endtime = $endtime;
        if(!empty($kaitime) && !empty($endtime)){
            $where['tuikuan_stime'] = ['between', [strtotime($kaitime), strtotime($endtime)]];
        }

        $search = I("search");
        $this->search = $search;
        if (!empty($search)) {
            $where['paysn'] = ['like', '%' . $search . '%'];
        }

        $where['tuikuan']= ['neq', 0];
        $count = $db->where($where)->count();   
        $Page = new \Think\Page($count, 20);
        $show = $Page->show();
        // 第一页
        $list = $db->where($where) -> order('tuikuan_stime desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($list as $k=>$v) {
            $list[$k]['hd'] = M('huodong')->where(['id' => $v['hid']])->find();
        }

        $this->assign('list',$list);

        $this->assign('page',$show);
        $this->display();
    }
    
    // 退款导出
    public function tuikuan_dao(){
    	$db = M('baoming');

        $where = [];

        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
            $kaitime = urldecode($kaitime);
            $endtime = urldecode($endtime);
            $where['tuikuan_stime'] = ['between', [strtotime($kaitime), strtotime($endtime)]];
        }

        $search = I("search");
        if (!empty($search)) {
            $where['paysn'] = ['like', '%' . $search . '%'];
        }

    	$where['tuikuan']= ['neq', 0];
    	$list = $db->where($where)->select();  
    	$data = [];
        $i = 1;
    	foreach($list as $k=>$v) {
            $data[$k]['id'] = $i; // 序号
            $data[$k]['paysn'] = ' '.$v['paysn'].' '; // 订单号
            $hd = M('huodong')->where(['id' => $v['hid']])->find(); // 活动信息
            $data[$k]['title'] = empty($v['title']) ? $hd['title'] : $v['title']; // 活动名称
            $data[$k]['number'] = $v['number']; // 数量
            $data[$k]['money'] = $v['money']; // 金额
            $data[$k]['name'] = $v['name']; //用户
            $data[$k]['phone'] = $v['phone']; //用户电话
            $data[$k]['pay_state'] = '已退款'; // 支付状态
            $data[$k]['tuikuan_stime'] = date('Y-m-d H:i:s', $v['tuikuan_stime']); // 退款申请时间
            $data[$k]['tuikuan_etime'] = date('Y-m-d H:i:s', $v['tuikuan_etime']); // 退款同意时间
            $data[$k]['tuikuan'] = date('Y-m-d H:i:s',time()); // 导出时间
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
                $headArr[] = '活动名称';
            }
            if ($field == 'number') {
                $headArr[] = '数量';
            }
            if ($field == 'money') {
                $headArr[] = '总金额';
            }
            if ($field == 'name') {
                $headArr[] = '姓名';
            }
            if ($field == 'phone') {
                $headArr[] = '手机号码';
            }
            if ($field == 'pay_state') {
                $headArr[] = '支付状态';
            }
            if ($field == 'tuikuan_stime') {
                $headArr[] = '退款申请时间';
            }
            if ($field == 'tuikuan_etime') {
                $headArr[] = '退款同意时间';
            }
            if ($field == 'tuikuan') {
                $headArr[] = '导出时间';
            }
        }
        $this->getExcel('报名退款信息表', $headArr, $data);
    }

    public function export(){
        $db = M('baoming');

        $where = [];
        
        $type = I("type");
        $this->type = $type;
        if (!empty($type)) {
            $where['type'] = ['eq', $type];
        }

        $pay_state = I('pay_state');
        $this->pay_state = $pay_state;
        if ($pay_state != '') {
            $where['pay_state'] = ['eq', $pay_state];
        }

        $ming=I("ming");
        $this->ming = $ming;
        if (!empty($ming)) {
            $tt['name|phone']=array('like','%'.$ming.'%');
        }
		$kaitime = I("kaitime");
		$endtime = I("endtime");
		$this->kaitime = $kaitime;
		$this->endtime = $endtime;
		$kaiti=str_replace("+"," ",$kaitime);
		$endti=str_replace("+"," ",$endtime);
		
		if(!empty($kaitime) && !empty($endtime)){
			$where['add_time'] = ['between', [strtotime($kaiti), strtotime($endti)]];
		}
		

        $where['tuikuan']= ['eq', 0];
        $list = $db->where($where)->order('add_time desc')->select();
        $data = [];
        $i = 1;
        foreach($list as $k => $v){
            $data[$k]['id'] = $i; // 序号
            $data[$k]['paysn'] = ' '.$v['paysn'].' '; // 订单号
            $hd = M('huodong')->where(['id' => $v['hid']])->find(); // 活动信息
            $data[$k]['title'] = empty($v['title']) ? $hd['title'] : $v['title']; // 活动名称
            $data[$k]['number'] = $v['number']; // 数量
            $data[$k]['money'] = $v['money']; // 金额
            $data[$k]['name'] = $v['name']; // 金额
            $data[$k]['phone'] = $v['phone']; // 金额
            $data[$k]['pay_state'] = $v['pay_state'] == 0 ? '未支付' : '已支付'; // 金额
            $data[$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']); // 时间
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
                $headArr[] = '活动名称';
            }
            if ($field == 'number') {
                $headArr[] = '数量';
            }
            if ($field == 'money') {
                $headArr[] = '总金额';
            }
            if ($field == 'name') {
                $headArr[] = '姓名';
            }
            if ($field == 'phone') {
                $headArr[] = '手机号码';
            }
            if ($field == 'pay_state') {
                $headArr[] = '支付状态';
            }
            if ($field == 'add_time') {
                $headArr[] = '支付时间';
            }
        }
        $this->getExcel('报名信息表', $headArr, $data);
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

    // 微信对账
    public function wxdz(){
        $db = M('baoming');

        $where = [];

        $kaitime = I("kaitime");
        $endtime = I("endtime");
        $this->kaitime = $kaitime;
        $this->endtime = $endtime;
        if(!empty($kaitime) && !empty($endtime)){
            $where['pay_time'] = ['between', [strtotime($kaitime), strtotime($endtime)]];
        }
        
        
        $storename = I("storename");
        $this->storename = $storename;
        if(!empty($storename)){
            $where['title'] = ['like', '%' . $storename . '%'];
        }

        $where['pay_state'] = ['eq', 1];
        // $where['tuikuan']= ['eq', 0];
        $count = $db->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $this->show = $Page->show();
        $list = $db->where($where)->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $dataList = [];
        foreach ($list as $key => $value) {
            $dataList[$key] = $value;
            $dataList[$key]['hd'] = M('huodong')->where(['id' => $value['hid']])->find(); // 活动信息
        }
        $this->list = $dataList;
        $this->display();
    }

    // 对账
    public function duizhang(){
        $db = M('baoming');

        $where = [];
        
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        $this->kaitime = $kaitime;
        $this->endtime = $endtime;
		$kaiti=str_replace("+"," ",$kaitime);
		$endti=str_replace("+"," ",$endtime);
        if(!empty($kaitime) && !empty($endtime)){
            $where['pay_time'] = ['between', [strtotime($kaiti), strtotime($endti)]];
        }

        $storename = I("storename");
        $this->storename = $storename;
        if(!empty($storename)){
            $where['title'] = ['like', '%' . $storename . '%'];
        }

        
        $where['pay_state'] = ['eq', 1];
        // $where['tuikuan']= ['eq', 0];
        $list = $db->where($where)->order('add_time desc')->select();
        $data = [];
        $i = 1;
        $total_number = 0;
        $total_shishou = 0;
        foreach($list as $k => $v){
            $data[$k]['id'] = $i; // 序号
            $data[$k]['paysn'] = $v['paysn'] . "\t"; // 订单号
            $hd = M('huodong')->where(['id' => $v['hid']])->find(); // 活动信息
            $data[$k]['title'] = empty($v['title']) ? $hd['title'] : $v['title']; // 活动名称
            $data[$k]['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']); // 支付时间
            $data[$k]['phone'] = $v['phone'] . "\t"; // 下单人手机号
            $data[$k]['name'] = $v['name']; // 下单人姓名
            $data[$k]['danjia'] = round($v['money'] * $v['number'], 2); // 单价
            $data[$k]['number'] = $v['number']; // 数量
            $total_number += $v['number'];
            $data[$k]['zhekou'] = 0; // 折扣
            $data[$k]['you'] = 0; // 邮费
            $data[$k]['shishou'] = $v['money']; // 金额
            $total_shishou += $v['money'];
            $data[$k]['qu'] = '微信支付';
            $data[$k]['pay_state'] = $v['pay_state'] == 0 ? '未支付' : '已支付'; // 金额
            $data[$k]['daotime'] = date('Y-m-d H:i:s'); // 导出时间
            $i++;
        }

        // 尾部插入合计
        array_push($data, [
            'id' => '合计',
            'paysn' => '',
            'title' => '',
            'pay_time' => '',
            'phone' => '',
            'name' => '',
            'danjia' => '',
            'number' => $total_number,
            'zhekou' => '',
            'you' => '',
            'shishou' => $total_shishou,
            'qu' => '',
            'pay_state' => '',
            'daotime' => ''
        ]);

        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'paysn') {
                $headArr[] = '订单号';
            }
            if ($field == 'title') {
                $headArr[] = '活动名称';
            }
            if ($field == 'pay_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'phone') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'name') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'danjia') {
                $headArr[] = '单价';
            }
            if ($field == 'number') {
                $headArr[] = '数量';
            }
            if ($field == 'zhekou') {
                $headArr[] = '折扣金额';
            }
            if ($field == 'you') {
                $headArr[] = '邮费';
            }
            if ($field == 'shishou') {
                $headArr[] = '实收金额';
            }
            if ($field == 'qu') {
                $headArr[] = '支付渠道';
            }
            if ($field == 'pay_state') {
                $headArr[] = '状态';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }

        $this->getExcel('报名信息微信对账表', $headArr, $data);
    }

    //服务条款
    public function xieyi(){
        $temp=M("women")->where("id=2")->find();
        $this->temp=$temp;
        $this->display();
    }
    
    //服务条款编辑处理
    public function xieyi_edit(){
        $data = I("post.");
        $arr=array('content' => $data['art_content']);
        $res=M("women")->where("id=2")->save($arr);
        if($res){
             $this->success('编辑成功', U('Wan/xieyi'));
        }else{
             $this->error('编辑失败');
        }
    }

    //关于我们
    public function guanyu(){
        $temp=M("women")->where("id=3")->find();
        $this->temp=$temp;
        $this->display();
    }
    
    //关于我们编辑处理
    public function guanyu_edit(){
        $data = I("post.");
        $arr=array('content' => $data['art_content']);
        $res=M("women")->where("id=3")->save($arr);
        if($res){
             $this->success('编辑成功', U('Wan/guanyu'));
        }else{
             $this->error('编辑失败');
        }
    }

}