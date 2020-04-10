<?php
namespace Wan\Controller;
use Think\Controller;

class IndexController extends PublicController {
	
    public function _initialize(){	
        $user = $_SESSION['user'];
        if(!isset($user) || empty($user)){
            $this->weixindeng();
        }
    }

    public function index(){
        $uid = $_SESSION['user']['id'];
        // 轮播图
        $pmap['id'] = ['in', '60,61,62,63,65'];
        $tu = M("picture")->where($pmap)->select();
        $tuList = [];
        foreach ($tu as $key => $value) {
            if (!empty($value['picture_url'])) {
                $tuList[$key] = $value;
                if (empty($value['url'])) {
                    $tuList[$key]['url'] = U('Index/picture_det', ['id' => $value['id']]);
                }
            }
        }

        $adTop = [];
        $adBottom = [];
        foreach ($tuList as $key => $value) {
            if ($key == 0) {
                $adBottom = $value;
            }
            if ($key == count($tuList) - 1) {
                $adTop = $value;
            }
        }
        $this->adTop = $adTop;
        $this->adBottom = $adBottom;
        $this->tu = $tuList;

        $where['is_sj'] = ['eq', 1];
        $huodong = M('huodong')->where($where)->order('id desc')->select();
        foreach ($huodong as $key => $value) {
            $flag = 1; // 进行中
            if ($value['end_time'] < time()) {
                $flag = 3; // 已结束
            }

            $count = M('baoming')->where(['hid' => $value['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
            $count = $count ? $count : 0;
            if ($count && $count == $value['num']) {
                $flag = 2; // 已售罄
            }
            $sc = 0;
            if (M('shoucang')->where(['uid' => $uid, 'hid' => $value['id']])->find()) {
                $sc = 1;
            }
            $huodong[$key]['sc'] = $sc;
            $huodong[$key]['flag'] = $flag;
        }
        $this->huodong = arr_sort($huodong, 'flag');
        $this->display();
    }

    // 轮播图详情页
    public function picture_det(){
        $id = I('get.id/d');
        $res = M("picture")->where(['id' => $id])->find();
        $this->res = $res;

        // 微信JsApi参数
        $wxapi = new \WxPayApi();
        $timestamp = $this->getMillisecond();
        $nonceStr = $wxapi::getNonceStr();
        $this->jsApiArr = [
            'appId' => \WxPayConfig::APPID,
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'signature' => $this->getSignature($timestamp, $nonceStr)
        ];
        $this->shareImg = 'http://' . $_SERVER['HTTP_HOST'] . $res['picture_url'];


        $this->display();
    }

    // 筛选
    public function sx(){
        $this->week = $this->getWeek(time());
        $this->display();
    }

    /**
     * 获取一周日期
     * @param $time 时间戳
     * @param $format 转换格式
     */
    public function getWeek($time, $format = "Y-m-d") {
        $week = date('w', $time);
        $weekname = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
        // 星期日排到末位
        if(empty($week)){
            $week = 7;
        }
        for ($i = 0; $i <= 6; $i++){
            $data[$i]['date'] = date($format, strtotime( '+' . $i + 1 - $week . ' days', $time));
            $data[$i]['week'] = $weekname[$i];
        }
        return $data;
    }

    // 搜索
    public function seach(){
        $uid = $_SESSION['user']['id'];
        $where = [];
        $kw = I('kw');
        if (!empty($kw)) {
            $where['title']=array('like','%'.$kw.'%');
        }

        $week = I('week');
        if (!empty($week)) {
            $startDate = strtotime($week);
            $endDate = strtotime(date('Y-m-d 23:59:59', strtotime($week)));
            $where['start_time'] = ['between', [$startDate, $endDate]];
        }

        $day = I('day/d');
        if (!empty($day)) {
            $week = empty($week) ? date('Y-m-d', time()) : $week;
            if ($day == 1) {
                $startDate = strtotime($week);
                $endDate = strtotime(date('Y-m-d 11:59:59', strtotime($week)));
                $where['start_time'] = ['between', [$startDate, $endDate]];
            } elseif ($day == 2) {
                $startDate = strtotime(date('Y-m-d 11:59:59', strtotime($week)));
                $endDate = strtotime(date('Y-m-d 17:59:59', strtotime($week)));
                $where['start_time'] = ['between', [$startDate, $endDate]];
            } else {
                $startDate = strtotime(date('Y-m-d 17:59:59', strtotime($week)));
                $endDate = strtotime(date('Y-m-d 23:59:59', strtotime($week)));
                $where['start_time'] = ['between', [$startDate, $endDate]];
            }
        }

        $types = I('types/d');
        if (!empty($types)) {
            if ($types == 1) {
                $where['type'] = ['eq', 1];
            } else {
                $where['type'] = ['in', [2, 3]];
            }
        }

        $where['is_sj'] = ['eq', 1];
        $huodong = M('huodong')->where($where)->order('pai desc')->select();
        foreach ($huodong as $key => $value) {
            $flag = 1; // 进行中
            if ($value['end_time'] < time()) {
                $flag = 3; // 已结束
            }

            $count = M('baoming')->where(['hid' => $value['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
            $count = $count ? $count : 0;
            if ($count && $count == $value['num']) {
                $flag = 2; // 已售罄
            }
            $sc = 0;
            if (M('shoucang')->where(['uid' => $uid, 'hid' => $value['id']])->find()) {
                $sc = 1;
            }
            $huodong[$key]['sc'] = $sc;
            $huodong[$key]['flag'] = $flag;
        }

        $this->huodong = arr_sort($huodong, 'flag');
        $this->display();
    }

    // 进行中
    public function start(){
        $uid = $_SESSION['user']['id'];
        $where['is_sj'] = ['eq', 1];
        $huodong = M('huodong')->where($where)->order('pai desc')->select();

        $datalist = [];
        foreach ($huodong as $key => $value) {
            $flag = 1; // 进行中
            if ($value['end_time'] < time()) {
                continue;
            }

            $count = M('baoming')->where(['hid' => $value['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
            $count = $count ? $count : 0;
            if ($count && $count == $value['num']) {
                $flag = 2; // 已售罄
            }
            $datalist[$key] = $value;
            $datalist[$key]['flag'] = $flag;
            $sc = 0;
            if (M('shoucang')->where(['uid' => $uid, 'hid' => $value['id']])->find()) {
                $sc = 1;
            }
            $datalist[$key]['sc'] = $sc;
        }
        $this->huodong = arr_sort($datalist, 'flag');
        $this->display();
    }

    // 已结束
    public function end(){
        $uid = $_SESSION['user']['id'];
        $where['is_sj'] = ['eq', 1];
        $huodong = M('huodong')->where($where)->order('pai desc')->select();
        $datalist = [];
        foreach ($huodong as $key => $value) {
            if ($value['end_time'] < time()) {
                $flag = 3; // 已结束
                $datalist[$key] = $value;
                $datalist[$key]['flag'] = $flag;
                $sc = 0;
                if (M('shoucang')->where(['uid' => $uid, 'hid' => $value['id']])->find()) {
                    $sc = 1;
                }
                $datalist[$key]['sc'] = $sc;
            }
        }
        $this->huodong = $datalist;
        $this->display();
    }

    // 活动详情
    public function det(){
        $id = I('get.id/d');
        empty($id) && die('参数错误！');

        $res = M('huodong')->where(['id' => $id])->find();
        $count = M('baoming')->where(['hid' => $res['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
        $count = $count ? $count : 0;
        $res['sy'] = $res['num'] - $count; // 剩余名额

        $flag = 1; // 进行中
        if ($res['end_time'] < time()) {
            $flag = 3; // 已结束
        }

        if ($count && $count == $res['num']) {
            $flag = 2; // 已售罄
        }
        $res['flag'] = $flag;

        $this->res = $res;

        // 微信JsApi参数
        $wxapi = new \WxPayApi();
        $timestamp = $this->getMillisecond();
        $nonceStr = $wxapi::getNonceStr();
        $this->jsApiArr = [
            'appId' => \WxPayConfig::APPID,
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'signature' => $this->getSignature($timestamp, $nonceStr)
        ];
        $this->shareImg = 'http://' . $_SERVER['HTTP_HOST'] . $res['photo'];

        $uid = $_SESSION['user']['id'];
        $user = M("user")->where("id={$uid}")->find();
        $this->huiyuan = $user['type'];
        $this->display();
    }

    // 收藏活动
    public function sc(){
        $hid = I('post.hid/d');
        empty($hid) && $this->ajaxReturn(['state' => 0, 'msg' => '参数错误！']);

        $db = M('shoucang');

        $map['hid'] = $hid;
        $map['uid'] = $_SESSION['user']['id'];
        $find = $db->where($map)->find();
        if (!$find) {
            $map['time'] = time();
            $res = $db->add($map);
            if ($res) {
                $this->ajaxReturn(['state' => 1, 'msg' => '收藏成功！']);
            } else {
                $this->ajaxReturn(['state' => 0, 'msg' => '请重试！']);
            }
        }else{
            $res = $db->where($map)->delete();
            if ($res) {
                $this->ajaxReturn(['state' => 1, 'msg' => '取消收藏成功！']);
            } else {
                $this->ajaxReturn(['state' => 0, 'msg' => '请重试！']);
            }
        }
    }

}