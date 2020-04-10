<?php
namespace Admin\Controller;
use Think\Controller;
class LogController extends CommController {
    /* 文章列表
     *
    */
    public function index() {
        $User = M('login_logs');
        $count = $User->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $User->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
        
    }
    /*删除
     *
    */
    public function delete() {
        $rs = M('login_logs')->where(array('log_id' => I('get.log_id')))->delete();
        if ($rs) {
            $this->success('删除成功', U('Log/index'));
        } else {
            $this->error();
        }
    }
    /*批量删除
     *
    */
    public function deleteAll() {
        foreach ($_POST as $rows) {
            $rs = M('login_logs')->where(array("log_id" => $rows))->delete();
        }
        if ($rs) {
            $this->success('批量删除成功', U('Log/index'));
        } else {
            $this->error('批量删除失败', U('Log/index'));
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
    public function biaoxian_index() {
        $catep = M("article")->where("art_cate_id=81")->getField('art_id', true);
        $tt = array();
        $tt['store_id'] = array('in', $catep);
        $tt['ster'] = array('neq', 0);
        $products = M("dingdan")->where($tt)->order("time desc")->select(); //所有的保险订单 已支付
        $arr = $products;
        foreach ($arr as $k => $v) {
            $user = M("user")->where("id='" . $v['uid'] . "'")->find();
            $arr[$k]['sename'] = $user['sename'];
            $arr[$k]['photo'] = $user['username'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
        }
        $this->assign('list', $arr);
        $this->display();
    }
    function goods_export($goods_list = array()) {
        //  dump($goods_list);
        //  exit;  //导出数据
        $data = array();
        foreach ($goods_list as $k => $goods_info) {
            // $data[$k][id] = $goods_info['id'];   //序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['shouru'] = $goods_info['shouru']; //收入金额
            $data[$k]['zhekou'] = '0'; //折扣金额
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = '保险'; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //导出时间
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
            
        }
        foreach ($data as $field => $v) {
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'shouru') {
                $headArr[] = '收入金额';
            }
            if ($field == 'zhekou') {
                $headArr[] = '折扣金额';
            }
            if ($field == 'shishou') {
                $headArr[] = '实收金额';
            }
            if ($field == 'you') {
                $headArr[] = '邮费';
            }
            if ($field == 'qu') {
                $headArr[] = '支付渠道';
            }
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '保险支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }
    public function daochu() {
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        $catep = M("article")->where("art_cate_id=81")->getField('art_id', true);
        $tt = array();
        $tt['store_id'] = array('in', $catep);
        $tt['ster'] = array('neq', 0);
        $products = M("dingdan")->where($tt)->order("time desc")->select(); //所有的保险订单 已支付
        $arr = array();
        foreach ($products as $k => $v) {
            if ($v['time'] > $kaitime and $v['time'] < $endtime) {
                array_push($arr, $v);
            }
        }
        foreach ($arr as $k => $v) {
            $user = M("user")->where("id='" . $v['uid'] . "'")->find();
            $arr[$k]['sename'] = $user['sename'];
            $arr[$k]['photo'] = $user['username'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $gong = M("gongying")->where("id='" . $store['gong'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
            $arr[$k]['gong'] = $gong['name'];
        }
        $this->goods_export($arr);
    }
    public function huodong_index() {
        $tt = array();
        $tt['h_id'] = array('neq', 'null');
        $tt['ster'] = array('neq', '0');
        $products = M("dingdan")->where($tt)->order("time desc")->select(); //所有的活动订单 已支付
        $arr = $products;
        foreach ($arr as $k => $v) {
            $user = M("user")->where("id='" . $v['uid'] . "'")->find();
            $arr[$k]['sename'] = $user['sename'];
            $arr[$k]['photo'] = $user['username'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $arr[$k]['you'] = '0';
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
        }
        $this->assign('list', $arr);
        $this->display();
    }
    public function huodong_daochu() {
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        $tt = array();
        $tt['h_id'] = array('neq', 'null');
        $tt['ster'] = array('neq', '0');
        $products = M("dingdan")->where($tt)->order("time desc")->select(); //所有的活动订单 已支付
        $arr = array();
        foreach ($products as $k => $v) {
            if ($v['time'] > $kaitime and $v['time'] < $endtime) {
                array_push($arr, $v);
            }
        }
        foreach ($arr as $k => $v) {
            $user = M("user")->where("id='" . $v['uid'] . "'")->find();
            $arr[$k]['sename'] = $user['sename'];
            $arr[$k]['photo'] = $user['username'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("huodong")->where("id='" . $v['h_id'] . "'")->find();
            $gong = M("gongying")->where("id='" . $store['gong'] . "'")->find();
            $arr[$k]['you'] = '0';
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
            $arr[$k]['gong'] = $gong['name'];
        }
        $this->goods_huodong($arr);
    }
    function goods_huodong($goods_list = array()) {
        $data = array();
        foreach ($goods_list as $k => $goods_info) {
            // $data[$k][id] = $goods_info['id'];   //序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['shouru'] = $goods_info['shouru']; //收入金额
            $data[$k]['zhekou'] = '0'; //折扣金额
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = '城会玩'; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //供应商
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
            
        }
        foreach ($data as $field => $v) {
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'shouru') {
                $headArr[] = '收入金额';
            }
            if ($field == 'zhekou') {
                $headArr[] = '折扣金额';
            }
            if ($field == 'shishou') {
                $headArr[] = '实收金额';
            }
            if ($field == 'you') {
                $headArr[] = '邮费';
            }
            if ($field == 'qu') {
                $headArr[] = '支付渠道';
            }
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '活动支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }

    public function lvyou_index() {
        $Brand = M('dingdan');

        $tt = array();
        $tt['type'] = array('eq', 1);
        $tt['ster'] = array('neq', 0);
        // $tt['tuikuan'] = array('eq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$tt['time'] = array('between', array($kaitime,$endtime));
        	$this->kaitime = $kaitime;
        	$this->endtime = $endtime;
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$tt['name'] = ['like', '%' . $storename . '%'];
        	$this->storename = $storename;
        }
        $gongid=I("gong");
        $this->gongid = $gongid;
        if(!empty($gongid)){
        	$store = M("article")->where("gong='" . $gongid . "'")->getField('art_id', true);
            if ($store) {
                $tt['store_id'] = array('in', $store);
            }
        }
        
        
        $brandList = $Brand->where($tt)->order("time desc")->select(); //所有的旅游订单集合
        $arr = $brandList;
        foreach ($arr as $k => $v) {
            $user = M("user")->where("id='" . $v['uid'] . "'")->find();
            $arr[$k]['sename'] = $v['lianxi'];
            $arr[$k]['photo'] = $v['lianxiphoto'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['qu'] = '微信支付';
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhe'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhe'] = 0;
            }
        }
        $this->assign('list', $arr);
        //所有的旅游供应商
        $gong=M("gongying")->where("cate_id=22")->select();
       $this->assign('gong',$gong);
        $this->display();
    }

    public function lvyou_daochu() {
        $Brand = M('dingdan');
        // $catep = M("cate")->where("cate_p_id=22")->getField('cate_id', true);
        // $t = array();
        // $t['art_cate_id'] = array('in', $catep);
        // $cate = M("article")->where($t)->getField('art_id', true); //所有的旅游产品id集合
        $tt = array();
        // $tt['store_id'] = array('in', $cate);
        $tt['type'] = array('eq', 1);
        $tt['ster'] = array('neq', 0);
        // $tt['tuikuan'] = array('eq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$tt['time'] = array('between', array($kaitime,$endtime));
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$tt['name'] =$storename;
        }
        $gong=I("gong");
        if(!empty($gong)){
        	$store=M("article")->where("gong='".$gong."'")->getField('art_id',true);
        	$tt['store_id']=array('in',$store);
        }
        $arr = $Brand->where($tt)->order("time desc")->select(); //所有的旅游订单集合
        
        foreach ($arr as $k => $v) {
            $user = M("user")->where("id='" . $v['uid'] . "'")->find();
            $arr[$k]['sename'] = $v['lianxi'];
            $arr[$k]['photo'] = $v['lianxiphoto'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
             $arr[$k]['yewu'] = $cate['cate_name'];
            $gong = M("gongying")->where("id='" . $store['gong'] . "'")->find();
            if(empty($gong)){
            	 $arr[$k]['gong'] = '暂无供应商';
            }else{
            	  $arr[$k]['gong'] = $gong['name'];
            }
            if(empty($store['youdi'])){
            	 $arr[$k]['you'] = '0';
            }else{
            	 $arr[$k]['you'] = $store['youdi'];
            }
            
             if(empty($store['jinjia'])){
            	 $arr[$k]['jiesuan'] = '0';
            }else{
            	 $arr[$k]['jiesuan'] = $store['jinjia'];
            }
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
            $arr[$k]['num'] = $v['num'];
            $arr[$k]['jiage'] = $v['jiage'];
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhekou'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhekou'] = 0;
            }
        }
         
        if (empty($arr)) {
            $this->success('暂无数据', U('Log/lvyou_index'));
        } else {
            $this->goods_lvyou($arr);
        }
        
       
    }

    // 财务管理-旅游订单
    function goods_lvyou($goods_list = array()) {
        $data = array();

        $i = 1;
        $total_num = 0;
        $total_shishou = 0;
        foreach ($goods_list as $k => $goods_info) {
            $data[$k]['id'] = $i++;   //序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['jiage'] = $goods_info['jiage']; //单价
            $data[$k]['num'] = $goods_info['num']; //数量
            $total_num += $goods_info['num'];
            $data[$k]['zhekou'] = $goods_info['zhekou']; //折扣金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $total_shishou += $goods_info['zong'];
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = $goods_info['yewu']; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //供应商
            $data[$k]['jiesuan'] = $goods_info['jiesuan']; //供应商
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
        }
        // 尾部插入合计
        array_push($data, [
            'id' => '合计',
            'pay' => '',
            'storename' => '',
            'zhifu_time' => '',
            'user' => '',
            'sename' => '',
            'jiage' => '',
            'num' => $total_num,
            'zhekou' => '',
            'you' => '',
            'shishou' => $total_shishou,
            'qu' => '',
            'fen' => '',
            'zhuang' => '',
            'gong' => '',
            'jiesuan' => '',
            'daotime' => ''
        ]);

        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'jiage') {
                $headArr[] = '单价';
            }
            if ($field == 'num') {
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
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
             if ($field == 'jiesuan') {
                $headArr[] = '结算价';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '旅游支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }
    public function che_index() {
        $Brand = M('dingdan');

        $tt = array();
        $tt['type'] = array('eq', 2);
        $tt['ster'] = array('neq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$tt['time'] = array('between', array($kaitime,$endtime));
        	$this->kaitime=$kaitime;
        	$this->endtime=$endtime;
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$tt['name'] =$storename;
        	$this->storename=$storename;
        }
        $gongid=I("gong");
        if(!empty($gongid)){
        	$store=M("article")->where("gong='".$gongid."'")->getField('art_id',true);
        	if ($store) {
                $tt['store_id']=array('in',$store);
            }
        	$this->gongid=$gongid;
        }
        $fuwuid=I("fuwu");
        if(!empty($fuwuid)){
        	$pay=M("fuwu_paidan")->where("fu_id='".$fuwuid."'")->getField('dingid',true);
        	if ($pay) {
                $tt['id']=array('in',$pay);
            }
        	$this->fuwuid=$fuwuid;
        }
        
        $count = $Brand->where($tt)->count(); //所有的车订单集合 已支付
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        $arr = $Brand->where($tt)->order("time desc") -> limit($Page->firstRow.','.$Page->listRows) -> select();
       
        foreach ($arr as $k => $v) {
            $address = M("address")->where("id='" . $v['addressid'] . "'")->find();
            $arr[$k]['sename'] = $address['name'];
            $arr[$k]['photo'] = $address['dian'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['qu'] = '微信支付';
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhe'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhe'] = 0;
            }
        }
           $this->assign('list', $arr);
       
        $this -> assign('page',$show);
         //所有的车管家供应商
        $gong=M("gongying")->where("cate_id=18")->select();
        $this->assign('gong',$gong);
        
        //所有的车管家服务商
        $fuwu=M("fuwu")->where("cate_id=18")->select();
        $this->assign('fuwu',$fuwu);
        $this->display();
    }
    public function teiwa_index() {
        $Brand = M('dingdan');

        $tt = array();
        $tt['type'] = array('eq', 3);
        $tt['ster'] = array('neq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
            $tt['time'] = array('between', array($kaitime,$endtime));
            $this->kaitime=$kaitime;
            $this->endtime=$endtime;
        }
        $storename=I("storename");
        if(!empty($storename)){
            $tt['name'] =$storename;
            $this->storename=$storename;
        }
        $gongid=I("gong");
        if(!empty($gongid)){
            $store=M("article")->where("gong='".$gongid."'")->getField('art_id',true);
            if ($store) {
                $tt['store_id']=array('in',$store);
            }
            $this->gongid=$gongid;
        }
        $fuwuid=I("fuwu");
        if(!empty($fuwuid)){
            $pay=M("fuwu_paidan")->where("fu_id='".$fuwuid."'")->getField('dingid',true);
            if ($pay) {
                $tt['id']=array('in',$pay);
            }
            $this->fuwuid=$fuwuid;
        }
        
        $count = $Brand->where($tt)->count(); //所有的车订单集合 已支付
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        $arr = $Brand->where($tt)->order("time desc") -> limit($Page->firstRow.','.$Page->listRows) -> select();
       
        foreach ($arr as $k => $v) {
            $address = M("address")->where("id='" . $v['addressid'] . "'")->find();
            $arr[$k]['sename'] = $address['name'];
            $arr[$k]['photo'] = $address['dian'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['qu'] = '微信支付';
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhe'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhe'] = 0;
            }
        }
           $this->assign('list', $arr);
       
        $this -> assign('page',$show);
         //所有的车管家供应商
        $gong=M("gongying")->where("cate_id=102")->select();
        $this->assign('gong',$gong);
        
        //所有的车管家服务商
        $fuwu=M("fuwu")->where("cate_id=102")->select();
        $this->assign('fuwu',$fuwu);
        $this->display();
    }   
    public function qita_index(){
    	
        $Brand = M('dingdan');

        $tt = array();
        $tt['type'] = array('eq', 4);
        $tt['ster'] = array('neq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$tt['time'] = array('between', array($kaitime,$endtime));
        	$this->kaitime=$kaitime;
        	$this->endtime=$endtime;
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$tt['name'] =$storename;
        	$this->storename=$storename;
        }
        $gongid=I("gong");
        if(!empty($gongid)){
        	$store=M("article")->where("gong='".$gongid."'")->getField('art_id',true);
        	if ($store) {
                $tt['store_id'] = array('in',$store);
            }
        	$this->gongid = $gongid;
        }
        $fuwuid=I("fuwu");
        if(!empty($fuwuid)){
        	$pay=M("fuwu_paidan")->where("fu_id='".$fuwuid."'")->getField('dingid',true);
        	if ($pay) {
                $tt['id']=array('in',$pay);
            }
        	$this->fuwuid=$fuwuid;
        }
        
        $count = $Brand->where($tt)->count(); //所有的车订单集合 已支付
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        $arr = $Brand->where($tt)->order("time desc") -> limit($Page->firstRow.','.$Page->listRows) -> select();
       
        foreach ($arr as $k => $v) {
            $address = M("address")->where("id='" . $v['addressid'] . "'")->find();
            $arr[$k]['sename'] = $address['name'];
            $arr[$k]['photo'] = $address['dian'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['qu'] = '微信支付';
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhe'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhe'] = 0;
            }
        }
           $this->assign('list', $arr);
       
        $this -> assign('page',$show);
         //所有的车管家供应商
        $gong=M("gongying")->where("cate_id=85")->select();
        $this->assign('gong',$gong);
        
        //所有的车管家服务商
        $fuwu=M("fuwu")->where("cate_id=85")->select();
        $this->assign('fuwu',$fuwu);
        $this->display();
    
    }
    
    public function qita_daochu(){
    	
        $Brand = M('dingdan');
        $tt = array();
        $tt['type'] = array('eq', 4);
        $tt['ster'] = array('neq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$tt['time'] = array('between', array($kaitime,$endtime));
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$tt['name'] =$storename;
        }
        $gong=I("gong");
        if(!empty($gong)){
        	$store=M("article")->where("gong='".$gong."'")->getField('art_id',true);
        	$tt['store_id']=array('in',$store);
        }
        $fuwu=I("fuwu");
        if(!empty($fuwu)){
        	$pay=M("fuwu_paidan")->where("fu_id='".$fuwu."'")->getField('dingid',true);
        	$tt['id']=array('in',$pay);
        }
        $arr = $Brand->where($tt)->order("time desc")->select(); //所有的车订单集合 已支付
       
      
        foreach ($arr as $k => $v) {
              $address = M("address")->where("id='" . $v['addressid'] . "'")->find();
            $arr[$k]['sename'] = $address['name'];
            $arr[$k]['photo'] = $address['dian'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
            $arr[$k]['yewu'] = $cate['cate_name'];
            $gong = M("gongying")->where("id='" . $store['gong'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['gong'] = $gong['name'];
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
            if(empty($store['jinjia'])){
            	 $arr[$k]['jiesuan'] = '0';
            }else{
            	 $arr[$k]['jiesuan'] = $store['jinjia'];
            }
            
            $fuwupai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
            if(!empty($fuwupai)){
            	$fuwu=M("fuwu")->where("id='".$fuwupai['fu_id']."'")->find();
            	 $arr[$k]['fuwu'] = $fuwu['name'];
            	  $arr[$k]['fuwufei'] = $store['f_money'];
            	  
            }else{
            	$arr[$k]['fuwu'] = '未分配服务商';
            	$arr[$k]['fuwufei'] = '0';
            }
            $arr[$k]['num'] = $v['num'];
            $arr[$k]['jiage'] = $v['jiage'];
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhekou'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhekou'] = 0;
            }
        }

        if(empty($arr)){
        	$this->success('暂无数据', U('Log/che_index'));
        }else{
        	   $this->goods_qita($arr);
        }
     
    
    }
    
    public function che_daochu() {
        $Brand = M('dingdan');
        $tt = array();
        $tt['type'] = array('eq', 2);
        $tt['ster'] = array('neq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$tt['time'] = array('between', array($kaitime,$endtime));
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$tt['name'] =$storename;
        }
        $gong=I("gong");
        if(!empty($gong)){
        	$store=M("article")->where("gong='".$gong."'")->getField('art_id',true);
        	$tt['store_id']=array('in',$store);
        }
        $fuwu=I("fuwu");
        if(!empty($fuwu)){
        	$pay=M("fuwu_paidan")->where("fu_id='".$fuwu."'")->getField('dingid',true);
        	$tt['id']=array('in',$pay);
        }
        $arr = $Brand->where($tt)->order("time desc")->select(); //所有的车订单集合 已支付
        foreach ($arr as $k => $v) {
              $address = M("address")->where("id='" . $v['addressid'] . "'")->find();
            $arr[$k]['sename'] = $address['name'];
            $arr[$k]['photo'] = $address['dian'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
            $arr[$k]['yewu'] = $cate['cate_name'];
            $gong = M("gongying")->where("id='" . $store['gong'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['gong'] = $gong['name'];
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
            if(empty($v['jin'])){
            	$arr[$k]['jiesuan'] = '0';
            }else{
            	$arr[$k]['jiesuan'] = $v['jin'];
            }
            $fuwupai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
            if(!empty($fuwupai)){
            	$fuwu=M("fuwu")->where("id='".$fuwupai['fu_id']."'")->find();
            	$arr[$k]['fuwu'] = $fuwu['name'];
            	$arr[$k]['fuwufei'] = $store['f_money']; 
            }else{
            	$arr[$k]['fuwu'] = '未分配服务商';
            	$arr[$k]['fuwufei'] = '0';
            }
            $arr[$k]['num'] = $v['num'];
            $arr[$k]['jiage'] = $v['jiage']; 
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhekou'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhekou'] = 0;
            }
        }
        if(empty($arr)){
        	$this->success('暂无数据', U('Log/che_index'));
        }else{
        	$this->goods_che($arr);
        }
    }
    public function teiwa_daochu() {
        $Brand = M('dingdan');

        $tt = array();
        $tt['type'] = array('eq', 3);
        $tt['ster'] = array('neq', 0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
            $tt['time'] = array('between', array($kaitime,$endtime));
        }
        $storename=I("storename");
        if(!empty($storename)){
            $tt['name'] =$storename;
        }
        $gong=I("gong");
        if(!empty($gong)){
            $store=M("article")->where("gong='".$gong."'")->getField('art_id',true);
            $tt['store_id']=array('in',$store);
        }
        $fuwu=I("fuwu");
        if(!empty($fuwu)){
            $pay=M("fuwu_paidan")->where("fu_id='".$fuwu."'")->getField('dingid',true);
            $tt['id']=array('in',$pay);
        }
        $arr = $Brand->where($tt)->order("time desc")->select(); //所有的车订单集合 已支付
        foreach ($arr as $k => $v) {
              $address = M("address")->where("id='" . $v['addressid'] . "'")->find();
            $arr[$k]['sename'] = $address['name'];
            $arr[$k]['photo'] = $address['dian'];
            if ($v['ster'] == 0) {
                $arr[$k]['zhuangtai'] = '未支付';
            } else {
                $arr[$k]['zhuangtai'] = '已支付';
            }
            $arr[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $arr[$k]['daotime'] = date('Y-m-d H:i:s', time());
            if (empty($v['num'])) {
                $arr[$k]['shouru'] = $v['zong'];
            } else {
                $arr[$k]['shouru'] = $v['jiage'] * $v['num'];
            }
            $store = M("article")->where("art_id='" . $v['store_id'] . "'")->find();
            $cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
            $arr[$k]['yewu'] = $cate['cate_name'];
            $gong = M("gongying")->where("id='" . $store['gong'] . "'")->find();
            $arr[$k]['you'] = $store['youdi'];
            $arr[$k]['gong'] = $gong['name'];
            $arr[$k]['qu'] = '微信支付';
            $arr[$k]['zhe'] = '0';
            if(empty($v['jin'])){
                $arr[$k]['jiesuan'] = '0';
            }else{
                $arr[$k]['jiesuan'] = $v['jin'];
            }
            $fuwupai=M("fuwu_paidan")->where("dingid='".$v['id']."'")->find();
            if(!empty($fuwupai)){
                $fuwu=M("fuwu")->where("id='".$fuwupai['fu_id']."'")->find();
                $arr[$k]['fuwu'] = $fuwu['name'];
                $arr[$k]['fuwufei'] = $store['f_money']; 
            }else{
                $arr[$k]['fuwu'] = '未分配服务商';
                $arr[$k]['fuwufei'] = '0';
            }
            $arr[$k]['num'] = $v['num'];
            $arr[$k]['jiage'] = $v['jiage']; 
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhekou'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhekou'] = 0;
            }
        }
        if(empty($arr)){
            $this->success('暂无数据', U('Log/teiwa_index'));
        }else{
            $this->goods_teiwa($arr);
        }
    }

    function goods_teiwa($goods_list = array()) {
        $data = array();
        $i = 1;
        $total_num = 0;
        $total_shishou = 0;
        foreach ($goods_list as $k => $goods_info) {
            $data[$k]['id'] = $i++;   //序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['jiage'] = $goods_info['jiage']; //单价
            $data[$k]['num'] = $goods_info['num']; //数量
            $total_num += $goods_info['num'];
            $data[$k]['zhekou'] = $goods_info['zhekou']; //折扣金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $total_shishou += $goods_info['zong'];
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = $goods_info['yewu']; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //状态
            $data[$k]['jiesuan'] = $goods_info['jiesuan']; //状态
            $data[$k]['fuwu'] = $goods_info['fuwu'];
            $data[$k]['fuwufei'] = $goods_info['fuwufei'];
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
        }
        // 尾部插入合计
        array_push($data, [
            'id' => '合计',
            'pay' => '',
            'storename' => '',
            'zhifu_time' => '',
            'user' => '',
            'sename' => '',
            'jiage' => '',
            'num' => $total_num,
            'zhekou' => '',
            'you' => '',
            'shishou' => $total_shishou,
            'qu' => '',
            'fen' => '',
            'zhuang' => '',
            'gong' => '',
            'jiesuan' => '',
            'daotime' => ''
        ]);
        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'jiage') {
                $headArr[] = '单价';
            }
            if ($field == 'num') {
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
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
            if ($field == 'jiesuan') {
                $headArr[] = '结算价';
            }
            if ($field == 'fuwu') {
                $headArr[] = '服务商';
            }
            if ($field == 'fuwufei') {
                $headArr[] = '服务费';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '忒娃支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }

    function goods_qita($goods_list = array()){
        $data = array();
        $i = 1;
        $total_num = 0;
        $total_shishou = 0;
        foreach ($goods_list as $k => $goods_info) {
            $data[$k]['id'] = $i++;   // 序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['jiage'] = $goods_info['jiage']; //单价
            $data[$k]['num'] = $goods_info['num']; //数量
            $total_num += $goods_info['num'];
            $data[$k]['zhekou'] = $goods_info['zhekou']; //折扣金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $total_shishou += $goods_info['zong'];
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = $goods_info['yewu']; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //状态
            $data[$k]['jiesuan'] = $goods_info['jiesuan']; //状态
            $data[$k]['fuwu'] = $goods_info['fuwu'];
            $data[$k]['fuwufei'] = $goods_info['fuwufei'];
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
        }
        // 尾部插入合计
        array_push($data, [
            'id' => '合计',
            'pay' => '',
            'storename' => '',
            'zhifu_time' => '',
            'user' => '',
            'sename' => '',
            'jiage' => '',
            'num' => $total_num,
            'zhekou' => '',
            'you' => '',
            'shishou' => $total_shishou,
            'qu' => '',
            'fen' => '',
            'zhuang' => '',
            'gong' => '',
            'jiesuan' => '',
            'daotime' => ''
        ]);

        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'jiage') {
                $headArr[] = '单价';
            }
            if ($field == 'num') {
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
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
            if ($field == 'jiesuan') {
                $headArr[] = '结算价';
            }
            if ($field == 'fuwu') {
                $headArr[] = '服务商';
            }
            if ($field == 'fuwufei') {
                $headArr[] = '服务费';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '其他商品支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }

    function goods_che($goods_list = array()) {
        $data = array();
        $i = 1;
        $total_num = 0;
        $total_shishou = 0;
        foreach ($goods_list as $k => $goods_info) {
            $data[$k]['id'] = $i++;   //序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['jiage'] = $goods_info['jiage']; //单价
            $data[$k]['num'] = $goods_info['num']; //数量
            $total_num += $goods_info['num'];
            $data[$k]['zhekou'] = $goods_info['zhekou']; //折扣金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $total_shishou += $goods_info['zong'];
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = $goods_info['yewu']; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //状态
            $data[$k]['jiesuan'] = $goods_info['jiesuan']; //状态
            $data[$k]['fuwu'] = $goods_info['fuwu'];
            $data[$k]['fuwufei'] = $goods_info['fuwufei'];
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
        }
        // 尾部插入合计
        array_push($data, [
            'id' => '合计',
            'pay' => '',
            'storename' => '',
            'zhifu_time' => '',
            'user' => '',
            'sename' => '',
            'jiage' => '',
            'num' => $total_num,
            'zhekou' => '',
            'you' => '',
            'shishou' => $total_shishou,
            'qu' => '',
            'fen' => '',
            'zhuang' => '',
            'gong' => '',
            'jiesuan' => '',
            'daotime' => ''
        ]);
        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'jiage') {
                $headArr[] = '单价';
            }
            if ($field == 'num') {
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
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
            if ($field == 'jiesuan') {
                $headArr[] = '结算价';
            }
            if ($field == 'fuwu') {
                $headArr[] = '服务商';
            }
            if ($field == 'fuwufei') {
                $headArr[] = '服务费';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '车支付信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }

    // 微信对账管理-退款订单
    public function tui_index() {
        
        $Picture = M('dingdan');
        $where['tuikuan']=array('neq',0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$where['tuikuan_endtime'] = array('between', array($kaitime,$endtime));
        	$this->kaitime=$kaitime;
        	$this->endtime=$endtime;
        }
        $yewuid=I("yewu");
        if(!empty($yewuid)){
        	$cate=M("cate")->where("cate_p_id={$yewuid}")->getField('cate_id',true);
        	$yuo=array();
        	if ($cate) {
                $yuo['art_cate_id']=array('in',$cate);
            }
        	$store=M("article")->where($yuo)->getField('art_id',true);
        	if ($store) {
                $where['store_id'] =array('in',$store);
            }
        	$this->yewuid=$yewuid;
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$where['name'] =$storename;
        	$this->storename=$storename;
        }
        $gongid=I("gong");
        if(!empty($gongid)){
        	$store=M("article")->where("gong='".$gongid."'")->getField('art_id',true);
        	if ($store) {
                $where['store_id']=array('in',$store);
            }
        	$this->gongid=$gongid;
        }
        $fuwuid=I("fuwu");
        if(!empty($fuwuid)){
        	$pay=M("fuwu_paidan")->where("fu_id='".$fuwuid."'")->getField('dingid',true);
        	if ($pay) {
                $where['id']=array('in',$pay);
            }
        	$this->fuwuid=$fuwuid;
        }
        $count = $Picture -> where($where) -> count();   
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();
        //第一页
        $pictureList = $Picture -> where($where) -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($pictureList as $k=>$v){
        	 $user=M("user")->where("id='".$v['uid']."'")->find();
        	 $pictureList[$k]['user']=$user['sename'];
        	  $pictureList[$k]['photo']=$user['photo'];
        	  $store=M("article")->where("art_id='".$v['store_id']."'")->find();
        	  $cate=M("cate")->where("cate_id='".$store['art_cate_id']."'")->find();
        	  $pictureList[$k]['fen']=$cate['cate_name'];
        }
       
        $this ->assign('picture',$pictureList);

        $this->assign('page',$show);
       //所有的车管家供应商
        $gong=M("gongying")->where("cate_id=18")->select();
        $this->assign('gong',$gong);
        
        //所有的车管家服务商
        $fuwu=M("fuwu")->where("cate_id=18")->select();
        $this->assign('fuwu',$fuwu);
        //一级分类
        $yewu=M("cate")->where("cate_id=18||cate_id=22||cate_id=85||cate_id=102")->select();
        $this->yewu=$yewu;
        $this -> display();
    }

    // 微信对账管理-退款订单导出
    public function tui_daochu() {
    	$Picture = M('dingdan');
        $where['tuikuan']=array('neq',0);
        $where['ster']=array('neq',0);
        $kaitime = I("kaitime");
        $endtime = I("endtime");
        if(!empty($kaitime) && !empty($endtime)){
        	$where['tuikuan_endtime'] = array('between', array($kaitime,$endtime));
        }
        $storename=I("storename");
        if(!empty($storename)){
        	$where['name'] =$storename;
        }
        $yewuid=I("yewu");
        if(!empty($yewuid)){
        	$cate=M("cate")->where("cate_p_id={$yewuid}")->getField('cate_id',true);
        	$yuo=array();
        	$yuo['art_cate_id']=array('in',$cate);
        	$store=M("article")->where($yuo)->getField('art_id',true);
        	$where['store_id'] =array('in',$store);
        	$this->yewuid=$yewuid;
        }
        
        $gong=I("gong");
        if(!empty($gong)){
        	$store=M("article")->where("gong='".$gong."'")->getField('art_id',true);
        	$where['store_id']=array('in',$store);
        }
        $fuwu=I("fuwu");
        if(!empty($fuwu)){
        	$pay=M("fuwu_paidan")->where("fu_id='".$fuwu."'")->getField('dingid',true);
        	$where['id']=array('in',$pay);
        }
        
        $pictureList = $Picture -> where($where) -> order('time desc')  -> select();
        
        foreach($pictureList as $k=>$v){
        	$user=M("user")->where("id='".$v['uid']."'")->find();
        	$pictureList[$k]['sename']=$user['sename'];//下单人姓名
        	$pictureList[$k]['photo']=$user['photo'];//下单人手机号
        	$pictureList[$k]['zhuangtai'] = '已退款';
        	$pictureList[$k]['shouru'] = $v['jiage'] * $v['num'];
        	$pictureList[$k]['qu'] = '微信支付';
            $pictureList[$k]['zhe'] = '0';
            $pictureList[$k]['daotime'] = date('Y-m-d H:i:s', time());
            $pictureList[$k]['tuikuan_ktime'] = $v['tuikuan_ktime'];
            $pictureList[$k]['tuikuan_endtime'] = $v['tuikuan_endtime'];
            $store=M("article")->where("art_id='".$v['store_id']."'")->find();
            $gong=M("gongying")->where("id='".$store['gong']."'")->find();
            $pictureList[$k]['you']=$store['youdi'];
            $pictureList[$k]['gong']=$gong['name'];
            $pictureList[$k]['pay'] = ' ' . $v['paysn'] . ' ';
            $pictureList[$k]['num'] = $v['num'];
            $pictureList[$k]['jiage'] = $v['jiage'];
            // 折扣价=营销价-进货价
            if ($v['cuxiao'] == 1) {
                $arr[$k]['zhekou'] = $v['jiage'] - $v['jin'];
            }else{
                $arr[$k]['zhekou'] = 0;
            }
        }
        $arr=$pictureList;
        $this->goods_tui($arr);
    }

    function goods_tui($goods_list = array()) {
        $data = [];
        $i = 1;
        $total_num = 0;
        $total_shishou = 0;
        foreach ($goods_list as $k => $goods_info) {
            $data[$k]['id'] = $i++;   //序号
            $data[$k]['pay'] = $goods_info['pay']; //订单号
            $data[$k]['storename'] = $goods_info['name']; //产品名称
            $data[$k]['zhifu_time'] = $goods_info['zhifu_time']; //支付时间
            $data[$k]['user'] = $goods_info['photo']; //下单人手机号
            $data[$k]['sename'] = $goods_info['sename']; //下单人姓名
            $data[$k]['jiage'] = $goods_info['jiage']; //单价
            $data[$k]['num'] = $goods_info['num']; //数量
            $total_num += $goods_info['num'];
            $data[$k]['zhekou'] = $goods_info['zhekou']; //折扣金额
            $data[$k]['you'] = $goods_info['you']; //邮费
            $data[$k]['shishou'] = $goods_info['zong']; //实收金额
            $total_shishou += $goods_info['zong'];
            $data[$k]['qu'] = '微信支付'; //交易渠道
            $data[$k]['fen'] = '退款业务'; //所属类型
            $data[$k]['zhuang'] = $goods_info['zhuangtai']; //状态
            $data[$k]['gong'] = $goods_info['gong']; //供应商
            $data[$k]['tuikuan_ktime'] = $goods_info['tuikuan_ktime']; // 申请时间
            $data[$k]['tuikuan_endtime'] = $goods_info['tuikuan_endtime']; // 退款时间
            $data[$k]['daotime'] = $goods_info['daotime']; //导出时间
        }
        // 尾部插入合计
        array_push($data, [
            'id' => '合计',
            'pay' => '',
            'storename' => '',
            'zhifu_time' => '',
            'user' => '',
            'sename' => '',
            'jiage' => '',
            'num' => $total_num,
            'zhekou' => '',
            'you' => '',
            'shishou' => $total_shishou,
            'qu' => '',
            'fen' => '',
            'zhuang' => '',
            'gong' => '',
            'tuikuan_ktime' => '',
            'tuikuan_endtime' => '',
            'daotime' => ''
        ]);
        foreach ($data as $field => $v) {
            if ($field == 'id') {
                $headArr[] = '序号';
            }
            if ($field == 'pay') {
                $headArr[] = '订单号';
            }
            if ($field == 'storename') {
                $headArr[] = '产品名称';
            }
            if ($field == 'zhifu_time') {
                $headArr[] = '支付时间';
            }
            if ($field == 'user') {
                $headArr[] = '下单人手机号';
            }
            if ($field == 'sename') {
                $headArr[] = '下单人姓名';
            }
            if ($field == 'jiage') {
                $headArr[] = '价格';
            }
            if ($field == 'num') {
                $headArr[] = '数量';
            }
            if ($field == 'zhekou') {
                $headArr[] = '折扣金额';
            }
            if ($field == 'you') {
                $headArr[] = '邮费';
            }
            if ($field == 'shishou') {
                $headArr[] = '实退金额';
            }
            if ($field == 'qu') {
                $headArr[] = '支付渠道';
            }
            if ($field == 'fen') {
                $headArr[] = '业务类型';
            }
            if ($field == 'zhuang') {
                $headArr[] = '状态';
            }
            if ($field == 'gong') {
                $headArr[] = '供应商';
            }
            if ($field == 'tuikuan_ktime') {
                $headArr[] = '申请时间';
            }
            if ($field == 'tuikuan_endtime') {
                $headArr[] = '退款时间';
            }
            if ($field == 'daotime') {
                $headArr[] = '导出时间';
            }
        }
        $filename = '退款信息'; //导出文件名称
        $this->getExcel($filename, $headArr, $data);
    }
    //旅游供应商列表
    public function gongyin() {
        $Article = M("gongying");
        $count = $Article->where("cate_id=22")->count();
        $Page = new \Think\Page($count, 30);
        $show = $Page->show();
        //第一页
        $products = $Article->where("cate_id=22")->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($products as $k => $v) {
            if ($v['cate_id'] == '1') {
                $products[$k]['fen'] = '城会玩';
            } else {
                $tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
                $products[$k]['fen'] = $tt['cate_name'];
            }
        }
        $this->assign('list', $products);
        $this->assign('page', $show);
        $this->display();
    }
    //车供应商列表
    public function gongyin_che() {
        $Article = M("gongying");
        $count = $Article->where("cate_id=18")->count();
        $Page = new \Think\Page($count, 30);
        $show = $Page->show();
        //第一页
        $products = $Article->where("cate_id=18")->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($products as $k => $v) {
            if ($v['cate_id'] == '1') {
                $products[$k]['fen'] = '城会玩';
            } else {
                $tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
                $products[$k]['fen'] = $tt['cate_name'];
            }
        }
        $this->assign('list', $products);
        $this->assign('page', $show);
        $this->display();
    }
    //忒娃供应商列表
    public function gongyin_teiwa() {
        $Article = M("gongying");
        $count = $Article->where("cate_id=102")->count();
        $Page = new \Think\Page($count, 30);
        $show = $Page->show();
        //第一页
        $products = $Article->where("cate_id=102")->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($products as $k => $v) {
            if ($v['cate_id'] == '1') {
                $products[$k]['fen'] = '城会玩';
            } else {
                $tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
                $products[$k]['fen'] = $tt['cate_name'];
            }
        }
        $this->assign('list', $products);
        $this->assign('page', $show);
        $this->display();
    }
    //保险供应商列表
    public function gongyin_bao() {
        $Article = M("gongying");
        $count = $Article->where("cate_id=81")->count();
        $Page = new \Think\Page($count, 30);
        $show = $Page->show();
        //第一页
        $products = $Article->where("cate_id=81")->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($products as $k => $v) {
            if ($v['cate_id'] == '1') {
                $products[$k]['fen'] = '城会玩';
            } else {
                $tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
                $products[$k]['fen'] = $tt['cate_name'];
            }
        }
        $this->assign('list', $products);
        $this->assign('page', $show);
        $this->display();
    }
    //其他供应商列表
    public function gongyin_qi() {
        $Article = M("gongying");
        $count = $Article->where("cate_id=85")->count();
        $Page = new \Think\Page($count, 30);
        $show = $Page->show();
        //第一页
        $products = $Article->where("cate_id=85")->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($products as $k => $v) {
            if ($v['cate_id'] == '1') {
                $products[$k]['fen'] = '城会玩';
            } else {
                $tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
                $products[$k]['fen'] = $tt['cate_name'];
            }
        }
        $this->assign('list', $products);
        $this->assign('page', $show);
        $this->display();
    }
    //城会玩供应商列表
    public function gongyin_cheng() {
        $Article = M("gongying");
        $count = $Article->where("cate_id=1")->count();
        $Page = new \Think\Page($count, 30);
        $show = $Page->show();
        //第一页
        $products = $Article->where("cate_id=1")->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($products as $k => $v) {
            if ($v['cate_id'] == '1') {
                $products[$k]['fen'] = '城会玩';
            } else {
                $tt = M("cate")->where("cate_id= '" . $v['cate_id'] . "'")->find();
                $products[$k]['fen'] = $tt['cate_name'];
            }
        }
        $this->assign('list', $products);
        $this->assign('page', $show);
        $this->display();
    }

    // 供应商产品
    public function gong_chanpin(){
        $id = I('get.id/d');
        $name = I('get.name');

        $startDate = I('get.kaitime', '');
        $endDate = I('get.endtime', '');
        $this->assign('startDate', $startDate);
        $this->assign('endDate', $endDate);

        $ming = I('get.ming', '');
        $this->assign('ming', $ming);
        if (isset($ming) && !empty($ming)) {
            $map['product_title'] = ['like', '%' . $ming . '%'];
        }

        $db = M('article');
        $map['gong'] = ['eq', $id];
        $count = $db->where($map)->count();   
        $Page = new \Think\Page($count, 10);
        $list = $db->where($map)->order('art_date desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $zjsj = 0; // 总结算价
        $zxl = 0; // 总销量
        foreach($list as $k => $v){
            $list[$k][] = $v; 
            $cate = M("cate")->where(['cate_id' => $v['art_cate_id']])->find();
            $list[$k]['fen'] = $cate['cate_name'];
            unset($map);
            $map['store_id'] = ['eq', $v['art_id']];
            $map['ster'] = ['eq', 4];
            $map['tuikuan'] = ['eq', 0];

            // 时间筛选销量
            if(!empty($startDate) && !empty($endDate)){
                $map['time'] = ['between', [$startDate, $endDate]];
            }

            // 销量
            $xlnum = M('dingdan')->where($map)->sum('num');
            $xlnum = $xlnum ? $xlnum : 0;
            $list[$k]['xl'] = $xlnum;

            // 结算价
            $jsj = 0;
            $dingdan_list = M('dingdan')->where($map)->select();
            if ($dingdan_list) {
                foreach ($dingdan_list as $key => $value) {
                    $jin = empty($value['jin']) ? 0 : $value['jin'];
                    $jsj += $value['num'] * $jin;
                }
            }
            $list[$k]['jsj'] = $jsj;

            $zxl += $xlnum;
            $zjsj += $jsj;
        }
        $this->assign('id', $id);
        $this->assign('name', $name);
        $this->assign('count', $count);
        $this->assign('zxl', $zxl);
        $this->assign('zjsj', $zjsj);
        $this->assign('list', $list);
        $this->assign('page', $Page->show());
        $this->display();
    }

    // 订单列表
    public function gong_order(){
        $art_id = I('get.art_id/d');
        if (empty($art_id)) die('参数错误！');
        $db = M('dingdan');

        $ming = I('ming');
        $this->assign('ming', $ming);
        if (isset($ming) && !empty($ming)) {
            $map['paysn'] = ['like', '%' . $ming . '%'];
        }

        $map['store_id'] = ['eq', $art_id];
        $map['ster'] = ['eq', 4];
        $map['tuikuan'] = ['eq', 0];
        $count = $db->where($map)->count();   
        $Page = new \Think\Page($count, 10);
        $list = $db->where($map)->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('art_id', $art_id);
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->assign('page', $Page->show());
        $this->assign('emptyStr', '<tr class="text-center"><td colspan="20" class="active"><strong>暂无订单数据！</strong></td></tr>');
        $this->display();
    }

    // 供应商产品销量表
    public function gong_export() {
        $id = I('get.id/d');
        $name = I('get.name');
        $startDate = I('get.kaitime', 0);
        $endDate = I('get.endtime', 0);

        $db = M('article');
        $map['gong'] = ['eq', $id];
        $goods_list = $db->where($map)->order('art_date desc')->select();

        $data = [];
        foreach($goods_list as $k => $v){
            $data[$k]['art_id'] = $k + 1;   // 序号
            $data[$k]['name'] = $name;
            $data[$k]['product_title'] = $v['product_title']; // 产品名称
            $cate = M("cate")->where(['cate_id' => $v['art_cate_id']])->find();
            $data[$k]['fen'] = $cate['cate_name']; // 产品类别
            unset($map);
            $map['store_id'] = ['eq', $v['art_id']];
            $map['ster'] = ['eq', 4];
            $map['tuikuan'] = ['eq', 0];

            // 时间筛选销量
            if(!empty($startDate) && !empty($endDate)){
                $map['time'] = ['between', [$startDate, $endDate]];
            }

            // 销量
            $xlnum = M('dingdan')->where($map)->sum('num');
            $xlnum = $xlnum ? $xlnum : 0;
            $data[$k]['xl'] = $xlnum;

            // 结算价
            $jsj = 0;
            $dingdan_list = M('dingdan')->where($map)->select();
            if ($dingdan_list) {
                foreach ($dingdan_list as $key => $value) {
                    $jin = empty($value['jin']) ? 0 : $value['jin'];
                    $jsj += $value['num'] * $jin;
                }
            }
            $data[$k]['jsj'] = $jsj;
            
            $data[$k]['art_date'] = $v['art_date']; // 添加时间
        }

        foreach ($data as $field => $v) {
            if ($field == 'art_id') {
                $headArr[] = '序号';
            }
            if ($field == 'name') {
                $headArr[] = '供应商名称';
            }
            if ($field == 'product_title') {
                $headArr[] = '产品名称';
            }
            if ($field == 'fen') {
                $headArr[] = '产品类别';
            }
            if ($field == 'xl') {
                $headArr[] = '销量';
            }
            if ($field == 'jsj') {
                $headArr[] = '结算价';
            }
            if ($field == 'art_date') {
                $headArr[] = '添加时间';
            }
        }
        $this->getExcel('产品销量表-' . $name, $headArr, $data);
    }

    //添加供应商
    public function gong_add() {
        //分类
        $where['cate_id'] = ['in', [18, 22,85,102]];
        $list2 = M('cate')->where($where)->order('cate_order asc')->select();
        $this->assign('list2', $list2);
        $this->display();
    }

    public function gong_addcl() {
        $data = I("post.");
        $rules = [
            ['art_cate_id', 'require', '请选择所属分类！'], 
            ['art_title', 'require', '请输入供应商名称！'], 
            ['art_dianhua', 'require', '请输入供应商电话！'], 
            ['art_lianxi', 'require', '请输入联系人！'], 
            ['art_content', 'require', '请输入介绍内容！'], 
            ['address', 'require', '请输入地址信息！'], 
            ['username', 'require', '请输入登录帐号！'], 
            ['username', '', '帐号已经存在！', 0, 'unique', 1]
        ];
        $db = M("gongying");
        if (!$db->validate($rules)->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($db->getError());
        } else {
            $config = [
                'rootPath'  => './Uploads/gongying/',
                'savePath'  => '',
                'maxSize'   => 19145728000,
                'exts'      => array('jpg', 'gif', 'png', 'jpeg')
            ];
            // LOGO图
            if (!empty($_FILES['logo'])){
                $upload = new \Think\Upload($config);
                $logo = $upload->uploadOne($_FILES['logo']);
                $logoUrl =  '/Uploads/gongying/'. $logo['savepath'] . $logo['savename'];
            }else{
                $this->error('请上传logo！');die;
            }
            // 封面图
            if (!empty($_FILES['base_img'])){
                $upload = new \Think\Upload($config);
                $base_img = $upload->uploadOne($_FILES['base_img']);
                $base_imgUrl =  '/Uploads/gongying/'. $base_img['savepath'] . $base_img['savename'];
            }else{
                $this->error('请上传封面图！');die;
            }

            // 验证通过 可以进行其他数据操作
            $to['cate_id'] = $data['art_cate_id'];
            $to['name'] = $data['art_title'];
            $to['photo'] = $data['art_dianhua'];
            $to['user'] = $data['art_lianxi'];
            $to['logo'] = $logoUrl;
            $to['base_img'] = $base_imgUrl;
            $to['jieshao'] = $data['art_content'];
            $to['address'] = $data['address'];
            $to['username'] = $data['username'];
            if (isset($data['password']) && !empty($data['password'])) {
                $to['password'] = md5($data['password']);
            }
            $to['time'] = date('Y-m-d H:i:s', time());
            $res = $db->add($to);
            if ($res) {
                $this->success('添加成功', U('Log/gongyin'), 1);
            } else {
                $this->success('添加失败', U('Log/gongyin'), 1);
            }
        }
    }

    //删除供应商
    public function gong_delete() {
        $id = I("id");
        $rs = M('gongying')->where(array('id' => $id))->delete();
        if ($rs) {
            $this->success('删除成功', U('Log/gongyin'));
        } else {
            $this->error();
        }
    }

    //编辑供应商
    public function gong_edit() {
        // 分类
        $where['cate_p_id'] = 0;
        $list2 = M('cate')->where($where)->order('cate_order asc')->select();
        $this->assign('list2', $list2);
        $id = I("id");
        $temp = M('gongying')->where(array('id' => $id))->find();
        $this->temp = $temp;
        $this->display();
    }
    //编辑供应商
    public function gong_bian() {
        $data = I("post.");
        $to['cate_id'] = $data['art_cate_id'];
        $to['name'] = $data['art_title'];
        $to['photo'] = $data['art_dianhua'];
        $to['user'] = $data['art_lianxi'];
        

        $data = I("post.");
        $rules = [
            ['art_cate_id', 'require', '请选择所属分类！'], 
            ['art_title', 'require', '请输入供应商名称！'], 
            ['art_dianhua', 'require', '请输入供应商电话！'], 
            ['art_lianxi', 'require', '请输入联系人！'], 
            ['art_content', 'require', '请输入介绍内容！'], 
            ['address', 'require', '请输入地址信息！'], 
            ['username', 'require', '请输入登录帐号！'], 
            ['username', '', '帐号已经存在！', 0, 'unique', 2]
        ];
        $db = M("gongying");
        if (!$db->validate($rules)->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($db->getError());
        } else {
            $config = [
                'rootPath'  => './Uploads/gongying/',
                'savePath'  => '',
                'maxSize'   => 19145728000,
                'exts'      => array('jpg', 'gif', 'png', 'jpeg')
            ];
            // LOGO图
            if (!empty($_FILES['logo']['name']) && !empty($_FILES['logo']['type']) && !empty($_FILES['logo']['tmp_name'])){
                $upload = new \Think\Upload($config);
                $logo = $upload->uploadOne($_FILES['logo']);
                $logoUrl =  '/Uploads/gongying/'. $logo['savepath'] . $logo['savename'];
                $to['logo'] = $logoUrl;
            }

            // 封面图
            if (!empty($_FILES['base_img']['name']) && !empty($_FILES['base_img']['type']) && !empty($_FILES['base_img']['tmp_name'])){
                $upload = new \Think\Upload($config);
                $base_img = $upload->uploadOne($_FILES['base_img']);
                $base_imgUrl =  '/Uploads/gongying/'. $base_img['savepath'] . $base_img['savename'];
                $to['base_img'] = $base_imgUrl;
            }

            // 验证通过 可以进行其他数据操作
            $to['cate_id'] = $data['art_cate_id'];
            $to['name'] = $data['art_title'];
            $to['photo'] = $data['art_dianhua'];
            $to['user'] = $data['art_lianxi'];
            $to['jieshao'] = $data['art_content'];
            $to['address'] = $data['address'];
            $to['username'] = $data['username'];
            if (isset($data['password']) && !empty($data['password'])) {
                $to['password'] = md5($data['password']);
            }
            $to['time'] = date('Y-m-d H:i:s', time());
            $ids = $data['ids'];
            $rs = M('gongying')->where(array('id' => $ids))->save($to);
            if ($rs) {
                $this->success('编辑成功', U('Log/gongyin'));
            } else {
                $this->success('编辑失败', U('Log/gongyin'));
            }
        }
    }

    public function gongdeleteAll() {
        $data = I("post.");
        foreach ($data as $rows) {
            $rs = M('gongying')->where(array("id" => $rows))->delete();
        }
        if ($rs) {
            $this->success('批量删除成功', U('Log/gongyin'));
        } else {
            $this->error('批量删除失败', U('Log/gongyin'));
        }
    }
}
