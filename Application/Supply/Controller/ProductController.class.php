<?php
namespace Supply\Controller;
use Think\Controller;

class ProductController extends CommController {

    // 产品列表
    public function lis(){
        $data = I('');
        $supply_info = $this->supply_info;

        $startDate = I('get.kaitime', '');
        $endDate = I('get.endtime', '');
        $this->assign('startDate', $startDate);
        $this->assign('endDate', $endDate);

        $this->assign('ming', $data['ming']);
        if (isset($data['ming']) && !empty($data['ming'])) {
            $map['product_title'] = ['like', '%' . $data['ming'] . '%'];
        }

        $map['gong'] = ['eq', $supply_info['id']];

        $db = M('article');
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

            $zxl += $xlnum; // 总销量
            $zjsj += $jsj; // 总结算价
        }
        $this->assign('supply_info', $supply_info);
        $this->assign('count', $count);
        $this->assign('zxl', $zxl);
        $this->assign('zjsj', $zjsj);
        $this->assign('list', $list);
        $this->assign('page', $Page->show());
        $this->display();
    }

    // 订单列表
    public function order(){
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

    // 产品详情
    public function det(){
        $supply_info = $this->supply_info;
        $art_id = I('get.art_id/d');
        if (empty($art_id)) die('参数错误！');
        $db = M('article');
        $map['art_id'] = ['eq', $art_id];
        $map['gong'] = ['eq', $supply_info['id']];
        $rs = $db->where($map)->find(); 
        $this->assign('rs', $rs);
        $this->display();
    }

    // 供应商产品销量表
    public function export() {
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
                $headArr[] = '结算金额';
            }
            if ($field == 'art_date') {
                $headArr[] = '添加时间';
            }
        }
        $this->getExcel('产品销量表-' . $name, $headArr, $data);
    }

}