<?php
namespace Serve\Controller;
use Think\Controller;

class OrderController extends CommController {

    public function lis(){
        $ming = I('get.ming', '');

        $msg = '请输入订单号查询！';
        if (!empty($ming)) {
            $this->assign('ming', $ming);
            $map['paysn'] = ['like', '%' . $ming . '%'];

            $serve_info = $this->serve_info;
            $m = M('fuwu_paidan');
            $map['fu_id'] = ['eq', $serve_info['id']];
            $map['state'] = ['eq', 0];
            $count = $m->where($map)->count();
            $Page = new \Think\Page($count, 15);
            $show = $Page->show();

            $res = $m->where($map)->find();
            if ($res) {
                // 服务商
                $res['fuwu_name'] = $serve_info['name'];
                // 订单
                $dingdan = M('dingdan')->where(['id' => $res['dingid']])->find();
                if ($dingdan['tuikuan'] != 0) {
                    $res = false;
                    $msg = "订单【{$ming}】已申请退款！";
                }else{
                    $res['chanpin_name'] = $dingdan['name'];
                    $res['fwl'] = $dingdan['num'];
                    // 商品
                    $chanpin = M('Article')->where(['art_id' => $dingdan['store_id']])->find();
                    $fwf = $dingdan['num'] * $chanpin['f_money'];
                    $res['fwf'] = $fwf;
                    // 商品类别
                    $cate = M('cate')->where(['cate_id' => $chanpin['art_cate_id']])->find();
                    $res['cate_name'] = $cate['cate_name'];  
                }
            }else{
                $res = false;
                $msg = "订单【{$ming}】不存在！";
            }
        }else{
            $res = false;
        }
        $this->assign('id', $serve_info['id']);
        $this->assign('name', $serve_info['name']);
        $this->assign('res', $res);
        $this->assign('msg', $msg);
        $this->display();
    }

    // 已核销
    public function oklis(){
        $ming = I('get.ming', '');
        if (!empty($ming)) {
            $this->assign('ming', $ming);
            $map['paysn'] = ['like', '%' . $ming . '%'];
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

        $phone = I('get.phone', '');
        if (!empty($phone)) {
            $this->assign('phone', $phone);
            $address = M('address')->where(['dian' => $phone])->find();
            $paysns = M('dingdan')->where(['addressid' => $address['id']])->getField('paysn', true);
            if ($paysns) {
                $map['paysn'] = ['in', $paysns];
            }
        }

        $serve_info = $this->serve_info;
        $m = M('fuwu_paidan');
        $map['fu_id'] = ['eq', $serve_info['id']];
        $map['state'] = ['eq', 1];
        $count = $m->where($map)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();

        $list = $m->where($map)->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $zfwl = 0; // 总服务量
        $zfwf = 0; // 总服务费
        foreach($list as $k => $v){
            $list[$k] = $v;
            // 服务商
            $list[$k]['fuwu_name'] = $serve_info['name'];
            // 订单
            $dingdan = M('dingdan')->where(['id' => $v['dingid']])->find();
            $list[$k]['chanpin_name'] = $dingdan['name'];
            $list[$k]['fwl'] = $dingdan['num'];
            $zfwl += $dingdan['num'];
            // 商品
            $chanpin = M('Article')->where(['art_id' => $dingdan['store_id']])->find();
            $fwf = $dingdan['num'] * $chanpin['f_money'];
            $list[$k]['fwf'] = $fwf;
            $zfwf += $fwf;
            // 商品类别
            $cate = M('cate')->where(['cate_id' => $chanpin['art_cate_id']])->find();
            $list[$k]['cate_name'] = $cate['cate_name'];
        }

        $where['cate_p_id'] = 18;
        $cate = M("cate")->where($where)->select();
        $this->assign('cate', $cate);

        $this->assign('id', $serve_info['id']);
        $this->assign('name', $serve_info['name']);
        $this->assign('count', $count);
        $this->assign('zfwl', $zfwl);
        $this->assign('zfwf', $zfwf);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    // 核销派单信息
    public function hexiao(){
        $dingid = I('get.dingid/d');
        if (empty($dingid)) {
            $this->error('参数错误！');die();
        }

        $db = M('dingdan');
        $map = ['id' => $dingid];
        $dingdan = $db->where($map)->find();
        if ($dingdan) {
            $res1 = $db->where($map)->save(['ster' => 4]); // 订单已完成

            $data['state'] = 1;
            $data['wan_time'] = date('Y-m-d H:i:s', time());
            $res2 = M('fuwu_paidan')->where(['dingid' => $dingid])->save($data); // 核销派单信息
            if($res1 || $res2){
                $this->success('恭喜您，核销成功！', U('Order/lis'));
            }else{
                $this->error('抱歉，核销失败，请重试！');
            }
        }else{
            $this->error('数据不存在！');die();
        }
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
        $map['state'] = ['eq', 1];
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
        }
        $this->getExcel('服务商派单表-' . $name, $headArr, $data);
    }


}